<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ImageStorageService;

class ImageStorageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('image.storage', function ($app) {
            $disk = config('filesystems.default');
            $rootFolder = config('filesystems.disks.'.$disk.'.root', 'public/storage');
            
            return new ImageStorageService($disk, $rootFolder);
        });
    }

    public function boot()
    {
        // Publish configuration file
        $this->publishes([
            __DIR__.'/../../config/image-storage.php' => config_path('image-storage.php'),
        ], 'config');
    }
}
