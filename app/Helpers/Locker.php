<?php

namespace App\Helpers;

use App\Exceptions\LockerException;
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
     * @throws LockerException
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
            DB::table('locks')->where('id', $position)->delete();
            throw new LockerException('Exception while executing locked operation', $e->getCode(), $e);
        }
        DB::table('locks')->where('id', $position)->delete();
        if (! $done) {
            throw new LockerException('Could not execute actions which require a lock handle');
        }
        return $callBackReturn;
    }
}
