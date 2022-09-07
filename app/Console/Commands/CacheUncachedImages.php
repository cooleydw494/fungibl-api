<?php

namespace App\Console\Commands;

use App\Models\Nft;
use App\Models\PoolNft;
use Illuminate\Console\Command;

class CacheUncachedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:cache-uncached';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache any ipfs uncached images for NFTs.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $uncachedNfts = Nft::where('image_cached', false)
                           ->where('cache_tries', '<', 3)
                           ->get();
        $uncachedPoolNfts = PoolNft::where('image_cached', false)
                                   ->where('cache_tries', '<', 3)
                                   ->get();
        $uncachedPoolNftIds = $uncachedPoolNfts->pluck('asset_id')
                                               ->toArray();
        $poolNftsJustCached = [];

        $uncachedNfts->each(static function (Nft $nft)
        use ($uncachedPoolNftIds, &$poolNftsJustCached) {
            $result = $nft->cacheImage();
            if ($result && in_array($nft->asset_id, $uncachedPoolNftIds)) {
                $poolNftsJustCached[] = $nft->asset_id;
            }
        });
        if (count($poolNftsJustCached) > 0) {
            PoolNft::whereIn('asset_id', $poolNftsJustCached)
                   ->update(['image_cached' => true]);
        }

        $uncachedPoolNfts->each(static function (PoolNft $poolNft)
        use ($poolNftsJustCached) {
            if (! in_array($poolNft->asset_id, $poolNftsJustCached)) {
                $poolNft->cacheImage();
            }
        });

        return 0;
    }
}
