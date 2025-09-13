<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Get user's cart
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        
        $cart = Cart::where('user_id', $userId)
            ->with(['items.product', 'user'])
            ->first();

        if (!$cart) {
            $cart = Cart::create(['user_id' => $userId]);
        }

        return response()->json([
            'success' => true,
            'data' => $cart,
            'message' => 'Cart retrieved successfully'
        ]);
    }

    /**
     * Add item to cart
     */
    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $userId = $request->user()->id;
        $product = Product::findOrFail($validated['product_id']);
        
        if ($product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 400);
        }

        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $validated['quantity'];
            if ($product->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more items. Stock limit exceeded'
                ], 400);
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $validated['product_id'],
                'user_id' => $validated['user_id'],
                'quantity' => $validated['quantity']
            ]);
        }

        $cart->load(['items.product']);

        return response()->json([
            'success' => true,
            'data' => $cart,
            'message' => 'Item added to cart successfully'
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateItem(Request $request, CartItem $cartItem): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if ($cartItem->product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 400);
        }

        $cartItem->update($validated);
        $cartItem->load(['product', 'cart.items.product']);

        return response()->json([
            'success' => true,
            'data' => $cartItem->cart,
            'message' => 'Cart item updated successfully'
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeItem(CartItem $cartItem): JsonResponse
    {
        $cart = $cartItem->cart;
        $cartItem->delete();
        $cart->load(['items.product']);

        return response()->json([
            'success' => true,
            'data' => $cart,
            'message' => 'Item removed from cart successfully'
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        
        $cart = Cart::where('user_id', $userId)->first();
        
        if ($cart) {
            $cart->items()->delete();
            $cart->load(['items.product']);
        }

        return response()->json([
            'success' => true,
            'data' => $cart,
            'message' => 'Cart cleared successfully'
        ]);
    }

    /**
     * Get cart summary
     */
    public function summary(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        
        $cart = Cart::where('user_id', $userId)
            ->with(['items.product'])
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_items' => 0,
                    'total_amount' => 0,
                    'items' => []
                ],
                'message' => 'Cart is empty'
            ]);
        }

        $summary = [
            'total_items' => $cart->total_items,
            'total_amount' => $cart->total_amount,
            'items_count' => $cart->items_count,
            'items' => $cart->items
        ];

        return response()->json([
            'success' => true,
            'data' => $summary,
            'message' => 'Cart summary retrieved successfully'
        ]);
    }
}
