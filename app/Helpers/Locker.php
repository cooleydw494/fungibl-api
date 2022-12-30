<?php

namespace App\Helpers;

use Auth;
use Carbon\Carbon;
use Closure;
use DB;
use Log;

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
        $exception = null;
        $position = 0;
        try {
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
                $minValue = DB::table('locks')
                              ->where('name', $name)
                              ->min('id');

                if ($minValue === $position) {
                    $callBackReturn = $callback();
                    $done = true;
                    break;
                }
                $tries++;
                sleep($interval);
            }
        } catch (\Exception $e) {
            $exception = $e;
        }
        DB::table('locks')->where('id', $position)->delete();
        if (! is_null($exception)) {
            throw $exception;
        }
        if (! $done) {
            throw new \Exception('Could not execute actions which require a lock handle');
        }
        return $callBackReturn;
    }
}
