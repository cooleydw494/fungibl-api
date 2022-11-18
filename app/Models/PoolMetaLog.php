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
