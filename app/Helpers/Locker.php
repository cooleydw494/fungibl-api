<?php

namespace App\Helpers;

use Auth;
use Carbon\Carbon;
use Closure;
use DB;

class Locker {
    /**
     * @param string     $name
     * @param Closure    $callback
     * @param int|null   $seconds
     * @param float|null $interval
     * @return mixed
     */
    public static function doWithLock(string $name,
                                      Closure $callback,
                                      ?int $seconds = null,
                                      ?float $interval = null): mixed
    {
        $done = $callBackReturn = false;
        $userId = Auth::user()->id;
        $position = DB::table('locks')->insertGetId([
            'name' => $name,
            'user_id' => $userId,
            'created_at' => Carbon::now(),
        ]);
        $tries = 0;
        $seconds = $seconds ?? 15;
        $interval = $interval ?? .25;
        $maxTries = round($seconds / $interval);
        while ($tries <= $maxTries) {
            $minValue = DB::table('my_table')
                          ->where('name', $name)
                          ->min('id');

            if ($minValue === $position) {
                $callBackReturn = $callback();
                DB::table('my_table')
                  ->where('id', $minValue)
                  ->delete();
                $done = true;
                break;
            }
            $tries++;
            sleep($interval);
        }
        if (!$done) {
            throw new \Exception('Could not execute actions which require a lock handle');
        }
        return $callBackReturn;
    }
}
