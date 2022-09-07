<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
