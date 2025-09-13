<?php

namespace App\Http\Controllers\Api;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class WishlistController extends Controller
{
    /**
     * Get user's wishlist
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        
        $wishlist = Wishlist::where('user_id', $userId)
            ->with(['product.category'])
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $wishlist,
            'message' => 'Wishlist retrieved successfully'
        ]);
    }

    /**
     * Add product to wishlist
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id'
        ]);

        $existing = Wishlist::where('user_id', $validated['user_id'])
                           ->where('product_id', $validated['product_id'])
                           ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in wishlist'
            ], 400);
        }

        $wishlist = Wishlist::create($validated);
        $wishlist->load(['product']);

        return response()->json([
            'success' => true,
            'data' => $wishlist,
            'message' => 'Product added to wishlist successfully'
        ], 201);
    }

    /**
     * Remove product from wishlist
     */
    public function destroy(Wishlist $wishlist): JsonResponse
    {
        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist successfully'
        ]);
    }

    /**
     * Clear entire wishlist
     */
    public function clear(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        
        Wishlist::where('user_id', $userId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared successfully'
        ]);
    }

    /**
     * Check if product is in wishlist
     */
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id'
        ]);

        $exists = Wishlist::where('user_id', $validated['user_id'])
                         ->where('product_id', $validated['product_id'])
                         ->exists();

        return response()->json([
            'success' => true,
            'data' => ['in_wishlist' => $exists],
            'message' => 'Wishlist check completed'
        ]);
    }
}
