<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PoolMeta
 *
 * @property int $current_nft_count
 * @property int $current_pool_value
 * @property int $current_avg_reward
 * @property int $current_pull_cost
 * @property int $starting_nft_count
 * @property int $starting_pool_value
 * @property int $starting_avg_reward
 * @property int $starting_pull_cost
 * @property int $lowest_nft_count
 * @property int $lowest_pool_value
 * @property int $lowest_avg_reward
 * @property int $lowest_pull_cost
 * @property int $highest_nft_count
 * @property int $highest_pool_value
 * @property int $highest_avg_reward
 * @property int $highest_pull_cost
 * @property int $app_supply_fun
 * @property int $circulating_supply_fun
 * @property int $llc_supply_fun
 * @property int $beta_supply_fun
 * @property int $public_supply_fun
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta query()
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereAppSupplyFun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereBetaSupplyFun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereCirculatingSupplyFun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereCurrentAvgReward($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereCurrentNftCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereCurrentPoolValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereCurrentPullCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereHighestAvgReward($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereHighestNftCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereHighestPoolValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereHighestPullCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereLlcSupplyFun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereLowestAvgReward($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereLowestNftCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereLowestPoolValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereLowestPullCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta wherePublicSupplyFun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereStartingAvgReward($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereStartingNftCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereStartingPoolValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereStartingPullCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMeta whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PoolMeta extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static array $metaSets = [
        'funBalances' => ['app_supply_fun', 'llc_supply_fun', 'beta_supply_fun', 'public_supply_fun'],
        'current' => ['current_nft_count', 'current_pool_value', 'current_avg_reward', 'current_pull_cost'],
        'starting' => ['starting_nft_count', 'starting_pool_value', 'starting_avg_reward', 'starting_pull_cost'],
        'lowest' => ['lowest_nft_count', 'lowest_pool_value', 'lowest_avg_reward', 'lowest_pull_cost'],
        'highest' => ['highest_nft_count', 'highest_pool_value', 'highest_avg_reward', 'highest_pull_cost'],
        'all' => null, // null triggers * in SQL for static::get()
    ];

    protected static ?array $funBalances = null;
    protected static ?array $currentMetas = null;
    protected static ?array $startingMetas = null;
    protected static ?array $lowestMetas = null;
    protected static ?array $highestMetas = null;
    protected static ?array $allMetas = null;

    /**
     * @param array $metas
     * @return bool
     */
    public static function doUpdates(array $metas): bool
    {
        return static::query()->limit(1)->update($metas);
    }

    /**
     * @param array $metas
     * @return bool|null
     */
    public static function doIncrements(array $metas): ?bool
    {
        if (count($metas) === 0) return null;
        $increments = [];
        foreach ($metas as $key => $increment) {
            $increments[$key] = DB::raw("$key + ($increment)");
        }
        return static::query()->update($increments);
    }

    /**
     * Get keyed array of PoolMeta keys/values given $keys array
     *
     * @param array|null $keys - null gets all keys
     * @param bool|null  $fresh
     * @return array
     */
    public static function get(?array $keys = null, ?bool $fresh = true): array
    {
        if (! $fresh && is_null($keys) && ! is_null(static::$allMetas)) {
            return static::$allMetas;
        }
        $metas = (array)DB::table('pool_metas')
                          ->select($keys ?? '*')
                          ->get()
                          ->first();
        if (is_null($keys)) {
            static::$allMetas = $metas;
        }
        return $metas;
    }

    /**
     * @param string    $setName
     * @param bool|null $fresh
     * @return array
     */
    public static function getMetaSet(string $setName, ?bool $fresh = true): array
    {
        ! $fresh ?: static::$$setName = static::get(static::$metaSets[$setName]);
        return static::$$setName;
    }

    // Leaving this remnant in case I switch back to individual key/value records
//    /**
//     * Create or update existing metas with keys/values from $metas array
//     *
//     * NOTE: This is a mass update, so will not trigger Observers
//     *
//     * @param array $metas
//     * @return void
//     */
//    public static function do(array $metas): void
//    {
//        $keys = array_keys($metas);
//        $metas = array_values(array_map(static function (string $key, string $value) {
//            return ['key' => $key, 'value' => $value];
//        }, $keys, $metas));
//        static::upsert($metas, ['key'], ['value']);
//    }

//    /**
//     * Get keyed array of PoolMeta keys/values given $keys array
//     *
//     * @param array     $keys - null gets all keys
//     * @param bool|null $models
//     * @return array
//     */
//    public static function get(?array $keys = null, ?bool $models = false): array
//    {
//        $collection = static::whereIn('key', $keys)
//                            ->get()
//                            ->keyBy('key');
//        // Don't use toArray, it recursively converts the models themselves
//        if ($models) return (array)$collection;
//        return $collection->map(static function (PoolMeta $m) {
//            return $m->value;
//        })->toArray();
//    }
}
