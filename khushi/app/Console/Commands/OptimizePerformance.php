<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheService;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class OptimizePerformance extends Command
{
    protected $signature = 'optimize:performance {--cache-only : Only warm up cache} {--images-only : Only optimize images} {--db-only : Only optimize database}';
    protected $description = 'Optimize application performance by caching, image optimization, and database optimization';

    public function handle()
    {
        $cacheOnly = $this->option('cache-only');
        $imagesOnly = $this->option('images-only');
        $dbOnly = $this->option('db-only');

        $this->info('Starting performance optimization...');

        if (!$imagesOnly && !$dbOnly) {
            $this->optimizeCache();
        }

        if (!$cacheOnly && !$dbOnly) {
            $this->optimizeImages();
        }

        if (!$cacheOnly && !$imagesOnly) {
            $this->optimizeDatabase();
        }

        $this->info('Performance optimization completed!');
    }

    private function optimizeCache()
    {
        $this->info('Optimizing cache...');
        
        $cacheService = app(CacheService::class);
        
        // Clear old cache
        $this->line('Clearing old cache...');
        $cacheService->clearAllCache();
        
        // Warm up cache
        $this->line('Warming up cache...');
        $cacheService->warmUpCache();
        
        // Optimize Laravel caches
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        
        $this->info('Cache optimization completed.');
    }

    private function optimizeImages()
    {
        $this->info('Optimizing images...');
        
        $imageService = app(ImageOptimizationService::class);
        
        // Compress existing images
        $this->line('Compressing existing images...');
        $imageService->compressExistingImages('products');
        $imageService->compressExistingImages('banners');
        $imageService->compressExistingImages('categories');
        
        $this->info('Image optimization completed.');
    }

    private function optimizeDatabase()
    {
        $this->info('Optimizing database...');
        
        // Analyze tables
        $this->line('Analyzing database tables...');
        $tables = DB::select('SHOW TABLES');
        
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            DB::statement("ANALYZE TABLE {$tableName}");
        }
        
        // Optimize tables
        $this->line('Optimizing database tables...');
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            DB::statement("OPTIMIZE TABLE {$tableName}");
        }
        
        $this->info('Database optimization completed.');
    }
}
