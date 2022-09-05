<?php

namespace App\Traits;

use Image;
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
        $image = Image::make($this->ipfs_image_url);
        $image->encode('png');
        return Storage::disk('s3')
               ->put($this->imagePath() . '.png', $image->stream());
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
}
