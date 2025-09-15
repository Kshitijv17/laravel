<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductRecommendation;
use App\Models\Order;
use App\Models\Analytics;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    public function getRecommendations($productId, $userId = null, $limit = 8)
    {
        $cacheKey = "recommendations:{$productId}:{$userId}:{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($productId, $userId, $limit) {
            $recommendations = collect();
            
            // Get different types of recommendations
            $collaborative = $this->getCollaborativeRecommendations($productId, $userId, $limit);
            $contentBased = $this->getContentBasedRecommendations($productId, $limit);
            $trending = $this->getTrendingRecommendations($productId, $limit);
            $crossSell = $this->getCrossSellRecommendations($productId, $limit);
            
            // Merge and score recommendations
            $allRecommendations = $collaborative->merge($contentBased)
                ->merge($trending)
                ->merge($crossSell)
                ->groupBy('id')
                ->map(function ($group) {
                    $product = $group->first();
                    $totalScore = $group->sum('recommendation_score');
                    $product->recommendation_score = $totalScore;
                    $product->recommendation_reasons = $group->pluck('recommendation_reason')->unique()->toArray();
                    return $product;
                })
                ->sortByDesc('recommendation_score')
                ->take($limit);
            
            return $allRecommendations->values();
        });
    }

    public function getPersonalizedRecommendations($userId, $limit = 12)
    {
        $cacheKey = "personalized_recommendations:{$userId}:{$limit}";
        
        return Cache::remember($cacheKey, 1800, function () use ($userId, $limit) {
            $user = User::find($userId);
            if (!$user) {
                return $this->getPopularProducts($limit);
            }
            
            $recommendations = collect();
            
            // Based on purchase history
            $purchaseHistory = $this->getRecommendationsFromPurchaseHistory($userId, $limit / 2);
            $recommendations = $recommendations->merge($purchaseHistory);
            
            // Based on browsing behavior
            $browsingBehavior = $this->getRecommendationsFromBrowsingBehavior($userId, $limit / 2);
            $recommendations = $recommendations->merge($browsingBehavior);
            
            // Fill remaining slots with trending products
            $remaining = $limit - $recommendations->count();
            if ($remaining > 0) {
                $trending = $this->getTrendingProducts($remaining);
                $recommendations = $recommendations->merge($trending);
            }
            
            return $recommendations->unique('id')->take($limit);
        });
    }

    public function getSimilarProducts($productId, $limit = 6)
    {
        $cacheKey = "similar_products:{$productId}:{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($productId, $limit) {
            $product = Product::find($productId);
            if (!$product) {
                return collect();
            }
            
            $similar = Product::where('id', '!=', $productId)
                ->where('status', 'active')
                ->where('stock_quantity', '>', 0)
                ->where(function ($query) use ($product) {
                    $query->where('category_id', $product->category_id)
                          ->orWhere('brand_id', $product->brand_id);
                })
                ->withCount(['orderItems as sales_count'])
                ->orderByDesc('sales_count')
                ->limit($limit)
                ->get();
            
            return $similar->map(function ($item) {
                $item->recommendation_score = $this->calculateSimilarityScore($item);
                $item->recommendation_reason = 'Similar product';
                return $item;
            });
        });
    }

    public function getFrequentlyBoughtTogether($productId, $limit = 4)
    {
        $cacheKey = "frequently_bought_together:{$productId}:{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($productId, $limit) {
            // Find products frequently bought together
            $frequentlyBought = DB::table('order_items as oi1')
                ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
                ->join('products', 'oi2.product_id', '=', 'products.id')
                ->where('oi1.product_id', $productId)
                ->where('oi2.product_id', '!=', $productId)
                ->where('products.status', 'active')
                ->where('products.stock_quantity', '>', 0)
                ->select('products.*', DB::raw('COUNT(*) as frequency'))
                ->groupBy('products.id')
                ->orderByDesc('frequency')
                ->limit($limit)
                ->get();
            
            return $frequentlyBought->map(function ($item) {
                $item->recommendation_score = $item->frequency * 0.1;
                $item->recommendation_reason = 'Frequently bought together';
                return $item;
            });
        });
    }

    public function getRecentlyViewedRecommendations($userId, $limit = 8)
    {
        $recentlyViewed = Analytics::where('user_id', $userId)
            ->where('event_type', 'product_view')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->pluck('data.product_id')
            ->unique()
            ->take(10);
        
        $recommendations = collect();
        
        foreach ($recentlyViewed as $productId) {
            $similar = $this->getSimilarProducts($productId, 2);
            $recommendations = $recommendations->merge($similar);
        }
        
        return $recommendations->unique('id')->take($limit);
    }

    public function getAbandonedCartRecommendations($userId, $limit = 6)
    {
        $cartItems = DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('cart_items.user_id', $userId)
            ->where('products.status', 'active')
            ->select('products.*')
            ->get();
        
        $recommendations = collect();
        
        foreach ($cartItems as $item) {
            $similar = $this->getSimilarProducts($item->id, 2);
            $recommendations = $recommendations->merge($similar);
        }
        
        return $recommendations->unique('id')->take($limit);
    }

    private function getCollaborativeRecommendations($productId, $userId, $limit)
    {
        if (!$userId) {
            return collect();
        }
        
        // Find users who bought this product
        $similarUsers = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.product_id', $productId)
            ->where('orders.user_id', '!=', $userId)
            ->where('orders.status', 'completed')
            ->pluck('orders.user_id')
            ->unique();
        
        // Find products these users also bought
        $recommendations = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('orders.user_id', $similarUsers)
            ->where('order_items.product_id', '!=', $productId)
            ->where('products.status', 'active')
            ->where('products.stock_quantity', '>', 0)
            ->select('products.*', DB::raw('COUNT(*) as frequency'))
            ->groupBy('products.id')
            ->orderByDesc('frequency')
            ->limit($limit)
            ->get();
        
        return $recommendations->map(function ($item) {
            $item->recommendation_score = $item->frequency * 0.3;
            $item->recommendation_reason = 'Users who bought this also bought';
            return $item;
        });
    }

    private function getContentBasedRecommendations($productId, $limit)
    {
        $product = Product::find($productId);
        if (!$product) {
            return collect();
        }
        
        return Product::where('id', '!=', $productId)
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id)
                      ->orWhere('brand_id', $product->brand_id);
            })
            ->whereBetween('selling_price', [
                $product->selling_price * 0.7,
                $product->selling_price * 1.3
            ])
            ->limit($limit)
            ->get()
            ->map(function ($item) use ($product) {
                $item->recommendation_score = $this->calculateContentSimilarity($product, $item);
                $item->recommendation_reason = 'Similar features and price';
                return $item;
            });
    }

    private function getTrendingRecommendations($productId, $limit)
    {
        return Product::where('id', '!=', $productId)
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->withCount(['orderItems as sales_count' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('created_at', '>=', now()->subDays(7));
                });
            }])
            ->orderByDesc('sales_count')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $item->recommendation_score = $item->sales_count * 0.1;
                $item->recommendation_reason = 'Trending now';
                return $item;
            });
    }

    private function getCrossSellRecommendations($productId, $limit)
    {
        $product = Product::find($productId);
        if (!$product || !$product->category) {
            return collect();
        }
        
        // Find complementary categories
        $complementaryCategories = $this->getComplementaryCategories($product->category_id);
        
        return Product::where('id', '!=', $productId)
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->whereIn('category_id', $complementaryCategories)
            ->withCount(['orderItems as sales_count'])
            ->orderByDesc('sales_count')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $item->recommendation_score = 0.2;
                $item->recommendation_reason = 'Great together';
                return $item;
            });
    }

    private function getRecommendationsFromPurchaseHistory($userId, $limit)
    {
        $purchasedCategories = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.user_id', $userId)
            ->where('orders.status', 'completed')
            ->pluck('products.category_id')
            ->unique();
        
        return Product::whereIn('category_id', $purchasedCategories)
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->withCount(['orderItems as sales_count'])
            ->orderByDesc('sales_count')
            ->limit($limit)
            ->get();
    }

    private function getRecommendationsFromBrowsingBehavior($userId, $limit)
    {
        $viewedCategories = Analytics::where('user_id', $userId)
            ->where('event_type', 'product_view')
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->pluck('data.category_id')
            ->filter()
            ->unique();
        
        return Product::whereIn('category_id', $viewedCategories)
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->withCount(['orderItems as sales_count'])
            ->orderByDesc('sales_count')
            ->limit($limit)
            ->get();
    }

    private function getPopularProducts($limit)
    {
        return Product::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->withCount(['orderItems as sales_count'])
            ->orderByDesc('sales_count')
            ->limit($limit)
            ->get();
    }

    private function getTrendingProducts($limit)
    {
        return Product::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->withCount(['orderItems as recent_sales' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('created_at', '>=', now()->subDays(7));
                });
            }])
            ->orderByDesc('recent_sales')
            ->limit($limit)
            ->get();
    }

    private function calculateSimilarityScore($product)
    {
        // Simple scoring based on various factors
        $score = 0.5; // Base score
        
        if ($product->sales_count > 10) {
            $score += 0.2;
        }
        
        if ($product->average_rating > 4) {
            $score += 0.2;
        }
        
        if ($product->created_at > now()->subDays(30)) {
            $score += 0.1;
        }
        
        return min($score, 1.0);
    }

    private function calculateContentSimilarity($product1, $product2)
    {
        $score = 0.3; // Base score
        
        if ($product1->category_id === $product2->category_id) {
            $score += 0.3;
        }
        
        if ($product1->brand_id === $product2->brand_id) {
            $score += 0.2;
        }
        
        // Price similarity
        $priceDiff = abs($product1->selling_price - $product2->selling_price) / $product1->selling_price;
        if ($priceDiff < 0.2) {
            $score += 0.2;
        }
        
        return min($score, 1.0);
    }

    private function getComplementaryCategories($categoryId)
    {
        // Define complementary category relationships
        $complementaryMap = [
            // Example: Electronics accessories
            1 => [2, 3, 4], // Phones -> Cases, Chargers, Headphones
            // Add more mappings based on your categories
        ];
        
        return $complementaryMap[$categoryId] ?? [];
    }

    public function clearRecommendationCache($productId = null, $userId = null)
    {
        if ($productId) {
            Cache::forget("recommendations:{$productId}:*");
            Cache::forget("similar_products:{$productId}:*");
            Cache::forget("frequently_bought_together:{$productId}:*");
        }
        
        if ($userId) {
            Cache::forget("personalized_recommendations:{$userId}:*");
        }
    }
}
