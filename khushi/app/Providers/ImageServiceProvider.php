<?php

namespace App\Providers;

use App\Services\ImageStorageService;
use Illuminate\Support\ServiceProvider;

class ImageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('image.storage', function ($app) {
            return new ImageStorageService(
                config('filesystems.default'),
                config('filesystems.disks.' . config('filesystems.default') . '.root', 'uploads')
            );
        });
    }

    public function provides()
    {
        return ['image.storage'];
    }
}
