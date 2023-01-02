<?php

namespace App\Traits;

use App\Models\Nft;
use App\Models\PoolNft;
use Image;
use Intervention\Image\Exception\NotReadableException;
use Storage;

trait IsNftRecord {

    /**
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'asset_id';
    }

    /**
     * @return array|null
     */
    public function getFakeMainnetDataAttribute(): ?array
    {
        if (env('APP_ENV') === 'production') {
            return null;
        }
        $metadata = json_decode($this->metadata);
        if (is_null($metadata->properties['mainnet_asset_id'] ?? null)) {
            return null;
        }
        return [
            'asset_id' => $metadata->properties['mainnet_asset_id'],
            'unit_name' => $metadata->properties['mainnet_unit_name'],
            'asset_name' => $metadata->properties['mainnet_asset_name'],
        ];
    }

    /**
     * @return false|string
     */
    public function cacheImage(): bool|string
    {
        $this->update(['image_cached' => null]); // null === unresolved attempt
        try {
            $image = Image::make($this->ipfs_image_url);
            $image->encode('png');
            $result = Storage::disk('s3')
                             ->put($this->imagePath('.png'), $image->stream());
        } catch (NotReadableException $exception) {
            info($exception->getMessage());
            info($exception->getTraceAsString());
            $result = false;
        }
        $this->update(['image_cached' => (bool)$result]);
        if ($result) {
            (static::class === Nft::class ? PoolNft::query() : Nft::query())
                ->where('asset_id', $this->asset_id)
                ->update([
                    'image_cached' => (bool)$result,
                    'ipfs_image_url' => $this->ipfs_image_url, // just in case
                ]);
        }
        return $result;
    }

    /**
     * @return string
     */
    public function imageUrlS3(): string
    {
        return \Storage::disk('s3')->url($this->imagePath());
    }

    /**
     * @param string|null $ext
     * @return string
     */
    public function imagePath(?string $ext = ''): string
    {
        return config('filesystems.disks.s3.img_path').$this->asset_id.$ext;
    }

//    public function getNft()
//    {
//        if (static::class === Nft::class) {
//            return $this;
//        } else {
//            return Nft::find($this->asset_id);
//        }
//    }
}
