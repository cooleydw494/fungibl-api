<?php

namespace App\Jobs;

use App\Helpers\Asalytic;
use App\Models\PoolNft;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshEstimate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $assetId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($assetId)
    {
        $this->assetId = $assetId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $poolNft = PoolNft::find($this->assetId);
        try {
            $estimate = Asalytic::estimatedPrice($poolNft->asset_id)
                    ->price_estimate ?? 0;
        } catch (GuzzleException $exception) {
            info('Error getting Asalytic estimated price for ASA ' . $this->assetId);
            info($exception->getMessage());
            info($exception->getTraceAsString());
            return;
        }
        $poolNft->updateQuietly(['current_estimated_price' => $estimate]);
    }
}
