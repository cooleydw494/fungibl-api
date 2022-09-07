<?php

namespace App\Models;

use App\Traits\IsNftRecord;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nft extends Model
{
    use HasFactory, IsNftRecord;

    protected $primaryKey = 'asset_id';
    public $incrementing = false;

    protected $guarded = [];

    /**
     * @param Nft|null   $nft
     * @param array|null $nftData
     * @return Nft
     * @throws Exception
     */
    public static function syncFromFrontend(
        ?Nft $nft = null,
        ?array $nftData = null
    ): static
    {
        // If neither are passed in, throw exception
        $passedNft = ! is_null($nft);
        $passedNftData = ! is_null($nftData);
        if (!$passedNft && !$passedNftData) {
            throw new Exception(
                'syncFromFrontend cannot be called without nftData or nft set'
            );
        }

        /** @var static|null $nft */
        $nft = $passedNft ? $nft : static::find($nftData['asset-id']);
        $newNft = is_null($nft);

        if ($newNft) {
            /** @var static $nft */
            $nft = static::create([
                'asset_id' => $nftData['asset-id'],
                'name' => $nftData['params']['name'],
                'unit_name' => $nftData['params']['unit-name'],
                'collection_name' => 'TODO:collection_name',
                'creator_wallet' => $nftData['params']['creator'],
                'meta_standard' => 'TODO:ms', // TODO
                'metadata' => 'TODO:metadata', // TODO
                'ipfs_image_url' => $nftData['imageUrl'],
                'image_cached' => false,
            ]);
        }

        $imageChange = !$newNft && $nft->ipfs_image_url !== $nftData['imageUrl'];
        $metadataChange = !$newNft && $nft->metadata !== 'TODO:metadata'; // TODO

        if ($metadataChange || $imageChange) {
            $needsCaching = ! ($nft->image_cached && !$imageChange);
            $nft->update([
                'metadata' => 'WIP',
                'ipfs_image_url' => $nftData['imageUrl'],
                'image_cached' => ! $needsCaching,
                'cache_tries' => $needsCaching ? 0 : $nft->cache_tries,
            ]);
        }

        return $nft;
    }

    /**
     * If no arguments passed, gets current estimated value in $ALGO
     *
     * If $previousEstimate is passed in check it represents a previous estimate
     * shared with the user. If that estimate has change more than $tolerance %
     * return false and handle outside this function
     *
     * @param int|null $previousEstimate
     * @param int|null   $tolerance
     * @return float|bool
     */
    public function estimateValue(
        ?int $previousEstimate = null,
        ?int $tolerance = 10
    ): int|bool
    {
        // TODO: replace below with actual current estimation logic
        $currentEstimate = $previousEstimate;
        if (is_null($previousEstimate)) {
            return $currentEstimate;
        }
        if (abs($previousEstimate/$currentEstimate) > $tolerance) {
            return false;
        }
        // We always round down on estimates/rewards, we like to keep things
        // neat and this defaults in favor of $FUN holders.
        return intval(floor($currentEstimate));
    }
}
