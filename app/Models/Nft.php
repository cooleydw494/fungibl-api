<?php

namespace App\Models;

use App\Traits\IsNftRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nft extends Model
{
    use HasFactory, IsNftRecord;

    protected $primaryKey = 'asset_id';

    protected $guarded = [];

    /**
     * @param object $nftData
     * @return Nft
     */
    public static function syncFromFrontend(object $nftData): static
    {
        /** @var static|null $nft */
        $nft = static::find($nftData['asset-id']);
        $newNft = is_null($nft);

        if ($newNft) {
            /** @var static $nft */
            $nft = static::create([
                'asset_id' => $nftData['asset-id'],
                'name' => $nftData->params['name'],
                'unit_name' => $nftData->params['unit-name'],
                'collection_name' => 'TODO:collection_name',
                'creator_wallet' => $nftData->params['creator'],
                'meta_standard' => 'TODO:ms', // TODO
                'metadata' => 'TODO:metadata', // TODO
                'ipfs_image_url' => $nftData->imageUrl,
            ]);
        }

        $imageChange = !$newNft && $nft->ipfs_image_url !== $nftData->imageUrl;
        $metadataChange = !$newNft && $nft->metadata !== 'TODO:metadata'; // TODO

        if ($metadataChange || $imageChange) {
            $nft->update([
                'metadata' => 'WIP',
                'ipfs_image_url' => $nftData->imageUrl,
                'image_cached' => ($nft->image_cached && !$imageChange),
            ]);
        }

        return $nft;
    }
}
