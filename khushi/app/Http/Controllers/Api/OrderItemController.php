<?php

namespace App\Http\Controllers\Api;

use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class OrderItemController extends Controller
{
    /**
     * Display a listing of order items
     */
    public function index(Request $request): JsonResponse
    {
        $query = OrderItem::with(['order.user', 'product']);

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $orderItems = $query->orderBy('created_at', 'desc')
                           ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $orderItems,
            'message' => 'Order items retrieved successfully'
        ]);
    }

    /**
     * Store a newly created order item
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0'
        ]);

        $orderItem = OrderItem::create($validated);

        return response()->json([
            'success' => true,
            'data' => $orderItem->load(['order', 'product']),
            'message' => 'Order item created successfully'
        ], 201);
    }

    /**
     * Display the specified order item
     */
    public function show(OrderItem $orderItem): JsonResponse
    {
        $orderItem->load(['order.user', 'product']);

        return response()->json([
            'success' => true,
            'data' => $orderItem,
            'message' => 'Order item retrieved successfully'
        ]);
    }

    /**
     * Update the specified order item
     */
    public function update(Request $request, OrderItem $orderItem): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'sometimes|integer|min:1',
            'price' => 'sometimes|numeric|min:0'
        ]);

        $orderItem->update($validated);

        return response()->json([
            'success' => true,
            'data' => $orderItem->load(['order', 'product']),
            'message' => 'Order item updated successfully'
        ]);
    }

    /**
     * Remove the specified order item
     */
    public function destroy(OrderItem $orderItem): JsonResponse
    {
        $orderItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order item deleted successfully'
        ]);
    }

    /**
     * Get order items by order
     */
    public function orderItems(Request $request, Order $order): JsonResponse
    {
        $items = $order->items()->with(['product'])->get();

        $summary = [
            'total_items' => $items->count(),
            'total_quantity' => $items->sum('quantity'),
            'total_amount' => $items->sum(function ($item) {
                return $item->quantity * $item->price;
            })
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'summary' => $summary
            ],
            'message' => 'Order items retrieved successfully'
        ]);
    }
}
