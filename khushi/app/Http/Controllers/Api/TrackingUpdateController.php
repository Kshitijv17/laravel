<?php

namespace App\Http\Controllers\Api;

use App\Models\TrackingUpdate;
use App\Models\CourierTracking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class TrackingUpdateController extends Controller
{
    /**
     * Display a listing of tracking updates
     */
    public function index(Request $request): JsonResponse
    {
        $query = TrackingUpdate::with(['courierTracking.order']);

        if ($request->has('courier_tracking_id')) {
            $query->where('courier_tracking_id', $request->courier_tracking_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('date_from')) {
            $query->whereDate('occurred_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('occurred_at', '<=', $request->date_to);
        }

        $updates = $query->orderBy('occurred_at', 'desc')
                        ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $updates,
            'message' => 'Tracking updates retrieved successfully'
        ]);
    }

    /**
     * Store a newly created tracking update
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'courier_tracking_id' => 'required|exists:courier_trackings,id',
            'status' => 'required|in:pending,picked_up,in_transit,out_for_delivery,delivered,failed,returned',
            'location' => 'nullable|string|max:255',
            'description' => 'required|string',
            'occurred_at' => 'required|date'
        ]);

        $update = TrackingUpdate::create($validated);

        // Update the main courier tracking status
        $courierTracking = $update->courierTracking;
        $courierTracking->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'data' => $update->load(['courierTracking']),
            'message' => 'Tracking update created successfully'
        ], 201);
    }

    /**
     * Display the specified tracking update
     */
    public function show(TrackingUpdate $trackingUpdate): JsonResponse
    {
        $trackingUpdate->load(['courierTracking.order']);

        return response()->json([
            'success' => true,
            'data' => $trackingUpdate,
            'message' => 'Tracking update retrieved successfully'
        ]);
    }

    /**
     * Update the specified tracking update
     */
    public function update(Request $request, TrackingUpdate $trackingUpdate): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,picked_up,in_transit,out_for_delivery,delivered,failed,returned',
            'location' => 'nullable|string|max:255',
            'description' => 'sometimes|string',
            'occurred_at' => 'sometimes|date'
        ]);

        $trackingUpdate->update($validated);

        return response()->json([
            'success' => true,
            'data' => $trackingUpdate->load(['courierTracking']),
            'message' => 'Tracking update updated successfully'
        ]);
    }

    /**
     * Remove the specified tracking update
     */
    public function destroy(TrackingUpdate $trackingUpdate): JsonResponse
    {
        $trackingUpdate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tracking update deleted successfully'
        ]);
    }

    /**
     * Get tracking updates by courier tracking
     */
    public function courierUpdates(Request $request, CourierTracking $courierTracking): JsonResponse
    {
        $updates = $courierTracking->trackingUpdates()
                                  ->orderBy('occurred_at', 'asc')
                                  ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'courier_tracking' => $courierTracking,
                'updates' => $updates,
                'total_updates' => $updates->count()
            ],
            'message' => 'Courier tracking updates retrieved successfully'
        ]);
    }
}
