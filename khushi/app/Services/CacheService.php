<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;

class CacheService
{
    const CACHE_TTL = 3600; // 1 hour
    const LONG_CACHE_TTL = 86400; // 24 hours

    public function getPopularProducts(int $limit = 8)
    {
        return Cache::remember('popular_products', self::CACHE_TTL, function () use ($limit) {
            return Product::with(['category', 'brand', 'images'])
                ->where('status', 'active')
                ->where('stock_quantity', '>', 0)
                ->orderBy('view_count', 'desc')
                ->orderBy('sales_count', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    public function getFeaturedProducts(int $limit = 8)
    {
        return Cache::remember('featured_products', self::CACHE_TTL, function () use ($limit) {
            return Product::with(['category', 'brand', 'images'])
                ->where('status', 'active')
                ->where('is_featured', true)
                ->where('stock_quantity', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    public function getLatestProducts(int $limit = 8)
    {
        return Cache::remember('latest_products', self::CACHE_TTL, function () use ($limit) {
            return Product::with(['category', 'brand', 'images'])
                ->where('status', 'active')
                ->where('stock_quantity', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    public function getCategories()
    {
        return Cache::remember('categories_with_counts', self::LONG_CACHE_TTL, function () {
            return Category::withCount(['products' => function ($query) {
                $query->where('status', 'active')->where('stock_quantity', '>', 0);
            }])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        });
    }

    public function getBrands()
    {
        return Cache::remember('brands_with_counts', self::LONG_CACHE_TTL, function () {
            return Brand::withCount(['products' => function ($query) {
                $query->where('status', 'active')->where('stock_quantity', '>', 0);
            }])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        });
    }

    public function getActiveBanners()
    {
        return Cache::remember('active_banners', self::CACHE_TTL, function () {
            return Banner::where('status', 'active')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->orderBy('sort_order')
                ->get();
        });
    }

    public function getProductFilters()
    {
        return Cache::remember('product_filters', self::LONG_CACHE_TTL, function () {
            return [
                'categories' => $this->getCategories(),
                'brands' => $this->getBrands(),
                'price_range' => $this->getPriceRange(),
                'attributes' => $this->getProductAttributes()
            ];
        });
    }

    public function getPriceRange()
    {
        return Cache::remember('price_range', self::LONG_CACHE_TTL, function () {
            return DB::table('products')
                ->where('status', 'active')
                ->where('stock_quantity', '>', 0)
                ->selectRaw('MIN(selling_price) as min_price, MAX(selling_price) as max_price')
                ->first();
        });
    }

    public function getProductAttributes()
    {
        return Cache::remember('product_attributes', self::LONG_CACHE_TTL, function () {
            return DB::table('product_attributes')
                ->join('products', 'product_attributes.product_id', '=', 'products.id')
                ->where('products.status', 'active')
                ->select('attribute_name', 'attribute_value')
                ->distinct()
                ->orderBy('attribute_name')
                ->orderBy('attribute_value')
                ->get()
                ->groupBy('attribute_name');
        });
    }

    public function getSiteSettings()
    {
        return Cache::remember('site_settings', self::LONG_CACHE_TTL, function () {
            return DB::table('settings')->pluck('value', 'key');
        });
    }

    public function getNavigationMenu()
    {
        return Cache::remember('navigation_menu', self::LONG_CACHE_TTL, function () {
            return Category::with(['children' => function ($query) {
                $query->where('status', 'active')->orderBy('sort_order');
            }])
            ->where('status', 'active')
            ->where('parent_id', null)
            ->orderBy('sort_order')
            ->get();
        });
    }

    public function clearProductCache()
    {
        $keys = [
            'popular_products',
            'featured_products',
            'latest_products',
            'product_filters',
            'price_range',
            'product_attributes'
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    public function clearCategoryCache()
    {
        Cache::forget('categories_with_counts');
        Cache::forget('navigation_menu');
        Cache::forget('product_filters');
    }

    public function clearBrandCache()
    {
        Cache::forget('brands_with_counts');
        Cache::forget('product_filters');
    }

    public function clearAllCache()
    {
        Cache::flush();
    }

    public function warmUpCache()
    {
        // Warm up frequently accessed data
        $this->getPopularProducts();
        $this->getFeaturedProducts();
        $this->getLatestProducts();
        $this->getCategories();
        $this->getBrands();
        $this->getActiveBanners();
        $this->getProductFilters();
        $this->getSiteSettings();
        $this->getNavigationMenu();
    }
}
