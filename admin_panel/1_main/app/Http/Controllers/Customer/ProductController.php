<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        // Check if product is active and shop is active
        if (!$product->is_active || !$product->shop->is_active) {
            abort(404, 'Product not found or unavailable');
        }

        // Load relationships
        $product->load(['category', 'shop']);

        // Get related products from the same category
        $relatedProducts = Product::with(['category', 'shop'])
                                 ->where('is_active', true)
                                 ->where('category_id', $product->category_id)
                                 ->where('id', '!=', $product->id)
                                 ->whereHas('shop', function($q) {
                                     $q->where('is_active', true);
                                 })
                                 ->limit(4)
                                 ->get();

        // Get more products from the same shop
        $shopProducts = Product::with(['category'])
                              ->where('is_active', true)
                              ->where('shop_id', $product->shop_id)
                              ->where('id', '!=', $product->id)
                              ->limit(4)
                              ->get();

        // Calculate discount percentage if applicable
        $discountPercentage = 0;
        if ($product->discount_price && $product->discount_price < $product->price) {
            $discountPercentage = round((($product->price - $product->discount_price) / $product->price) * 100);
        }

        // Get final price (discount price if available, otherwise regular price)
        $finalPrice = $product->discount_price && $product->discount_price < $product->price 
                     ? $product->discount_price 
                     : $product->price;

        // Mock reviews data (since we don't have a reviews table yet)
        $reviews = collect(); // Empty collection for now
        $averageRating = 4.5; // Mock average rating

        return view('customer.product', compact(
            'product', 
            'relatedProducts', 
            'shopProducts', 
            'discountPercentage', 
            'finalPrice',
            'reviews',
            'averageRating'
        ));
    }

    /**
     * Search products (AJAX endpoint)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::with(['category', 'shop'])
                          ->where('is_active', true)
                          ->where(function($q) use ($query) {
                              $q->where('title', 'like', '%' . $query . '%')
                                ->orWhere('description', 'like', '%' . $query . '%');
                          })
                          ->whereHas('shop', function($q) {
                              $q->where('is_active', true);
                          })
                          ->limit(10)
                          ->get()
                          ->map(function($product) {
                              return [
                                  'id' => $product->id,
                                  'name' => $product->title,
                                  'price' => $product->selling_price ?? $product->price,
                                  'discount_price' => $product->price,
                                  'image' => $product->image ? asset('storage/' . $product->image) : null,
                                  'shop_name' => $product->shop->name,
                                  'category_name' => $product->category->title ?? 'Uncategorized',
                                  'url' => route('customer.product.show', $product)
                              ];
                          });

        return response()->json($products);
    }
}
