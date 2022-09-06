<?php

namespace App\Models;

use App\Traits\IsNftRecord;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoolNft extends Model
{
    use HasFactory, IsNftRecord;

    protected $guarded = [];

    /**
     * Add an NFT to the pool_nfts table to represent its presence in the
     * Fungibl App NFT pool. First make sure the NFT is synced and updated in
     * the nfts table (this is agnostic of pool_nfts and efficient duplication)
     *
     * @param array $nftData
     * @return PoolNft
     * @throws Exception
     */
    public static function addToPool(array $nftData): PoolNft
    {
        $nft = Nft::syncFromFrontend(null, $nftData);
        /** @var User $user */
        $user = auth()->user();
        $tolerance = 10; // TODO: pass this in $nftData->tolerance from user
        // TODO: remove this, it is ONLY for minting testnet seeded NFT pool
        $nftDataEstimate = rand(10, 250);
        $estimatedAlgo = $nft->estimateValue($nftDataEstimate, $tolerance);
        return PoolNft::create([
            ...$nft->only(['asset_id', 'name', 'unit_name', 'collection_name', 'image_cached']),
            'in_pool' => true,
            'submit_est_algo' => $estimatedAlgo,
            'submit_algorand_address' => $user->algorand_address,
        ]);
    }
}
