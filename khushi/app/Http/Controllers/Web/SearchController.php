<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $category = $request->get('category');
        $brand = $request->get('brand');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $sortBy = $request->get('sort', 'relevance');
        $inStock = $request->boolean('in_stock');
        $rating = $request->get('rating');
        
        // Build product query with filters
        $products = Product::query()
            ->with(['category', 'brand', 'variants'])
            ->where('status', 'active');

        // Text search
        if ($query) {
            $products->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('short_description', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%")
                  ->orWhere('tags', 'LIKE', "%{$query}%");
            });
            
            // Log search query for analytics
            $this->logSearchQuery($query, $request->ip());
        }

        // Category filter
        if ($category) {
            $products->where('category_id', $category);
        }

        // Brand filter
        if ($brand) {
            $products->where('brand_id', $brand);
        }

        // Price range filter
        if ($minPrice) {
            $products->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $products->where('price', '<=', $maxPrice);
        }

        // Stock filter
        if ($inStock) {
            $products->where('stock_quantity', '>', 0);
        }

        // Rating filter
        if ($rating) {
            $products->whereHas('reviews', function($q) use ($rating) {
                $q->havingRaw('AVG(rating) >= ?', [$rating]);
            });
        }

        // Sorting
        switch ($sortBy) {
            case 'price_low':
                $products->orderBy('price', 'asc');
                break;
            case 'price_high':
                $products->orderBy('price', 'desc');
                break;
            case 'newest':
                $products->orderBy('created_at', 'desc');
                break;
            case 'rating':
                $products->leftJoin('reviews', 'products.id', '=', 'reviews.product_id')
                        ->select('products.*', DB::raw('AVG(reviews.rating) as avg_rating'))
                        ->groupBy('products.id')
                        ->orderBy('avg_rating', 'desc');
                break;
            case 'popularity':
                $products->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                        ->select('products.*', DB::raw('COUNT(order_items.id) as sales_count'))
                        ->groupBy('products.id')
                        ->orderBy('sales_count', 'desc');
                break;
            default: // relevance
                if ($query) {
                    $products->orderByRaw("
                        CASE 
                            WHEN name LIKE ? THEN 1
                            WHEN name LIKE ? THEN 2
                            WHEN description LIKE ? THEN 3
                            ELSE 4
                        END
                    ", ["{$query}%", "%{$query}%", "%{$query}%"]);
                } else {
                    $products->orderBy('created_at', 'desc');
                }
        }

        $products = $products->paginate(20)->withQueryString();

        // Get filter options for sidebar
        $categories = Category::active()->withCount('products')->get();
        $brands = Brand::active()->withCount('products')->get();
        $priceRange = $this->getPriceRange();

        // Search suggestions
        $suggestions = $this->getSearchSuggestions($query);

        return view('web.search.results', compact(
            'products', 'categories', 'brands', 'priceRange', 'query', 
            'suggestions', 'category', 'brand', 'minPrice', 'maxPrice', 
            'sortBy', 'inStock', 'rating'
        ));
    }

    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = Cache::remember("search_suggestions_{$query}", 300, function() use ($query) {
            $products = Product::where('status', 'active')
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "{$query}%")
                      ->orWhere('name', 'LIKE', "%{$query}%");
                })
                ->select('name', 'slug', 'image')
                ->limit(10)
                ->get();

            $categories = Category::where('status', 'active')
                ->where('name', 'LIKE', "%{$query}%")
                ->select('name', 'slug')
                ->limit(5)
                ->get();

            return [
                'products' => $products,
                'categories' => $categories
            ];
        });

        return response()->json($suggestions);
    }

    public function filters(Request $request)
    {
        $category = $request->get('category');
        $query = $request->get('q');

        $baseQuery = Product::where('status', 'active');

        if ($query) {
            $baseQuery->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            });
        }

        if ($category) {
            $baseQuery->where('category_id', $category);
        }

        // Get available brands for current search
        $brands = Brand::whereIn('id', function($q) use ($baseQuery) {
            $q->select('brand_id')
              ->from('products')
              ->whereIn('id', $baseQuery->select('id'));
        })->where('status', 'active')->get();

        // Get price range for current search
        $priceRange = $baseQuery->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();

        // Get available ratings
        $ratings = DB::table('reviews')
            ->whereIn('product_id', $baseQuery->select('id'))
            ->selectRaw('FLOOR(rating) as rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        return response()->json([
            'brands' => $brands,
            'price_range' => $priceRange,
            'ratings' => $ratings
        ]);
    }

    private function getPriceRange()
    {
        return Cache::remember('product_price_range', 3600, function() {
            return Product::where('status', 'active')
                ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
                ->first();
        });
    }

    private function getSearchSuggestions($query)
    {
        if (strlen($query) < 2) {
            return [];
        }

        return Cache::remember("search_suggestions_popular_{$query}", 300, function() use ($query) {
            return DB::table('search_logs')
                ->where('query', 'LIKE', "%{$query}%")
                ->select('query', DB::raw('COUNT(*) as search_count'))
                ->groupBy('query')
                ->orderBy('search_count', 'desc')
                ->limit(5)
                ->pluck('query')
                ->toArray();
        });
    }

    private function logSearchQuery($query, $ip)
    {
        DB::table('search_logs')->insert([
            'query' => $query,
            'ip_address' => $ip,
            'user_id' => auth()->id(),
            'created_at' => now()
        ]);
    }

    public function analytics()
    {
        $topSearches = DB::table('search_logs')
            ->select('query', DB::raw('COUNT(*) as search_count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('query')
            ->orderBy('search_count', 'desc')
            ->limit(20)
            ->get();

        $searchTrends = DB::table('search_logs')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as searches'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $noResultsQueries = DB::table('search_logs')
            ->leftJoin('products', function($join) {
                $join->on('products.name', 'LIKE', DB::raw("CONCAT('%', search_logs.query, '%')"))
                     ->orOn('products.description', 'LIKE', DB::raw("CONCAT('%', search_logs.query, '%')"));
            })
            ->whereNull('products.id')
            ->select('search_logs.query', DB::raw('COUNT(*) as search_count'))
            ->groupBy('search_logs.query')
            ->orderBy('search_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.analytics.search', compact(
            'topSearches', 'searchTrends', 'noResultsQueries'
        ));
    }
}
