<?php

namespace App\Http\Controllers\Web;

use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Payment;
use App\Models\Coupon;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display checkout page
     */
    public function checkout()
    {
        $cart = Cart::where('user_id', Auth::id())
            ->with(['items.product'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $addresses = Address::where('user_id', Auth::id())->get();
        $appliedCoupon = Session::get('applied_coupon');

        $subtotal = $cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $discount = 0;
        if ($appliedCoupon) {
            $coupon = (object) $appliedCoupon;
            if ($coupon->type === 'percentage') {
                $discount = ($subtotal * $coupon->value) / 100;
            } else {
                $discount = $coupon->value;
            }
        }

        $shipping = 50; // Fixed shipping cost
        $tax = ($subtotal - $discount) * 0.1; // 10% tax
        $total = $subtotal - $discount + $shipping + $tax;

        return view('web.checkout.index', compact(
            'cart',
            'addresses',
            'subtotal',
            'discount',
            'shipping',
            'tax',
            'total',
            'appliedCoupon'
        ))->with('cartItems', $cart->items);
    }

    /**
     * Process order
     */
    public function processOrder(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
            'same_as_shipping' => 'nullable',
            'billing_first_name' => 'nullable|string|max:255',
            'billing_last_name' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:500',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'nullable|string|max:255',
            'billing_zip_code' => 'nullable|string|max:10',
            'billing_country' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cod,card,paypal,wallet',
            'notes' => 'nullable|string|max:500'
        ]);

        $cart = Cart::where('user_id', Auth::id())
            ->with(['items.product'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        // Create shipping address
        $shippingAddress = Address::create([
            'user_id' => Auth::id(),
            'type' => 'shipping',
            'full_name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address_line1' => $validated['address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'postal_code' => $validated['zip_code'],
            'country' => $validated['country'],
            'is_default' => false
        ]);

        // Create billing address
        $sameAsShipping = !isset($validated['same_as_shipping']) || $validated['same_as_shipping'] === 'on';
        
        if ($sameAsShipping) {
            $billingAddress = Address::create([
                'user_id' => Auth::id(),
                'type' => 'billing',
                'full_name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address_line1' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'postal_code' => $validated['zip_code'],
                'country' => $validated['country'],
                'is_default' => false
            ]);
        } else {
            $billingFirstName = $validated['billing_first_name'] ?? $validated['first_name'];
            $billingLastName = $validated['billing_last_name'] ?? $validated['last_name'];
            
            $billingAddress = Address::create([
                'user_id' => Auth::id(),
                'type' => 'billing',
                'full_name' => $billingFirstName . ' ' . $billingLastName,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address_line1' => $validated['billing_address'] ?? $validated['address'],
                'city' => $validated['billing_city'] ?? $validated['city'],
                'state' => $validated['billing_state'] ?? $validated['state'],
                'postal_code' => $validated['billing_zip_code'] ?? $validated['zip_code'],
                'country' => $validated['billing_country'] ?? $validated['country'],
                'is_default' => false
            ]);
        }

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = $cart->items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            $appliedCoupon = Session::get('applied_coupon');
            $discount = 0;
            $couponId = null;

            if ($appliedCoupon) {
                $coupon = (object) $appliedCoupon;
                $couponId = $coupon->id;
                if ($coupon->type === 'percentage') {
                    $discount = ($subtotal * $coupon->value) / 100;
                } else {
                    $discount = $coupon->value;
                }
            }

            $shipping = 50;
            $tax = ($subtotal - $discount) * 0.1;
            $total = $subtotal - $discount + $shipping + $tax;

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'shipping_amount' => $shipping,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'coupon_id' => $couponId,
                'notes' => $validated['notes']
            ]);

            // Create order items
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                    'total' => $cartItem->product->price * $cartItem->quantity
                ]);

                // Update product stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Create order addresses - split full_name for order_addresses table
            $shippingNameParts = explode(' ', $shippingAddress->full_name, 2);
            $billingNameParts = explode(' ', $billingAddress->full_name, 2);
            
            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'shipping',
                'first_name' => $shippingNameParts[0] ?? '',
                'last_name' => $shippingNameParts[1] ?? '',
                'address_line_1' => $shippingAddress->address_line1,
                'address_line_2' => $shippingAddress->address_line2,
                'city' => $shippingAddress->city,
                'state' => $shippingAddress->state,
                'postal_code' => $shippingAddress->postal_code,
                'country' => $shippingAddress->country,
                'phone' => $shippingAddress->phone
            ]);

            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'billing',
                'first_name' => $billingNameParts[0] ?? '',
                'last_name' => $billingNameParts[1] ?? '',
                'address_line_1' => $billingAddress->address_line1,
                'address_line_2' => $billingAddress->address_line2,
                'city' => $billingAddress->city,
                'state' => $billingAddress->state,
                'postal_code' => $billingAddress->postal_code,
                'country' => $billingAddress->country,
                'phone' => $billingAddress->phone
            ]);

            // If card or paypal selected, redirect to Razorpay initiation page
            if (in_array($validated['payment_method'], ['card', 'paypal'])) {
                DB::commit();

                $redirect = route('payment.initiate', $order->id);
                if (request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Redirecting to secure payment...',
                        'redirect_url' => $redirect,
                    ]);
                }
                return redirect()->to($redirect);
            }

            // Otherwise (COD, wallet) use existing mock processor
            $paymentStatus = $this->processPayment($order, $validated['payment_method']);

            if ($paymentStatus['success']) {
                // Clear cart and coupon
                $cart->items()->delete();
                $cart->delete();
                Session::forget('applied_coupon');

                DB::commit();

                if (request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Order placed successfully!',
                        'redirect_url' => route('order.success', $order->id)
                    ]);
                }
                
                return redirect()->route('order.success', $order->id)
                    ->with('success', 'Order placed successfully!');
            } else {
                DB::rollBack();
                
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment failed: ' . $paymentStatus['message']
                    ], 400);
                }
                
                return redirect()->back()
                    ->with('error', 'Payment failed: ' . $paymentStatus['message']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order processing failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order processing failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Order processing failed. Please try again.');
        }
    }

    /**
     * Order success page
     */
    public function success($orderId)
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['items.product', 'payments'])
            ->findOrFail($orderId);

        return view('web.checkout.success', compact('order'));
    }

    /**
     * Track order
     */
    public function track(Request $request)
    {
        $orderNumber = $request->get('order_number');
        
        if (!$orderNumber) {
            return view('web.order.track');
        }

        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'tracking.updates', 'addresses'])
            ->first();

        if (!$order) {
            return view('web.order.track')
                ->with('error', 'Order not found');
        }

        return view('web.order.track', compact('order'));
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order)
    {
        // Check if order belongs to user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return redirect()->back()
                ->with('error', 'Order cannot be cancelled at this stage');
        }

        $order->update(['status' => 'cancelled']);

        // Restore product stock
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        return redirect()->route('user.orders')
            ->with('success', 'Order cancelled successfully');
    }

    /**
     * Process payment (mock implementation)
     */
    private function processPayment($order, $paymentMethod)
    {
        // Mock payment processing
        $paymentData = [
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'payment_method' => $paymentMethod,
            'status' => 'pending',
            'payment_id' => 'TXN-' . strtoupper(uniqid())
        ];

        // Handle different payment methods
        switch ($paymentMethod) {
            case 'cod':
                $paymentData['status'] = 'pending';
                break;
            case 'card':
            case 'paypal':
                $paymentData['status'] = 'paid';
                break;
            case 'wallet':
                $user = Auth::user();
                if ($user->wallet_balance < $order->total_amount) {
                    return [
                        'success' => false,
                        'message' => 'Insufficient wallet balance'
                    ];
                }
                $user->decrement('wallet_balance', $order->total_amount);
                $paymentData['status'] = 'paid';
                break;
        }

        Payment::create($paymentData);

        return ['success' => true, 'message' => 'Payment processed successfully'];
    }
}
