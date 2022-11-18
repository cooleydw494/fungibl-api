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
        dispatch(new SavePoolMetaLog($poolMeta->get([
            'last_action',
            'current_nft_count',
            'current_pool_value',
            'current_avg_reward',
            'current_pull_cost',

            'starting_nft_count',
            'starting_pool_value',
            'starting_avg_reward',
            'starting_pull_cost',

            'lowest_nft_count',
            'lowest_pool_value',
//            'lowest_nft_value',
            'lowest_avg_reward',
            'lowest_pull_cost',

            'highest_nft_count',
            'highest_pool_value',
//            'highest_nft_value',
            'highest_avg_reward',
            'highest_pull_cost',

            'app_supply_fun',
            'circulating_supply_fun',
            'llc_supply_fun',
            'beta_supply_fun',
            'public_supply_fun',
        ])));
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
