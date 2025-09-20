<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display customer's orders
     */
    public function index()
    {
        // Get authenticated user's orders
        $orders = collect(); // Empty collection for now
        
        if (auth()->check()) {
            $orders = Order::with(['items.product', 'shop'])
                          ->where('user_id', auth()->id())
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);
        }

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Show the buy now form
     */
    public function buyNow(Product $product)
    {
        // Check if product is available
        if (!$product->is_active || !$product->shop->is_active || $product->quantity <= 0) {
            return redirect()->back()->with('error', 'Product is not available for purchase.');
        }

        // Load relationships
        $product->load(['category', 'shop']);

        // Calculate final price
        $finalPrice = $product->selling_price && $product->selling_price < $product->price 
                     ? $product->selling_price 
                     : ($product->selling_price ?? $product->price);

        return view('customer.buy-now', compact('product', 'finalPrice'));
    }

    /**
     * Process the buy now order
     */
    public function processBuyNow(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->quantity,
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'payment_method' => 'required|in:cod,card,bank_transfer',
        ]);

        // Check product availability again
        if (!$product->is_active || !$product->shop->is_active || $product->quantity < $request->quantity) {
            return redirect()->back()->with('error', 'Product is not available in the requested quantity.');
        }

        DB::beginTransaction();
        
        try {
            // Calculate prices
            $finalPrice = $product->selling_price && $product->selling_price < $product->price 
                         ? $product->selling_price 
                         : ($product->selling_price ?? $product->price);
            
            $subtotal = $finalPrice * $request->quantity;
            $shippingCost = $subtotal >= 100 ? 0 : 10; // Free shipping over $100
            $tax = $subtotal * 0.08; // 8% tax
            $totalAmount = $subtotal + $shippingCost + $tax;

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => auth()->id(), // null if guest
                'shop_id' => $product->shop_id,
                'status' => 'pending',
                'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'pending',
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'shipping_amount' => $shippingCost,
                'total_amount' => $totalAmount,
                'currency' => 'USD',
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => json_encode([
                    'address' => $request->shipping_address,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'country' => 'USA'
                ]),
                'billing_address' => json_encode([
                    'address' => $request->shipping_address,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'country' => 'USA'
                ]),
                'notes' => $request->notes,
            ]);

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $finalPrice,
                'total' => $subtotal,
            ]);

            // Update product quantity
            $product->decrement('quantity', $request->quantity);

            DB::commit();

            return redirect()->route('customer.order.success', $order)
                           ->with('success', 'Order placed successfully! Order #' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Failed to process order. Please try again.')
                           ->withInput();
        }
    }

    /**
     * Show order success page
     */
    public function success(Order $order)
    {
        // Load relationships
        $order->load(['items.product', 'shop']);

        return view('customer.order-success', compact('order'));
    }

    /**
     * Show order details
     */
    public function show(Order $order)
    {
        // Check if user can view this order (if authenticated)
        if (auth()->check() && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Load relationships
        $order->load(['items.product', 'shop']);

        return view('customer.order-details', compact('order'));
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        $prefix = 'ORD-' . date('Y') . '-';
        $lastOrder = Order::where('order_number', 'like', $prefix . '%')
                         ->orderBy('id', 'desc')
                         ->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
