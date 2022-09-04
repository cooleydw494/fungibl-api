<?php

namespace App\Traits;

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
        $file = file_get_contents($this->ipfs_image_url);
        return Storage::disk('s3')
               ->putFile($this->imagePath(), $file, 'public');
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
        return config('filesystems.s3.img_path') . $this->asset_id;
    }
}
