<?php

namespace App\Http\Controllers\Api;

use App\Models\OrderAddress;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class OrderAddressController extends Controller
{
    /**
     * Display a listing of order addresses
     */
    public function index(Request $request): JsonResponse
    {
        $query = OrderAddress::with(['order.user', 'address']);

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $orderAddresses = $query->orderBy('created_at', 'desc')
                               ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $orderAddresses,
            'message' => 'Order addresses retrieved successfully'
        ]);
    }

    /**
     * Store a newly created order address
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'address_id' => 'required|exists:addresses,id',
            'type' => 'required|in:billing,shipping'
        ]);

        $orderAddress = OrderAddress::create($validated);

        return response()->json([
            'success' => true,
            'data' => $orderAddress->load(['order', 'address']),
            'message' => 'Order address created successfully'
        ], 201);
    }

    /**
     * Display the specified order address
     */
    public function show(OrderAddress $orderAddress): JsonResponse
    {
        $orderAddress->load(['order.user', 'address']);

        return response()->json([
            'success' => true,
            'data' => $orderAddress,
            'message' => 'Order address retrieved successfully'
        ]);
    }

    /**
     * Update the specified order address
     */
    public function update(Request $request, OrderAddress $orderAddress): JsonResponse
    {
        $validated = $request->validate([
            'address_id' => 'sometimes|exists:addresses,id',
            'type' => 'sometimes|in:billing,shipping'
        ]);

        $orderAddress->update($validated);

        return response()->json([
            'success' => true,
            'data' => $orderAddress->load(['order', 'address']),
            'message' => 'Order address updated successfully'
        ]);
    }

    /**
     * Remove the specified order address
     */
    public function destroy(OrderAddress $orderAddress): JsonResponse
    {
        $orderAddress->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order address deleted successfully'
        ]);
    }

    /**
     * Get order addresses by order
     */
    public function orderAddresses(Request $request, Order $order): JsonResponse
    {
        $addresses = $order->addresses()->with(['address'])->get();

        $grouped = $addresses->groupBy('type');

        return response()->json([
            'success' => true,
            'data' => [
                'shipping_address' => $grouped->get('shipping', collect())->first(),
                'billing_address' => $grouped->get('billing', collect())->first(),
                'all_addresses' => $addresses
            ],
            'message' => 'Order addresses retrieved successfully'
        ]);
    }
}
