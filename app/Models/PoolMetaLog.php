<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PoolMetaLog
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog query()
 * @mixin \Eloquent
 * @property string|null $last_action
 * @property int $id
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
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereAppSupplyFun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereBetaSupplyFun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereCirculatingSupplyFun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereCurrentAvgReward($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereCurrentNftCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereCurrentPoolValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereCurrentPullCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereHighestAvgReward($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereHighestNftCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereHighestPoolValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereHighestPullCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereLastAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereLlcSupplyFun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereLowestAvgReward($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereLowestNftCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereLowestPoolValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereLowestPullCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog wherePublicSupplyFun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereStartingAvgReward($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereStartingNftCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereStartingPoolValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereStartingPullCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PoolMetaLog whereUpdatedAt($value)
 */
class PoolMetaLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get Pull and Submit counts since $start, as well as Submit Rate
     *
     * @param Carbon $start
     * @return array
     */
    public static function getCounts(Carbon $start): array
    {
        $submitCount = PoolMetaLog::where('created_at', '>=', $start)
                                  ->where('last_action', 'submit')
                                  ->count();
        $pullCount = PoolMetaLog::where('created_at', '>=', $start)
                                ->where('last_action', 'pull')
                                ->count();
        if ($submitCount+$pullCount === 0) {
            $submitRate = 0;
        } else {
            $submitRate = round(($submitCount*100)/($submitCount+$pullCount), 2);
        }

        return [
            'submits' => $submitCount,
            'pulls' => $pullCount,
            'submitRate' => $submitRate,
        ];
    }

    /**
     * Take a duration string and get the start date (end date implicitly NOW)
     *
     * @param string $duration
     * @return Carbon
     */
    public static function getDurationStart(string $duration): Carbon
    {
        $start = Carbon::now();
        return match ($duration) {
            '30m' => $start->subMinutes(30),
            '1h' => $start->subHour(),
            '12h' => $start->subHours(12),
            '1d' => $start->subDays(2),
            '7d' => $start->subDays(8),
            '30d' => $start->subDays(31),
            default => $start->subDay(),
        };
    }
}
