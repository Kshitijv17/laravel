<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Observers\ProductObserver;
use App\Services\CacheService;
use App\Services\ImageOptimizationService;

class PerformanceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService();
        });

        $this->app->singleton(ImageOptimizationService::class, function ($app) {
            return new ImageOptimizationService();
        });
    }

    public function boot()
    {
        // Register observers for cache invalidation
        Product::observe(ProductObserver::class);
        
        // Register performance monitoring
        if (config('performance.monitoring.enabled')) {
            $this->registerPerformanceMonitoring();
        }
    }

    private function registerPerformanceMonitoring()
    {
        // Log slow database queries
        if (config('performance.database.slow_query_log')) {
            \DB::listen(function ($query) {
                if ($query->time > config('performance.database.slow_query_time', 2) * 1000) {
                    \Log::warning('Slow Query Detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms'
                    ]);
                }
            });
        }

        // Log slow requests
        if (config('performance.monitoring.log_slow_requests')) {
            $this->app['events']->listen('kernel.handled', function ($request, $response) {
                $threshold = config('performance.monitoring.slow_request_threshold', 1000);
                $executionTime = (microtime(true) - LARAVEL_START) * 1000;
                
                if ($executionTime > $threshold) {
                    \Log::warning('Slow Request Detected', [
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'execution_time' => round($executionTime, 2) . 'ms',
                        'memory_usage' => memory_get_peak_usage(true),
                        'user_id' => auth()->id()
                    ]);
                }
            });
        }
    }
}
