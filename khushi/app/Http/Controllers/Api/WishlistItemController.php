<?php

namespace App\Http\Controllers\Api;

use App\Models\WishlistItem;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class WishlistItemController extends Controller
{
    /**
     * Display a listing of wishlist items
     */
    public function index(Request $request): JsonResponse
    {
        $query = WishlistItem::with(['wishlist.user', 'product']);

        if ($request->has('wishlist_id')) {
            $query->where('wishlist_id', $request->wishlist_id);
        }

        if ($request->has('user_id')) {
            $query->whereHas('wishlist', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $wishlistItems = $query->orderBy('created_at', 'desc')
                              ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $wishlistItems,
            'message' => 'Wishlist items retrieved successfully'
        ]);
    }

    /**
     * Store a newly created wishlist item
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'wishlist_id' => 'required|exists:wishlists,id',
            'product_id' => 'required|exists:products,id'
        ]);

        // Check if item already exists in wishlist
        $existingItem = WishlistItem::where('wishlist_id', $validated['wishlist_id'])
                                   ->where('product_id', $validated['product_id'])
                                   ->first();

        if ($existingItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in wishlist'
            ], 400);
        }

        $wishlistItem = WishlistItem::create($validated);

        return response()->json([
            'success' => true,
            'data' => $wishlistItem->load(['wishlist', 'product']),
            'message' => 'Product added to wishlist successfully'
        ], 201);
    }

    /**
     * Display the specified wishlist item
     */
    public function show(WishlistItem $wishlistItem): JsonResponse
    {
        $wishlistItem->load(['wishlist.user', 'product']);

        return response()->json([
            'success' => true,
            'data' => $wishlistItem,
            'message' => 'Wishlist item retrieved successfully'
        ]);
    }

    /**
     * Remove the specified wishlist item
     */
    public function destroy(WishlistItem $wishlistItem): JsonResponse
    {
        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist successfully'
        ]);
    }

    /**
     * Get wishlist items by wishlist
     */
    public function wishlistItems(Request $request, Wishlist $wishlist): JsonResponse
    {
        $items = $wishlist->items()->with(['product'])->get();

        return response()->json([
            'success' => true,
            'data' => [
                'wishlist' => $wishlist,
                'items' => $items,
                'total_items' => $items->count()
            ],
            'message' => 'Wishlist items retrieved successfully'
        ]);
    }

    /**
     * Move item to cart
     */
    public function moveToCart(Request $request, WishlistItem $wishlistItem): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1'
        ]);

        $quantity = $validated['quantity'] ?? 1;
        $userId = $wishlistItem->wishlist->user_id;

        // Check stock availability
        if ($wishlistItem->product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 400);
        }

        // Get or create user's cart
        $cart = \App\Models\Cart::firstOrCreate(['user_id' => $userId]);

        // Add to cart
        $cartItem = \App\Models\CartItem::where('cart_id', $cart->id)
                                       ->where('product_id', $wishlistItem->product_id)
                                       ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            \App\Models\CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $wishlistItem->product_id,
                'user_id' => $userId,
                'quantity' => $quantity
            ]);
        }

        // Remove from wishlist
        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product moved to cart successfully'
        ]);
    }
}
