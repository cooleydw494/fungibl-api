<?php

namespace App\Http\Controllers;

use App\Models\Nft;
use App\Models\PoolNft;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        foreach ($request->nfts as $nftData) {
            try {
                $nft = Nft::syncFromFrontend(null, $nftData);
                ($nft->image_cached && $nft->cache_tries < 3)
                    ?: $needsCaching[] = $nft->asset_id;
            } catch (Exception $exception) {
                info($exception->getMessage());
                info($exception->getTraceAsString());
            }
        }
        $addToPoolResponse = $this->addToPool($request);
        return response()->json([
            'success' => ':)', 'needs_caching' => $needsCaching ?? [],
            'add_to_pool_response' => $addToPoolResponse,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addToPool(Request $request): JsonResponse
    {
        foreach ($request->nfts as $nftData) {
            try {
                $poolNfts[] = PoolNft::addToPool($nftData);
            } catch (Exception $exception) {
                $exceptions[] = $exception;
                info($exception->getMessage());
                info($exception->getTraceAsString());
            }
        }
        return response()->json([
            'pool_nfts' => $poolNfts ?? null,
            'exceptions' => $exceptions ?? null,
        ]);
    }

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
