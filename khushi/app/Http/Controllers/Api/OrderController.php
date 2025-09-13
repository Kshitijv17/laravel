<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'items.product', 'payments', 'addresses']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')
                       ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'Orders retrieved successfully'
        ]);
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'nullable|exists:addresses,id',
            'payment_method' => 'required|string',
            'coupon_code' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Get user's cart
            $cart = Cart::where('user_id', $validated['user_id'])->with('items.product')->first();
            
            if (!$cart || $cart->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Calculate total
            $subtotal = $cart->items->sum(function($item) {
                return $item->quantity * $item->product->final_price;
            });

            // Create order
            $order = Order::create([
                'user_id' => $validated['user_id'],
                'order_number' => 'ORD-' . time() . '-' . rand(1000, 9999),
                'total_amount' => $subtotal,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'ordered_at' => now()
            ]);

            // Create order items
            foreach ($cart->items as $cartItem) {
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->final_price,
                    'total' => $cartItem->quantity * $cartItem->product->final_price
                ]);
            }

            // Clear cart
            $cart->items()->delete();

            DB::commit();

            $order->load(['items.product', 'user']);

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Order created successfully'
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['user', 'items.product', 'payments', 'transactions', 'returnRequests']);

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order retrieved successfully'
        ]);
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string'
        ]);

        $order->update($validated);
        $order->load(['items.product', 'user']);

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order updated successfully'
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order): JsonResponse
    {
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled'
            ], 400);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully'
        ]);
    }

    /**
     * Get user orders
     */
    public function userOrders(Request $request, $userId): JsonResponse
    {
        $orders = Order::where('user_id', $userId)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'User orders retrieved successfully'
        ]);
    }

    /**
     * Get order statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::pending()->count(),
            'completed_orders' => Order::completed()->count(),
            'total_revenue' => Order::completed()->sum('total_amount'),
            'average_order_value' => Order::completed()->avg('total_amount')
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Order statistics retrieved successfully'
        ]);
    }
}
