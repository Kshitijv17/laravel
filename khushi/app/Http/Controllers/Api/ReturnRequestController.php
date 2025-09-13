<?php

namespace App\Http\Controllers\Api;

use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ReturnRequestController extends Controller
{
    /**
     * Display a listing of return requests
     */
    public function index(Request $request): JsonResponse
    {
        $query = ReturnRequest::with(['user', 'order', 'orderItem.product']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('reason')) {
            $query->where('reason', $request->reason);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $returnRequests = $query->orderBy('created_at', 'desc')
                               ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $returnRequests,
            'message' => 'Return requests retrieved successfully'
        ]);
    }

    /**
     * Store a newly created return request
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_id' => 'required|exists:orders,id',
            'order_item_id' => 'required|exists:order_items,id',
            'reason' => 'required|in:defective,wrong_item,not_as_described,damaged,changed_mind',
            'description' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'attachments' => 'nullable|array'
        ]);

        // Generate return number
        $validated['return_number'] = 'RET-' . strtoupper(uniqid());
        $validated['status'] = 'pending';

        $returnRequest = ReturnRequest::create($validated);
        $returnRequest->load(['user', 'order', 'orderItem.product']);

        return response()->json([
            'success' => true,
            'data' => $returnRequest,
            'message' => 'Return request created successfully'
        ], 201);
    }

    /**
     * Display the specified return request
     */
    public function show(ReturnRequest $returnRequest): JsonResponse
    {
        $returnRequest->load(['user', 'order', 'orderItem.product']);

        return response()->json([
            'success' => true,
            'data' => $returnRequest,
            'message' => 'Return request retrieved successfully'
        ]);
    }

    /**
     * Update the specified return request
     */
    public function update(Request $request, ReturnRequest $returnRequest): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,approved,rejected,processing,completed',
            'admin_notes' => 'nullable|string',
            'refund_amount' => 'nullable|numeric|min:0',
            'resolution' => 'nullable|string'
        ]);

        // Set timestamps based on status
        if (isset($validated['status'])) {
            switch ($validated['status']) {
                case 'approved':
                    if (!$returnRequest->approved_at) {
                        $validated['approved_at'] = now();
                    }
                    break;
                case 'rejected':
                    if (!$returnRequest->rejected_at) {
                        $validated['rejected_at'] = now();
                    }
                    break;
                case 'completed':
                    if (!$returnRequest->completed_at) {
                        $validated['completed_at'] = now();
                    }
                    break;
            }
        }

        $returnRequest->update($validated);
        $returnRequest->load(['user', 'order', 'orderItem.product']);

        return response()->json([
            'success' => true,
            'data' => $returnRequest,
            'message' => 'Return request updated successfully'
        ]);
    }

    /**
     * Approve return request
     */
    public function approve(Request $request, ReturnRequest $returnRequest): JsonResponse
    {
        $validated = $request->validate([
            'refund_amount' => 'required|numeric|min:0',
            'admin_notes' => 'nullable|string'
        ]);

        $returnRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
            'refund_amount' => $validated['refund_amount'],
            'admin_notes' => $validated['admin_notes'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'data' => $returnRequest,
            'message' => 'Return request approved successfully'
        ]);
    }

    /**
     * Reject return request
     */
    public function reject(Request $request, ReturnRequest $returnRequest): JsonResponse
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string'
        ]);

        $returnRequest->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'admin_notes' => $validated['admin_notes']
        ]);

        return response()->json([
            'success' => true,
            'data' => $returnRequest,
            'message' => 'Return request rejected successfully'
        ]);
    }

    /**
     * Complete return request
     */
    public function complete(ReturnRequest $returnRequest): JsonResponse
    {
        if ($returnRequest->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Return request must be approved before completion'
            ], 400);
        }

        $returnRequest->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $returnRequest,
            'message' => 'Return request completed successfully'
        ]);
    }

    /**
     * Get return request statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = ReturnRequest::query();

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total_requests' => $query->count(),
            'pending_requests' => $query->where('status', 'pending')->count(),
            'approved_requests' => $query->where('status', 'approved')->count(),
            'rejected_requests' => $query->where('status', 'rejected')->count(),
            'completed_requests' => $query->where('status', 'completed')->count(),
            'total_refund_amount' => $query->whereNotNull('refund_amount')->sum('refund_amount'),
            'approval_rate' => $query->count() > 0 ? 
                ($query->where('status', 'approved')->count() / $query->count()) * 100 : 0
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Return request statistics retrieved successfully'
        ]);
    }
}
