<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function created(Product $product)
    {
        $this->clearProductCaches();
    }

    public function updated(Product $product)
    {
        $this->clearProductCaches();
        
        // Clear specific product cache
        Cache::forget("product:{$product->id}");
        Cache::forget("product_slug:{$product->slug}");
    }

    public function deleted(Product $product)
    {
        $this->clearProductCaches();
        
        // Clear specific product cache
        Cache::forget("product:{$product->id}");
        Cache::forget("product_slug:{$product->slug}");
    }

    private function clearProductCaches()
    {
        $this->cacheService->clearProductCache();
        
        // Clear page cache that might contain products
        $this->clearPageCache();
    }

    private function clearPageCache()
    {
        $patterns = [
            'page_cache:*',
            'home_*',
            'category_*',
            'search_*'
        ];

        foreach ($patterns as $pattern) {
            $keys = Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                Cache::getRedis()->del($keys);
            }
        }
    }
}
