<?php

namespace App\Observers;

use App\Models\PoolMeta;
use App\Models\PoolNft;

class PoolNftObserver
{
    /**
     * Handle the PoolNft "created" event.
     *
     * This handler assumes the NFT is IN THE POOL currently
     *
     * NOTE: $increments code has been commented out because I plan to use a
     * locking mechanism for all submit/pull logic and given that its more
     * efficient to update all at once (and it isn't all increments).
     * Beyond that, a singular update also means I can use a PoolMetaObserver
     * which wasn't an option before.
     *
     * Leaving the $increments code for now though...
     *
     * @param PoolNft $poolNft
     * @return void
     */
    public function created(PoolNft $poolNft): void
    {
        /** @var array $c - Current PoolMeta keys/values */
        $c = PoolMeta::get();
//        $increments = [];
        $updates = [];

        $newNftCount = $c['current_nft_count'] + 1;
        $updates['current_nft_count'] = $newNftCount;
//        $increments['current_nft_count'] = 1;

        if ($newNftCount > $c['highest_nft_count']) {
            $updates['highest_nft_count'] = $newNftCount;
        }
        if ($newNftCount < $c['lowest_nft_count']) {
            $updates['lowest_nft_count'] = $newNftCount;
        }

        $newPoolValue = $c['current_pool_value'] + $poolNft->submit_est_algo;
        $updates['current_pool_value'] = $newPoolValue;
//        $increments['current_pool_value'] = $poolNft->submit_est_algo;
        if ($newPoolValue > $c['highest_pool_value']) {
            $updates['highest_pool_value'] = $newPoolValue;
        }

//        $increments['app_supply_fun'] = -($poolNft->submit_reward_fun);
//        $increments['public_supply_fun'] = $poolNft->submit_reward_fun;
        $newAppSupply = $c['app_supply_fun'] - $poolNft->submit_reward_fun;
        $newPublicSupply = $c['public_supply_fun'] + $poolNft->submit_reward_fun;
        $newCirculatingSupply = $c['circulating_supply_fun'] + $poolNft->submit_reward_fun;
        $newPullCost = PoolNft::calculatePullCost($newCirculatingSupply, $newNftCount);
        $newAvgNftVal = $newPoolValue / $newNftCount;
        $newAvgReward = PoolNft::calculateReward($newAvgNftVal);
        $updates['app_supply_fun'] = $newAppSupply;
        $updates['public_supply_fun'] = $newPublicSupply;
        $updates['circulating_supply_fun'] = $newCirculatingSupply;
        $updates['current_pull_cost'] = $newPullCost;
        $updates['current_avg_reward'] = $newAvgReward;

        if ($c['highest_pull_cost'] === 0 || $newPullCost > $c['highest_pull_cost']) {
            $updates['highest_pull_cost'] = $newPullCost;
        } elseif ($c['lowest_pull_cost'] === 0 || $newPullCost < $c['lowest_pull_cost']) {
            $updates['lowest_pull_cost'] = $newPullCost;
        }

        if ($c['highest_avg_reward'] === 0 || $newAvgReward > $c['highest_avg_reward']) {
            $updates['highest_avg_reward'] = $newAvgReward;
        } elseif ($c['lowest_avg_reward'] === 0 || $newAvgReward < $c['lowest_avg_reward']) {
            $updates['lowest_avg_reward'] = $newAvgReward;
        }

        PoolMeta::doUpdates($updates);
//        PoolMeta::doIncrements($increments);
    }

    /**
     * Handle the PoolNft "updated" event.
     *
     * @param PoolNft $poolNft
     * @return void
     */
    public function updated(PoolNft $poolNft): void
    {
        //
    }

    /**
     * Handle the PoolNft "deleted" event.
     *
     * This is for soft-deletes given PoolNfts use soft-deletes
     *
     * @param PoolNft $poolNft
     * @return void
     */
    public function deleted(PoolNft $poolNft)
    {
        /** @var array $c - Current PoolMeta keys/values */
        $c = PoolMeta::get();
        $updates = [];

        $newNftCount = $c['current_nft_count'] - 1;
        $updates['current_nft_count'] = $newNftCount;

        if ($newNftCount > $c['highest_nft_count']) {
            $updates['highest_nft_count'] = $newNftCount;
        }
        if ($newNftCount < $c['lowest_nft_count']) {
            $updates['lowest_nft_count'] = $newNftCount;
        }

        $newPoolValue = $c['current_pool_value'] - $poolNft->current_est_algo;
        $updates['current_pool_value'] = $newPoolValue;
        if ($newPoolValue < $c['lowest_pool_value']) {
            $updates['lowest_pool_value'] = $newPoolValue;
        }

        $newAppSupply = $c['app_supply_fun'] + $poolNft->pull_cost_fun;
        $newPublicSupply = $c['public_supply_fun'] - $poolNft->pull_cost_fun;
        $newCirculatingSupply = $c['circulating_supply_fun'] - $poolNft->pull_cost_fun;
        $newPullCost = PoolNft::calculatePullCost($newCirculatingSupply, $newNftCount);
        $newAvgNftVal = $newPoolValue / $newNftCount;
        $newAvgReward = PoolNft::calculateReward($newAvgNftVal);
        $updates['app_supply_fun'] = $newAppSupply;
        $updates['public_supply_fun'] = $newPublicSupply;
        $updates['circulating_supply_fun'] = $newCirculatingSupply;
        $updates['current_pull_cost'] = $newPullCost;
        $updates['current_avg_reward'] = $newAvgReward;

        if ($c['highest_pull_cost'] === 0 || $newPullCost > $c['highest_pull_cost']) {
            $updates['highest_pull_cost'] = $newPullCost;
        } elseif ($c['lowest_pull_cost'] === 0 || $newPullCost < $c['lowest_pull_cost']) {
            $updates['lowest_pull_cost'] = $newPullCost;
        }

        if ($c['highest_avg_reward'] === 0 || $newAvgReward > $c['highest_avg_reward']) {
            $updates['highest_avg_reward'] = $newAvgReward;
        } elseif ($c['lowest_avg_reward'] === 0 || $newAvgReward < $c['lowest_avg_reward']) {
            $updates['lowest_avg_reward'] = $newAvgReward;
        }

        PoolMeta::doUpdates($updates);
    }

    /**
     * Handle the PoolNft "restored" event.
     *
     * @param PoolNft $poolNft
     * @return void
     */
    public function restored(PoolNft $poolNft)
    {
        //
    }

    /**
     * Handle the PoolNft "force deleted" event.
     *
     * @param PoolNft $poolNft
     * @return void
     */
    public function forceDeleted(PoolNft $poolNft)
    {
        //
    }
}