<?php

namespace App\Models;

use App\Helpers\Locker;
use App\Traits\IsNftRecord;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Algorand;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rootsoft\Algorand\Models\Accounts\Address;
use DB;

/**
 * App\Models\PoolNft
 *
 * @property int $asset_id
 * @property string $name
 * @property string $unit_name
 * @property string $collection_name
 * @property string $creator_wallet
 * @property string $meta_standard
 * @property string|null $metadata
 * @property string $ipfs_image_url
 * @property int|null $image_cached
 * @property int $in_pool
 * @property int $current_est_algo
 * @property int $submit_est_algo
 * @property int $submit_reward_fun
 * @property string $submit_algorand_address
 * @property int $submit_iteration
 * @property string $contract_info
 * @property int|null $pull_est_algo
 * @property int|null $pull_cost_fun
 * @property string|null $pull_algorand_address
 * @property string|null $pulled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft newQuery()
 * @method static \Illuminate\Database\Query\Builder|PoolNft onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft query()
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereCollectionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereContractInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereCreatorWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereCurrentEstAlgo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereImageCached($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereInPool($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereIpfsImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereMetaStandard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft wherePullAlgorandAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft wherePullCostFun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft wherePullEstAlgo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft wherePulledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereSubmitAlgorandAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereSubmitEstAlgo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereSubmitIteration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereSubmitRewardFun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereUnitName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolNft whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PoolNft withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PoolNft withoutTrashed()
 * @mixin \Eloquent
 */
class PoolNft extends Model
{
    use HasFactory, IsNftRecord, SoftDeletes;

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
        $user = Auth::user();
        $tolerance = 10; // TODO: pass this in $nftData->tolerance from user
        // TODO: remove this, it is ONLY for minting testnet seeded NFT pool
        $frontendEstimate = $nftData['estimated_value'];
        $estimatedAlgo = $nft->estimateValue($frontendEstimate, $tolerance);

        $poolNft = Locker::doWithLock('pool',
            static function () use ($estimatedAlgo, $nft, $nftData, $user) {
            $submitReward = static::calculateReward($estimatedAlgo);
            $submitIteration = (DB::table('pool_nfts')
                                  ->select('submit_iteration')
                                  ->where('asset_id', $nft->asset_id)
                                  ->latest()
                                  ->first()->submit_iteration ?? 0) + 1;
            return PoolNft::create([
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
                'submit_iteration' => $submitIteration,
            ]);
        });

        Algorand::assetManager()->transfer(
            env('FUN_ASSET_ID'),
            Algorand::accountManager()->restoreAccount(json_decode(env('SEED'))),
            $poolNft->submit_reward_fun,
            Address::fromAlgorandAddress($user->algorand_address),
        );

        return $poolNft;
    }

    /**
     * @return void
     */
    public function markPulled(): void
    {
        info(self::calculatePullCost());
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
        info($circulatingSupply);
        info($poolCount);
        if (is_null($circulatingSupply) || is_null($poolCount)) {
            $c = PoolMeta::get();
        }
        $circulatingSupply = $circulatingSupply ?? $c['circulating_supply_fun'];
        $poolCount = $poolCount ?? $c['current_nft_count'];
        info($circulatingSupply);
        info($poolCount);
        // Cost always rounds up to default in bias of pool solvency
        return intval(ceil($circulatingSupply / $poolCount));
    }
}
