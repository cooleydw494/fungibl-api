<?php

namespace App\Traits;

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
     * @return false|string
     */
    public function cacheImage(): bool|string
    {
        $this->update(['image_cached' => null]); // null === unresolved attempt
        info(ini_get('allow_url_fopen'));
        try {
            $image = Image::make($this->ipfs_image_url);
            $image->encode('png');
            $result = Storage::disk('s3')
                             ->put($this->imagePath() . '.png', $image->stream());
        } catch (NotReadableException $exception) {
            info($exception->getMessage());
            info($exception->getTraceAsString());
            $result = false;
        }
        $this->update(['image_cached' => (bool)$result]);
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
     * @return string
     */
    public function imagePath(): string
    {
        return config('filesystems.disks.s3.img_path') . $this->asset_id;
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
