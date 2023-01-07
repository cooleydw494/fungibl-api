<?php

namespace App\Http\Controllers;

use App\Exceptions\CriticalPoolException;
use App\Exceptions\EstimatedValueException;
use App\Exceptions\LockerException;
use App\Helpers\Asalytic;
use App\Helpers\Locker;
use App\Helpers\Oracle;
use App\Models\Nft;
use App\Models\PendingContract;
use App\Models\PoolNft;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Auth;

class NftController extends Controller
{
    /**
     * Use a specific NFT's data from the front-end to sync with the DB record
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function sync(Request $request): JsonResponse
    {
        $nftLookups = $request->input('nft_lookups');
        $nfts = [];
        $dontSync = [];
        $isProd = env('APP_ENV') === 'production';
        if ($isProd) {
            $nftIds = array_column($nftLookups, 'asset_id');
            $nftsData = Asalytic::getAsaInfo($nftIds);
        } else {
            $nftsData = $nftLookups;
        }
        foreach ($nftsData as $nftData) {
            if (!$isProd && is_null($nftData['mainnet_asset_id'] ?? null)) {
                $dontSync[] = $nftData['asset_id'];
                continue; // These don't exist. "real" testnet nfts only!
            }
            try {
                $nft = $isProd
                    ? Nft::syncFromAsalytic($nftData)
                    : Nft::syncFromAlgod($nftData);
                $nft->append('fake_mainnet_data');
                $nfts[] = $nft;
                ($nft->image_cached && $nft->cache_tries < 3)
                    ?: $needsCaching[] = $nft->asset_id;
            } catch (Exception $exception) {
                info($exception->getMessage());
                info($exception->getTraceAsString());
            }
        }
        return response()->json([
            'success' => ':)', 'needs_caching' => $needsCaching ?? [], 'nfts' => $nfts, 'dont_sync' => $dontSync,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function createSubmitContract(Request $request): JsonResponse
    {
        $user = Auth::user();
        $nftAssetId = $request->input('nft_asset_id');
        $existing = PendingContract::where('user_id', $user->id)
                           ->where('nft_asset_id', $nftAssetId)
                           ->first();
        if ($existing) {
            $contractInfo = $existing->contract_info;
        } else {
            try {
                $submitterAddress = $user->algorand_address;
                $response = Oracle::createSubmitContract($nftAssetId, $submitterAddress);
                $contractInfo = $response->ctc_info ?? null;
                if (is_null($contractInfo)) {
                    $response = json_encode($response);
                    throw new Exception("no contract info retrieved from Oracle. Response: $response");
                }
                $contractInfo = json_encode($contractInfo);
                PendingContract::create([
                    'contract_info' => $contractInfo,
                    'user_id' => $user->id,
                    'nft_asset_id' => $nftAssetId,
                ]);
            } catch (\Exception $exception) {
                info($exception->getMessage());
                info($exception->getTraceAsString());
                return response()->json(['error' => $exception->getMessage(),]);
            }
        }
        return response()->json([
            'success' => 'created Submit contract',
            'ctc_info' => $contractInfo,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function  addToPool(Request $request): JsonResponse
    {
        $userId = Auth::user()->id;
        $finalizedReward = 0;
        foreach ($request->nfts as $nftData) {
            try {
                // Only source the contract data we know WE created
                $pendingContract = PendingContract::where('nft_asset_id', $nftData['asset_id'])
                                                  ->where('user_id', $userId)
                                                  ->first();
                if (is_null($pendingContract)) {
                    throw new Exception("No pending contract for ASA {$nftData['asset_id']}, user $userId");
                }
                $nftData['contract_info'] = $pendingContract->contract_info;
                $poolNft = PoolNft::addToPool($nftData);
                if (is_a($poolNft, PoolNft::class)) {
                    $pendingContract->delete();
                }
                $finalizedReward += $poolNft->submit_reward_fun;
                $poolNfts[] = $poolNft;
            } catch (Exception $exception) {
                $exceptions[] = $exception;
                info($exception->getMessage());
                info($exception->getTraceAsString());
            }
        }
        return response()->json([
            'finalized_reward' => $finalizedReward,
            'pool_nfts' => $poolNfts ?? null,
            'exceptions' => $exceptions ?? null,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function randomContractInfo(Request $request): JsonResponse
    {
        try {
            $poolNft = Locker::doWithLock('pool', static function () use ($request) {
                /** @var PoolNft $poolNft */
                $poolNft = PoolNft::inRandomOrder()->first();
                if (is_null($poolNft)) {
                    throw new CriticalPoolException('No NFTs in the pool (this is bad).');
                }
                $poolNft->markPulled();
                $successful = Oracle::setPullerDetails($poolNft);
                return $poolNft;
            });
        } catch (CriticalPoolException $exception) {
            // TODO: send a critical alert
            return response()->json(['error' => $exception->getMessage()]);
        } catch (LockerException $exception) {
            return response()->json(['error' => $exception->getMessage()]);
        }

        if (env('APP_ENV') !== 'production') {
            $poolNft->append('fake_mainnet_data');
        }

        return response()->json([
            'success' => ':)',
            'nft' => $poolNft,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function featuredNftInfo(Request $request): JsonResponse
    {
        /** @var PoolNft $poolNft */
        $poolNft = PoolNft::inRandomOrder()->first();
        $name = $poolNft->fake_mainnet_data['unit_name'] ?? $poolNft->unit_name;

        return response()->json([
            'success' => 'Retrieved featured pool nft info',
            'asset_id' => $poolNft->asset_id,
            'fake_asset_id' => $poolNft->fake_mainnet_data['asset_id'],
            'name' => $name,
        ]);
    }

    public function getEstimate(Request $request, Nft $nft): JsonResponse
    {
        try {
            $estimate = $nft->estimateValue();
        } catch (EstimatedValueException $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
        return response()->json(['success' => 'Estimated value calculated', 'estimated_value' => $estimate]);
    }

    // TODO: will probably need to split this off when implementing a secure way of pulling
//    /**
//     * @param Request $request
//     * @param int     $assetId
//     * @return JsonResponse
//     */
//    public function markPulled(Request $request, int $assetId): JsonResponse
//    {
//        $poolNft = PoolNft::findOrFail($assetId);
//        $poolNft->markPulled();
//        return response()->json(['success' => ':)']);
//    }

    /**
     * @param Request $request
     * @param int     $assetId
     * @return JsonResponse
     */
    public function cacheImage(Request $request, int $assetId): JsonResponse
    {
        $nft = Nft::findOrFail($assetId);
        $cached = $nft->cacheImage();
        return response()->json(['cache_result' => $cached]);
    }
}
