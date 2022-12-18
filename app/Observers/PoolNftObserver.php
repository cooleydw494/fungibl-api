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
        static::nftSubmitted($poolNft);
    }

    /**
     * Handle the PoolNft "updated" event.
     *
     * @param PoolNft $poolNft
     * @return void
     */
    public function updated(PoolNft $poolNft): void
    {
        if ($poolNft->isDirty('current_est_algo')) {
            static::estAlgoUpdated($poolNft);
        }
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
        $c = PoolMeta::get(); // Current PoolMeta keys/values
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

        $updates['last_action'] = 'pull';

        PoolMeta::doUpdates($updates);
    }

    /**
     * Update all the things that need updating when the NFT for submission has
     * been secured in the Smart Contract
     *
     * @param PoolNft $poolNft
     * @return void
     */
    public function nftSubmitted(PoolNft $poolNft): void
    {
        $c = PoolMeta::get(); // Current PoolMeta keys/values
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

        $updates['last_action'] = 'submit';

        PoolMeta::doUpdates($updates);
//        PoolMeta::doIncrements($increments);
    }

    /**
     * @param PoolNft $poolNft
     * @return void
     */
    public static function estAlgoUpdated(PoolNft $poolNft): void
    {
        $oldEstAlgo = $poolNft->getOriginal('current_est_algo');
        $estAlgoDelta = $poolNft->current_est_algo - $oldEstAlgo;
        PoolMeta::doIncrements(['current_pool_value' => $estAlgoDelta]);
        // TODO: maybe figure out how to safely update the avg reward here
        // but consider that this is done in large batches one by one
    }

    //<editor-fold desc="Restored Event...">
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
    //</editor-fold>

    //<editor-fold desc="Force Deleted Event...">
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
    //</editor-fold>
}
