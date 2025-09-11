<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = Cart::where('user_id', Auth::id())->with('items.product')->first();
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        return view('checkout.index', compact('cart'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'required|string|max:255',
            'shipping_zip' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:255',
            'payment_method' => 'required|string|in:stripe,cod',
        ]);

        $cart = Cart::where('user_id', Auth::id())->with('items.product')->first();
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Calculate totals
        $subtotal = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->final_price;
        });
        $taxRate = 0.08; // 8% tax
        $taxAmount = $subtotal * $taxRate;
        $shippingAmount = 10.00; // Fixed shipping
        $totalAmount = $subtotal + $taxAmount + $shippingAmount;

        if ($request->payment_method === 'stripe') {
            return $this->processStripePayment($request, $cart, $subtotal, $taxAmount, $shippingAmount, $totalAmount);
        } else {
            return $this->processCODOrder($request, $cart, $subtotal, $taxAmount, $shippingAmount, $totalAmount);
        }
    }

    private function processStripePayment($request, $cart, $subtotal, $taxAmount, $shippingAmount, $totalAmount)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = [];
        foreach ($cart->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->product->name,
                        'images' => $item->product->image ? [$item->product->image] : [],
                    ],
                    'unit_amount' => $item->product->final_price * 100, // Convert to cents
                ],
                'quantity' => $item->quantity,
            ];
        }

        // Add shipping as line item
        $lineItems[] = [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'Shipping',
                ],
                'unit_amount' => $shippingAmount * 100,
            ],
            'quantity' => 1,
        ];

        // Add tax as line item
        $lineItems[] = [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'Tax',
                ],
                'unit_amount' => $taxAmount * 100,
            ],
            'quantity' => 1,
        ];

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => url(route('checkout.success', ['session_id' => '{CHECKOUT_SESSION_ID}'], true)),
                'cancel_url' => url(route('checkout.cancel', [], true)),
                'metadata' => [
                    'user_id' => Auth::id(),
                    'shipping_address' => $request->shipping_address,
                    'shipping_city' => $request->shipping_city,
                    'shipping_state' => $request->shipping_state,
                    'shipping_zip' => $request->shipping_zip,
                    'shipping_country' => $request->shipping_country,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'shipping_amount' => $shippingAmount,
                ],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            return back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    private function processCODOrder($request, $cart, $subtotal, $taxAmount, $shippingAmount, $totalAmount)
    {
        return DB::transaction(function () use ($request, $cart, $subtotal, $taxAmount, $shippingAmount, $totalAmount) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $totalAmount,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_zip' => $request->shipping_zip,
                'shipping_country' => $request->shipping_country,
                'billing_address' => $request->shipping_address, // Use shipping address as billing address
                'billing_city' => $request->shipping_city,
                'billing_state' => $request->shipping_state,
                'billing_zip' => $request->shipping_zip,
                'billing_country' => $request->shipping_country,
                'payment_method' => 'cod',
                'payment_status' => 'pending',
                'status' => 'pending',
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->final_price,
                ]);

                // Update stock
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // Clear cart
            $cart->items()->delete();
            $cart->delete();

            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('success', 'Order placed successfully!');
        });
    }

    public function success(Request $request)
    {
        if ($request->has('session_id')) {
            // Handle Stripe success
            return $this->handleStripeSuccess($request->session_id);
        } elseif ($request->has('order')) {
            // Handle COD success
            $order = Order::findOrFail($request->order);
            return view('checkout.success', compact('order'));
        }

        return redirect()->route('home');
    }

    private function handleStripeSuccess($sessionId)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = Session::retrieve($sessionId);
            
            if ($session->payment_status === 'paid') {
                $cart = Cart::where('user_id', $session->metadata->user_id)->with('items.product')->first();
                
                if ($cart && !$cart->items->isEmpty()) {
                    $order = DB::transaction(function () use ($session, $cart) {
                        $order = Order::create([
                            'user_id' => $session->metadata->user_id,
                            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                            'subtotal' => $session->metadata->subtotal,
                            'tax_amount' => $session->metadata->tax_amount,
                            'shipping_amount' => $session->metadata->shipping_amount,
                            'total_amount' => $session->amount_total / 100,
                            'shipping_address' => $session->metadata->shipping_address,
                            'shipping_city' => $session->metadata->shipping_city,
                            'shipping_state' => $session->metadata->shipping_state,
                            'shipping_zip' => $session->metadata->shipping_zip,
                            'shipping_country' => $session->metadata->shipping_country,
                            'billing_address' => $session->metadata->shipping_address, // Use shipping as billing address
                            'billing_city' => $session->metadata->shipping_city,
                            'billing_state' => $session->metadata->shipping_state,
                            'billing_zip' => $session->metadata->shipping_zip,
                            'billing_country' => $session->metadata->shipping_country,
                            'payment_method' => 'stripe',
                            'payment_status' => 'paid',
                            'status' => 'processing',
                            'stripe_session_id' => $sessionId,
                        ]);

                        foreach ($cart->items as $item) {
                            OrderItem::create([
                                'order_id' => $order->id,
                                'product_id' => $item->product_id,
                                'quantity' => $item->quantity,
                                'price' => $item->product->final_price,
                            ]);

                            // Update stock
                            $item->product->decrement('stock_quantity', $item->quantity);
                        }

                        // Clear cart
                        $cart->items()->delete();
                        $cart->delete();

                        return $order;
                    });

                    return view('checkout.success', compact('order'));
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Payment verification failed.');
        }

        return redirect()->route('home');
    }

    public function cancel()
    {
        return redirect()->route('cart.index')->with('error', 'Payment was cancelled.');
    }
}
