<?php

namespace App\Observers;

use App\Jobs\SavePoolMetaLog;
use App\Models\PoolMeta;

class PoolMetaObserver
{
    /**
     * Handle the PoolMeta "created" event.
     *
     * @param PoolMeta $poolMeta
     * @return void
     */
    public function created(PoolMeta $poolMeta): void
    {
        //
    }

    /**
     * Handle the PoolMeta "updated" event.
     *
     * @param PoolMeta $poolMeta
     * @return void
     */
    public function updated(PoolMeta $poolMeta): void
    {
        dispatch(new SavePoolMetaLog($poolMeta->getAttributes()));
    }

    /**
     * Handle the PoolMeta "deleted" event.
     *
     * @param PoolMeta $poolMeta
     * @return void
     */
    public function deleted(PoolMeta $poolMeta): void
    {
        //
    }

    /**
     * Handle the PoolMeta "restored" event.
     *
     * @param PoolMeta $poolMeta
     * @return void
     */
    public function restored(PoolMeta $poolMeta): void
    {
        //
    }

    /**
     * Handle the PoolMeta "force deleted" event.
     *
     * @param PoolMeta $poolMeta
     * @return void
     */
    public function forceDeleted(PoolMeta $poolMeta): void
    {
        //
    }
}
