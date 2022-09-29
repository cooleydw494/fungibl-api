<?php

namespace App\Console\Commands;

use App\Jobs\RefreshEstimate;
use App\Models\PoolNft;
use Illuminate\Console\Command;

class RefreshEstimates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'estimates:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the Asalytic estimates for all synced NFTs';

    /**
     * Queue every Pool NFT to refresh estimates
     */
    public function handle()
    {
        PoolNft::all()->each(static function(PoolNft $poolNft) {
            dispatch(new RefreshEstimate($poolNft->asset_id));
        });
    }
}
