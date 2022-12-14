<?php

namespace App\Models;

use App\Exceptions\EstimatedValueException;
use App\Exceptions\Handler;
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
 * @property string $creator_name
 * @property string $reserve_wallet
 * @property int $total_supply
 * @property int $rarity_rank
 * @property-read array|null $fake_mainnet_data
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereCreatorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereRarityRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereReserveWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nft whereTotalSupply($value)
 */
class Nft extends Model
{
    use HasFactory, IsNftRecord;

    protected $primaryKey = 'asset_id';
    public $incrementing = false;

    protected $guarded = [];

    /**
     * Use data from Algod to populate testnet NFTs (expects faker metadata)
     *
     * @param array|null $nftData
     * @return Nft
     * @throws Exception
     */
    public static function syncFromAlgod(array $nftData): static
    {
        /** @var static|null $nft */
        $nft = static::find($nftData['asset_id']);
        $newNft = is_null($nft);
        if ($newNft) {
            /** @var static $nft */
            $collectionName = preg_replace('/[0-9]/', '', $nftData['properties']['mainnet_unit_name'] ?? 'Nunyaz');
//            info(json_encode([
//                'asset_id' => $nftData['asset_id'],
//                'unit_name' => $nftData['params']['unit-name'],
//                'name' => $nftData['params']['name'],
//                'collection_name' => $collectionName,
//                'creator_name' => 'Imposter',
//                'creator_wallet' => $nftData['params']['creator'],
//                'reserve_wallet' => $nftData['params']['reserve'],
//                'meta_standard' => $nftData['metadata_standard'],
//                'metadata' => $nftData['metadata'],
//                'total_supply' => 420,
//                'rarity_rank' => 69,
//                'ipfs_image_url' => $nftData['imageUrl'],
//                'image_cached' => false,
//            ]));
            $nft = static::create([
                'asset_id' => $nftData['asset_id'],
                'unit_name' => $nftData['params']['unit-name'],
                'name' => $nftData['params']['name'],
                'collection_name' => $collectionName,
                'creator_name' => 'Imposter',
                'creator_wallet' => $nftData['params']['creator'],
                'reserve_wallet' => $nftData['params']['reserve'],
                'meta_standard' => $nftData['metadata_standard'],
                'metadata' => json_encode($nftData['metadata']),
                'total_supply' => 420,
                'rarity_rank' => 69,
                'ipfs_image_url' => $nftData['imageUrl'],
                'image_cached' => false,
            ]);
        }
        // We don't need to handle any updating for fake NFTs
        return $nft;
    }

    /**
     * @param Nft|null   $nft
     * @param array|null $nftData
     * @return Nft
     * @throws Exception
     */
    public static function syncFromAsalytic(object $nftData): static
    {
        /** @var static|null $nft */
        $nft = static::find($nftData->asa_id);
        $newNft = is_null($nft);

        if ($newNft) {
            $metaStandard = 'none';
            if (! is_null($nftData->arc3_metadata))
                $metaStandard = 'arc3';
            if (! is_null($nftData->arc69_metadata)) {
                $metaStandard = 'arc69';
            }
            if (! is_null($nftData->arc19_metadata)) {
                $metaStandard = 'arc19';
            }
            /** @var static $nft */
            $nft = static::create([
                'asset_id' => $nftData->asa_id,
                'unit_name' => $nftData->unit_name,
                'name' => $nftData->name,
                'collection_name' => $nftData->collection_id,
                'creator_name' => $nftData->creator_id,
                'creator_wallet' => $nftData->creator,
                'reserve_wallet' => $nftData->reserve,
                'meta_standard' => $metaStandard,
                'metadata' => $nftData->metadata,
                'total_supply' => $nftData->rarity->total ?? null,
                'rarity_rank' => $nftData->rarity->rank ?? null,
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
     * @throws EstimatedValueException
     */
    public function estimateValue(
        ?int $previousEstimate = null,
        ?int $tolerance = 10
    ): int|bool
    {
        $assetId = $this->asset_id;
        if (env('APP_ENV') !== 'production') {
            $assetId = $this->fake_mainnet_data['asset_id'] ?? $this->asset_id;
        }
        try {
            $currentEstimate = Asalytic::estimatedPrice($assetId)
                ->estimated_price ?? 0;
        } catch (GuzzleException $exception) {
            $message = 'There was an error communicating with Asalytic';
            throw new EstimatedValueException($message, $exception->getCode(), $exception);
        }
        if ($currentEstimate < 5) {
            throw new EstimatedValueException('NFT estimated value too low');
        }
        if (! is_null($previousEstimate) && abs($previousEstimate/$currentEstimate) > $tolerance) {
            throw new EstimatedValueException("Estimate delta is higher than $tolerance%");
        }
        // We always round down on estimates/rewards, we like to keep things
        // neat and this defaults in favor of $FUN holders.
        return intval(floor($currentEstimate));
    }
}
