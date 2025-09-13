<?php

namespace App\Http\Controllers\Web;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display cart page
     */
    public function index()
    {
        $cart = null;
        $cartItems = collect();
        
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())
                ->with(['items.product'])
                ->first();
            
            if ($cart) {
                $cartItems = $cart->items;
            }
        } else {
            // Handle guest cart from session
            $sessionCart = Session::get('cart', []);
            foreach ($sessionCart as $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $cartItems->push((object)[
                        'id' => $item['id'],
                        'product' => $product,
                        'quantity' => $item['quantity'],
                        'price' => $product->price
                    ]);
                }
            }
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        // Calculate tax and total
        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $tax;

        // Get recommended products
        $recommendedProducts = Product::where('is_featured', true)
            ->where('status', true)
            ->inStock()
            ->limit(4)
            ->get();

        return view('web.cart.index', compact('cartItems', 'subtotal', 'tax', 'total', 'recommendedProducts'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ]);
        }

        if (Auth::check()) {
            // Authenticated user cart
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $validated['product_id'])
                ->where('variant_id', $validated['variant_id'] ?? null)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $validated['quantity'];
                if ($product->stock < $newQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot add more items. Stock limit reached.'
                    ]);
                }
                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $validated['product_id'],
                    'variant_id' => $validated['variant_id'] ?? null,
                    'quantity' => $validated['quantity'],
                    'price' => $product->price
                ]);
            }
        } else {
            // Guest cart in session
            $sessionCart = Session::get('cart', []);
            $itemKey = $validated['product_id'] . '_' . ($validated['variant_id'] ?? 'default');
            
            if (isset($sessionCart[$itemKey])) {
                $newQuantity = $sessionCart[$itemKey]['quantity'] + $validated['quantity'];
                if ($product->stock < $newQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot add more items. Stock limit reached.'
                    ]);
                }
                $sessionCart[$itemKey]['quantity'] = $newQuantity;
            } else {
                $sessionCart[$itemKey] = [
                    'id' => $itemKey,
                    'product_id' => $validated['product_id'],
                    'variant_id' => $validated['variant_id'] ?? null,
                    'quantity' => $validated['quantity'],
                    'price' => $product->price
                ];
            }
            
            Session::put('cart', $sessionCart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully'
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if (Auth::check()) {
            $cartItem = CartItem::whereHas('cart', function ($q) {
                $q->where('user_id', Auth::id());
            })->findOrFail($itemId);

            if ($cartItem->product->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available'
                ]);
            }

            $cartItem->update(['quantity' => $validated['quantity']]);
        } else {
            $sessionCart = Session::get('cart', []);
            if (isset($sessionCart[$itemId])) {
                $product = Product::find($sessionCart[$itemId]['product_id']);
                if ($product->stock < $validated['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock available'
                    ]);
                }
                $sessionCart[$itemId]['quantity'] = $validated['quantity'];
                Session::put('cart', $sessionCart);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully'
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove($itemId)
    {
        if (Auth::check()) {
            $cartItem = CartItem::whereHas('cart', function ($q) {
                $q->where('user_id', Auth::id());
            })->findOrFail($itemId);
            
            $cartItem->delete();
        } else {
            $sessionCart = Session::get('cart', []);
            unset($sessionCart[$itemId]);
            Session::put('cart', $sessionCart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart'
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $cart->items()->delete();
            }
        } else {
            Session::forget('cart');
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }

    /**
     * Apply coupon to cart
     */
    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string'
        ]);

        $coupon = Coupon::where('code', $validated['coupon_code'])
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon code'
            ]);
        }

        // Store coupon in session
        Session::put('applied_coupon', $coupon->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully',
            'coupon' => $coupon
        ]);
    }

    /**
     * Remove applied coupon
     */
    public function removeCoupon()
    {
        Session::forget('applied_coupon');

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully'
        ]);
    }

    /**
     * Get cart count (AJAX)
     */
    public function count()
    {
        $count = 0;

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $count = $cart->items()->sum('quantity');
            }
        } else {
            $sessionCart = Session::get('cart', []);
            $count = array_sum(array_column($sessionCart, 'quantity'));
        }

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
