<?php

namespace App\Models;

use App\Helpers\Asalytic;
use App\Traits\IsNftRecord;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Nft
 *
 * @property int $asset_id
 * @property string $name
 * @property string $unit_name
 * @property string $collection_name
 * @property string $creator_wallet
 * @property string $meta_standard
 * @property string|null $metadata
 * @property string $ipfs_image_url
 * @property int|null $image_cached
 * @property int $cache_tries
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Nft newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Nft newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Nft query()
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereCacheTries($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereCollectionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereCreatorWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereImageCached($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereIpfsImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereMetaStandard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereUnitName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        $currentEstimate = $previousEstimate; // TODO: replace with below when ready
//        try {
//            $currentEstimate = Asalytic::estimatedPrice($this->asset_id)
//                    ->price_estimate ?? 0;
//        } catch (GuzzleException $exception) {
//            info('Guzzle Exception in estimateValue: ' . $exception->getMessage());
//            info($exception->getTraceAsString());
//            $currentEstimate = $previousEstimate;
//        }
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
