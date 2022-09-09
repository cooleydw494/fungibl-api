<?php

namespace App\Models;

use App\Traits\IsNftRecord;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Algorand;
use Rootsoft\Algorand\Models\Accounts\Address;

class PoolNft extends Model
{
    use HasFactory, IsNftRecord;

    protected $guarded = [];

    protected $primaryKey = 'asset_id';
    public $incrementing = false;

    /**
     * Add an NFT to the pool_nfts table to represent its presence in the
     * Fungibl App NFT pool. First make sure the NFT is synced and updated in
     * the nfts table (this is agnostic of pool_nfts and efficient duplication)
     *
     * @param array $nftData
     * @return PoolNft
     * @throws Exception
     */
    public static function addToPool(array $nftData): PoolNft
    {
        $nft = Nft::syncFromFrontend(null, $nftData);
        /** @var User $user */
        $user = auth()->user();
        $tolerance = 10; // TODO: pass this in $nftData->tolerance from user
        // TODO: remove this, it is ONLY for minting testnet seeded NFT pool
        $frontendEstimate = $nftData['estimated_value'];
        $estimatedAlgo = $nft->estimateValue($frontendEstimate, $tolerance);
        $submitReward = 0; //static::calculateReward($estimatedAlgo);// TODO: replace after seeding
        $poolNft = PoolNft::create([
            ...$nft->only([
                'asset_id', 'name', 'creator_wallet', 'unit_name', 'collection_name',
                'ipfs_image_url', 'image_cached', 'meta_standard', 'metadata',
            ]),
            'in_pool' => true,
            'current_est_algo' => $estimatedAlgo,
            'submit_est_algo' => $estimatedAlgo,
            'submit_reward_fun' => $submitReward,
            'submit_algorand_address' => $user->algorand_address,
            'contract_info' => $nftData['contract_info'],
        ]);

//        Algorand::assetManager()->transfer(
//            env('FUN_ASSET_ID'),
//            Algorand::accountManager()->restoreAccount(json_decode(env('SEED'))),
//            $submitReward,
//            Address::fromAlgorandAddress($user->algorand_address),
//        );

        return $poolNft;
    }

    /**
     * @return void
     */
    public function markPulled(): void
    {
        $this->update([
            'in_pool' => false,
            'pull_est_algo' => $this->submit_est_algo,
            'pull_cost_fun' => self::calculatePullCost(),
            'pulled_at' => Carbon::now(),
        ]);
        $this->delete();
    }

    /**
     * @param int      $estimatedAlgo
     * @param int|null $appSupplyFun
     * @param int|null $currentPoolValue
     * @return int
     */
    public static function calculateReward(int $estimatedAlgo,
                                           ?int $appSupplyFun = null,
                                           ?int $currentPoolValue = null): int
    {
        if (is_null($appSupplyFun) || is_null($currentPoolValue)) {
            $m = PoolMeta::get();
        }
        $appSupplyFun = $appSupplyFun ?? $m['app_supply_fun'];
        $currentPoolValue = $currentPoolValue ?? $m['current_pool_value'];
        $reward = $appSupplyFun
            * ($estimatedAlgo / ($currentPoolValue + $estimatedAlgo));
        // We always round down on estimates/rewards, we like to keep things
        // neat and this defaults in favor of $FUN holders.
        return intval(floor($reward));
    }

    /**
     * @param int|null $circulatingSupply
     * @param int|null $poolCount
     * @return int
     */
    public static function calculatePullCost(?int $circulatingSupply = null,
                                             ?int $poolCount = null): int
    {
        if (is_null($circulatingSupply) || is_null($poolCount)) {
            $c = PoolMeta::get();
        }
        $circulatingSupply = $circulatingSupply ?? $c['public_supply_fun'];
        $poolCount = $poolCount ?? $c['current_pool_count'];
        // Cost always rounds up to default in bias of pool solvency
        return intval(ceil($circulatingSupply / $poolCount));
    }
}
