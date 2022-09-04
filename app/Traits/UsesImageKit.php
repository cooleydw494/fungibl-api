<?php

namespace App\Traits;

use ImageKit\ImageKit;

trait UsesImageKit {

    // I will fight anyone that says singletons are an anti-pattern
    /** @var ImageKit|null */
    public static ?ImageKit $imageKitClient = null;

    public function __construct()
    {
        $this->getClient();
    }

    /**
     * @return ImageKit
     */
    public function getClient(): ImageKit
    {
        if (is_null(static::$imageKitClient)) {
            static::$imageKitClient = new ImageKit(
                env('IMAGE_KIT_PUBLIC_KEY'),
                env('IMAGE_KIT_PRIVATE_KEY'),
                env('IMAGE_KIT_ENDPOINT'),
            );
        }
        return static::$imageKitClient;
    }

    /**
     * @param string     $path
     * @param int|null   $w
     * @param int|null   $h
     * @param array|null $params (radius, aspectRatio, see link below for more)
     * https://github.com/imagekit-developer/imagekit-php#list-of-supported-transformations
     * @return string
     */
    public function url(string $path, ?int $w, ?int $h, ?array $params = [])
    : string
    {
        return static::$imageKitClient->url([
            'path' => $path,
            'transformation' => [
                [
                    'width' => $w,
                    'height' => $h,
                    ...$params,
                ],
            ],
        ]);
    }
}
