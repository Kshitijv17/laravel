<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductComparison;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class ComparisonService
{
    const MAX_COMPARISON_PRODUCTS = 4;

    public function addToComparison($productId, $userId = null)
    {
        $sessionId = Session::getId();
        $comparisonProducts = $this->getComparisonProducts($userId, $sessionId);
        
        if (count($comparisonProducts) >= self::MAX_COMPARISON_PRODUCTS) {
            return [
                'success' => false,
                'message' => 'Maximum ' . self::MAX_COMPARISON_PRODUCTS . ' products can be compared at once'
            ];
        }
        
        if (in_array($productId, $comparisonProducts)) {
            return [
                'success' => false,
                'message' => 'Product is already in comparison list'
            ];
        }
        
        $comparisonProducts[] = $productId;
        $this->saveComparison($comparisonProducts, $userId, $sessionId);
        
        return [
            'success' => true,
            'message' => 'Product added to comparison',
            'count' => count($comparisonProducts)
        ];
    }

    public function removeFromComparison($productId, $userId = null)
    {
        $sessionId = Session::getId();
        $comparisonProducts = $this->getComparisonProducts($userId, $sessionId);
        
        $comparisonProducts = array_values(array_filter($comparisonProducts, function($id) use ($productId) {
            return $id != $productId;
        }));
        
        $this->saveComparison($comparisonProducts, $userId, $sessionId);
        
        return [
            'success' => true,
            'message' => 'Product removed from comparison',
            'count' => count($comparisonProducts)
        ];
    }

    public function getComparisonProducts($userId = null, $sessionId = null)
    {
        $sessionId = $sessionId ?: Session::getId();
        
        $comparison = ProductComparison::where(function($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->latest()->first();
        
        return $comparison ? $comparison->product_ids : [];
    }

    public function getComparisonData($userId = null, $sessionId = null)
    {
        $productIds = $this->getComparisonProducts($userId, $sessionId);
        
        if (empty($productIds)) {
            return [];
        }
        
        $products = Product::with(['category', 'brand', 'images', 'attributes'])
            ->whereIn('id', $productIds)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');
        
        // Maintain order of products as they were added
        $orderedProducts = collect($productIds)->map(function($id) use ($products) {
            return $products->get($id);
        })->filter();
        
        return $this->formatComparisonData($orderedProducts);
    }

    public function clearComparison($userId = null, $sessionId = null)
    {
        $sessionId = $sessionId ?: Session::getId();
        
        ProductComparison::where(function($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->delete();
        
        return [
            'success' => true,
            'message' => 'Comparison list cleared'
        ];
    }

    public function getComparisonCount($userId = null, $sessionId = null)
    {
        return $this->getComparisonProducts($userId, $sessionId)->count();
    }

    public function isInComparison($productId, $userId = null, $sessionId = null)
    {
        $comparisonProducts = $this->getComparisonProducts($userId, $sessionId);
        return in_array($productId, $comparisonProducts);
    }

    public function generateComparisonTable($products)
    {
        if ($products->isEmpty()) {
            return [];
        }
        
        $comparisonTable = [];
        
        // Basic Information
        $comparisonTable['Basic Information'] = [
            'Name' => $products->pluck('name')->toArray(),
            'Brand' => $products->map(fn($p) => $p->brand->name ?? 'N/A')->toArray(),
            'Category' => $products->map(fn($p) => $p->category->name ?? 'N/A')->toArray(),
            'SKU' => $products->pluck('sku')->toArray(),
        ];
        
        // Pricing
        $comparisonTable['Pricing'] = [
            'Price' => $products->map(fn($p) => '$' . number_format($p->selling_price, 2))->toArray(),
            'Original Price' => $products->map(fn($p) => $p->original_price ? '$' . number_format($p->original_price, 2) : 'N/A')->toArray(),
            'Discount' => $products->map(function($p) {
                if ($p->original_price && $p->original_price > $p->selling_price) {
                    $discount = (($p->original_price - $p->selling_price) / $p->original_price) * 100;
                    return round($discount) . '%';
                }
                return 'N/A';
            })->toArray(),
        ];
        
        // Availability
        $comparisonTable['Availability'] = [
            'Stock Status' => $products->map(fn($p) => $p->stock_quantity > 0 ? 'In Stock' : 'Out of Stock')->toArray(),
            'Stock Quantity' => $products->pluck('stock_quantity')->toArray(),
        ];
        
        // Ratings & Reviews
        $comparisonTable['Ratings & Reviews'] = [
            'Average Rating' => $products->map(fn($p) => $p->average_rating ? number_format($p->average_rating, 1) . '/5' : 'No ratings')->toArray(),
            'Total Reviews' => $products->map(fn($p) => $p->reviews_count ?: 0)->toArray(),
        ];
        
        // Specifications (from product attributes)
        $allAttributes = $products->flatMap(function($product) {
            return $product->attributes->pluck('attribute_name');
        })->unique()->values();
        
        if ($allAttributes->isNotEmpty()) {
            $specifications = [];
            foreach ($allAttributes as $attribute) {
                $specifications[$attribute] = $products->map(function($product) use ($attribute) {
                    $attr = $product->attributes->firstWhere('attribute_name', $attribute);
                    return $attr ? $attr->attribute_value : 'N/A';
                })->toArray();
            }
            $comparisonTable['Specifications'] = $specifications;
        }
        
        // Additional Features
        $comparisonTable['Features'] = [
            'Weight' => $products->map(fn($p) => $p->weight ? $p->weight . ' kg' : 'N/A')->toArray(),
            'Dimensions' => $products->map(fn($p) => $p->dimensions ?: 'N/A')->toArray(),
        ];
        
        return $comparisonTable;
    }

    public function getSimilarAlternatives($productIds, $limit = 6)
    {
        $products = Product::whereIn('id', $productIds)->get();
        
        if ($products->isEmpty()) {
            return collect();
        }
        
        $categoryIds = $products->pluck('category_id')->unique();
        $brandIds = $products->pluck('brand_id')->unique();
        $priceRange = [
            'min' => $products->min('selling_price') * 0.8,
            'max' => $products->max('selling_price') * 1.2
        ];
        
        return Product::whereNotIn('id', $productIds)
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->where(function($query) use ($categoryIds, $brandIds) {
                $query->whereIn('category_id', $categoryIds)
                      ->orWhereIn('brand_id', $brandIds);
            })
            ->whereBetween('selling_price', [$priceRange['min'], $priceRange['max']])
            ->with(['category', 'brand', 'images'])
            ->withCount(['orderItems as sales_count'])
            ->orderByDesc('sales_count')
            ->limit($limit)
            ->get();
    }

    private function saveComparison($productIds, $userId = null, $sessionId = null)
    {
        $sessionId = $sessionId ?: Session::getId();
        
        // Delete existing comparison
        ProductComparison::where(function($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->delete();
        
        // Create new comparison if there are products
        if (!empty($productIds)) {
            ProductComparison::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'product_ids' => $productIds,
                'comparison_data' => $this->generateComparisonMetadata($productIds)
            ]);
        }
    }

    private function formatComparisonData($products)
    {
        return [
            'products' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->selling_price,
                    'original_price' => $product->original_price,
                    'discount_percentage' => $product->original_price > $product->selling_price ? 
                        round((($product->original_price - $product->selling_price) / $product->original_price) * 100) : 0,
                    'image' => $product->images->first()?->image_path,
                    'brand' => $product->brand?->name,
                    'category' => $product->category?->name,
                    'rating' => $product->average_rating,
                    'reviews_count' => $product->reviews_count,
                    'stock_status' => $product->stock_quantity > 0 ? 'in_stock' : 'out_of_stock',
                    'stock_quantity' => $product->stock_quantity,
                    'attributes' => $product->attributes->mapWithKeys(function($attr) {
                        return [$attr->attribute_name => $attr->attribute_value];
                    })
                ];
            }),
            'comparison_table' => $this->generateComparisonTable($products),
            'similar_alternatives' => $this->getSimilarAlternatives($products->pluck('id')->toArray())
        ];
    }

    private function generateComparisonMetadata($productIds)
    {
        $products = Product::whereIn('id', $productIds)->get();
        
        return [
            'total_products' => count($productIds),
            'categories' => $products->pluck('category.name')->unique()->values()->toArray(),
            'brands' => $products->pluck('brand.name')->unique()->values()->toArray(),
            'price_range' => [
                'min' => $products->min('selling_price'),
                'max' => $products->max('selling_price')
            ],
            'created_at' => now()->toISOString()
        ];
    }
}
