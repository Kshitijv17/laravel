<?php

namespace App\Http\Controllers\Api;

use App\Models\CourierTracking;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CourierTrackingController extends Controller
{
    /**
     * Display a listing of courier trackings
     */
    public function index(Request $request): JsonResponse
    {
        $query = CourierTracking::with(['order.user']);

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->has('courier_company')) {
            $query->where('courier_company', $request->courier_company);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('tracking_number', 'like', '%' . $request->search . '%');
        }

        $trackings = $query->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $trackings,
            'message' => 'Courier trackings retrieved successfully'
        ]);
    }

    /**
     * Store a newly created courier tracking
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'courier_company' => 'required|string|max:255',
            'tracking_number' => 'required|string|unique:courier_trackings,tracking_number|max:255',
            'status' => 'required|in:pending,picked_up,in_transit,out_for_delivery,delivered,failed,returned',
            'estimated_delivery' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $tracking = CourierTracking::create($validated);

        // Update order status to shipped
        $tracking->order->update(['status' => 'shipped']);

        return response()->json([
            'success' => true,
            'data' => $tracking->load(['order']),
            'message' => 'Courier tracking created successfully'
        ], 201);
    }

    /**
     * Display the specified courier tracking
     */
    public function show(CourierTracking $courierTracking): JsonResponse
    {
        $courierTracking->load(['order.user', 'trackingUpdates']);

        return response()->json([
            'success' => true,
            'data' => $courierTracking,
            'message' => 'Courier tracking retrieved successfully'
        ]);
    }

    /**
     * Update the specified courier tracking
     */
    public function update(Request $request, CourierTracking $courierTracking): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,picked_up,in_transit,out_for_delivery,delivered,failed,returned',
            'estimated_delivery' => 'nullable|date',
            'actual_delivery' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $courierTracking->update($validated);

        // Update order status based on tracking status
        if (isset($validated['status'])) {
            $this->updateOrderStatus($courierTracking, $validated['status']);
        }

        return response()->json([
            'success' => true,
            'data' => $courierTracking->load(['order']),
            'message' => 'Courier tracking updated successfully'
        ]);
    }

    /**
     * Track by tracking number
     */
    public function trackByNumber(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tracking_number' => 'required|string'
        ]);

        $tracking = CourierTracking::with(['order.user', 'trackingUpdates'])
                                  ->where('tracking_number', $validated['tracking_number'])
                                  ->first();

        if (!$tracking) {
            return response()->json([
                'success' => false,
                'message' => 'Tracking number not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tracking,
            'message' => 'Tracking information retrieved successfully'
        ]);
    }

    /**
     * Add tracking update
     */
    public function addUpdate(Request $request, CourierTracking $courierTracking): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,picked_up,in_transit,out_for_delivery,delivered,failed,returned',
            'location' => 'nullable|string|max:255',
            'description' => 'required|string',
            'occurred_at' => 'required|date'
        ]);

        $update = $courierTracking->trackingUpdates()->create($validated);

        // Update main tracking status
        $courierTracking->update(['status' => $validated['status']]);

        // Update order status
        $this->updateOrderStatus($courierTracking, $validated['status']);

        return response()->json([
            'success' => true,
            'data' => $update,
            'message' => 'Tracking update added successfully'
        ], 201);
    }

    /**
     * Get order tracking
     */
    public function orderTracking(Request $request, Order $order): JsonResponse
    {
        $tracking = $order->courierTracking()->with(['trackingUpdates'])->first();

        if (!$tracking) {
            return response()->json([
                'success' => false,
                'message' => 'No tracking information found for this order'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tracking,
            'message' => 'Order tracking retrieved successfully'
        ]);
    }

    /**
     * Get tracking statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_shipments' => CourierTracking::count(),
            'pending_shipments' => CourierTracking::where('status', 'pending')->count(),
            'in_transit_shipments' => CourierTracking::where('status', 'in_transit')->count(),
            'delivered_shipments' => CourierTracking::where('status', 'delivered')->count(),
            'failed_shipments' => CourierTracking::where('status', 'failed')->count(),
            'average_delivery_time' => CourierTracking::whereNotNull('actual_delivery')
                                                    ->selectRaw('AVG(DATEDIFF(actual_delivery, created_at)) as avg_days')
                                                    ->value('avg_days'),
            'courier_performance' => CourierTracking::selectRaw('courier_company, COUNT(*) as total, 
                                                               SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered')
                                                   ->groupBy('courier_company')
                                                   ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Tracking statistics retrieved successfully'
        ]);
    }

    /**
     * Update order status based on tracking status
     */
    private function updateOrderStatus(CourierTracking $tracking, string $trackingStatus): void
    {
        $orderStatus = match($trackingStatus) {
            'delivered' => 'delivered',
            'failed', 'returned' => 'cancelled',
            default => $tracking->order->status
        };

        if ($orderStatus !== $tracking->order->status) {
            $tracking->order->update(['status' => $orderStatus]);
        }
    }
}
