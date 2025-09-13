<?php

namespace App\Http\Controllers\Api;

use App\Models\CartItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CartItemController extends Controller
{
    /**
     * Display a listing of cart items
     */
    public function index(Request $request): JsonResponse
    {
        $query = CartItem::with(['cart.user', 'product']);

        if ($request->has('cart_id')) {
            $query->where('cart_id', $request->cart_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $cartItems = $query->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $cartItems,
            'message' => 'Cart items retrieved successfully'
        ]);
    }

    /**
     * Store a newly created cart item
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1'
        ]);

        // Check if item already exists in cart
        $existingItem = CartItem::where('cart_id', $validated['cart_id'])
                               ->where('product_id', $validated['product_id'])
                               ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $validated['quantity']);
            $cartItem = $existingItem;
        } else {
            $cartItem = CartItem::create($validated);
        }

        return response()->json([
            'success' => true,
            'data' => $cartItem->load(['cart', 'product']),
            'message' => 'Cart item added successfully'
        ], 201);
    }

    /**
     * Display the specified cart item
     */
    public function show(CartItem $cartItem): JsonResponse
    {
        $cartItem->load(['cart.user', 'product']);

        return response()->json([
            'success' => true,
            'data' => $cartItem,
            'message' => 'Cart item retrieved successfully'
        ]);
    }

    /**
     * Update the specified cart item
     */
    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem->update($validated);

        return response()->json([
            'success' => true,
            'data' => $cartItem->load(['cart', 'product']),
            'message' => 'Cart item updated successfully'
        ]);
    }

    /**
     * Remove the specified cart item
     */
    public function destroy(CartItem $cartItem): JsonResponse
    {
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart item removed successfully'
        ]);
    }

    /**
     * Get cart items by cart
     */
    public function cartItems(Request $request, Cart $cart): JsonResponse
    {
        $items = $cart->items()->with(['product'])->get();

        $summary = [
            'total_items' => $items->count(),
            'total_quantity' => $items->sum('quantity'),
            'total_amount' => $items->sum(function ($item) {
                return $item->quantity * $item->product->final_price;
            })
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'summary' => $summary
            ],
            'message' => 'Cart items retrieved successfully'
        ]);
    }
}
