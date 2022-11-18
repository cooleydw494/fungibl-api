<?php

namespace App\Providers;

use App\Models\PoolMeta;
use App\Models\PoolNft;
use App\Observers\PoolMetaObserver;
use App\Observers\PoolNftObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        PoolNft::observe(PoolNftObserver::class);
        PoolMeta::observe(PoolMetaObserver::class);
    }
}
