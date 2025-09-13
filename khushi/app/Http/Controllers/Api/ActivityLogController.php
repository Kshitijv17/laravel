<?php

namespace App\Http\Controllers\Api;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::with(['user']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->orderBy('created_at', 'desc')
                     ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $logs,
            'message' => 'Activity logs retrieved successfully'
        ]);
    }

    /**
     * Store a newly created activity log
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'action' => 'required|string|max:255',
            'model_type' => 'nullable|string|max:255',
            'model_id' => 'nullable|integer',
            'description' => 'required|string',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string',
            'properties' => 'nullable|array'
        ]);

        $log = ActivityLog::create($validated);

        return response()->json([
            'success' => true,
            'data' => $log,
            'message' => 'Activity log created successfully'
        ], 201);
    }

    /**
     * Display the specified activity log
     */
    public function show(ActivityLog $activityLog): JsonResponse
    {
        $activityLog->load(['user']);

        return response()->json([
            'success' => true,
            'data' => $activityLog,
            'message' => 'Activity log retrieved successfully'
        ]);
    }

    /**
     * Get user activity logs
     */
    public function userLogs(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $logs = ActivityLog::with(['user'])
                          ->where('user_id', $validated['user_id'])
                          ->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $logs,
            'message' => 'User activity logs retrieved successfully'
        ]);
    }

    /**
     * Get activity statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_activities' => ActivityLog::count(),
            'today_activities' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week_activities' => ActivityLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month_activities' => ActivityLog::whereMonth('created_at', now()->month)->count(),
            'top_actions' => ActivityLog::selectRaw('action, COUNT(*) as count')
                                      ->groupBy('action')
                                      ->orderBy('count', 'desc')
                                      ->limit(10)
                                      ->get(),
            'active_users_today' => ActivityLog::whereDate('created_at', today())
                                              ->distinct('user_id')
                                              ->count('user_id')
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Activity statistics retrieved successfully'
        ]);
    }

    /**
     * Clear old activity logs
     */
    public function clearOld(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $cutoffDate = now()->subDays($validated['days']);
        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        return response()->json([
            'success' => true,
            'data' => ['deleted_count' => $deletedCount],
            'message' => "Deleted {$deletedCount} old activity logs"
        ]);
    }
}
