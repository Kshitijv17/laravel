<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryAdapter;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class CloudinaryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('cloudinary', function ($app, $config) {
            $cloudinary = new CloudinaryEngine(
                $config['cloud_name'],
                $config['api_key'],
                $config['api_secret']
            );

            return new Filesystem(new CloudinaryAdapter($cloudinary, $config));
        });
    }
}
