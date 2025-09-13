<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    /**
     * Display user notifications
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        
        $query = Notification::where('user_id', $userId);

        if ($request->has('read')) {
            if ($request->read) {
                $query->read();
            } else {
                $query->unread();
            }
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->orderBy('created_at', 'desc')
                              ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'message' => 'Notifications retrieved successfully'
        ]);
    }

    /**
     * Store a new notification
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'nullable|array',
            'action_url' => 'nullable|string|max:255'
        ]);

        $notification = Notification::create($validated);

        return response()->json([
            'success' => true,
            'data' => $notification,
            'message' => 'Notification created successfully'
        ], 201);
    }

    /**
     * Display the specified notification
     */
    public function show(Notification $notification): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $notification,
            'message' => 'Notification retrieved successfully'
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'data' => $notification->fresh(),
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(Notification $notification): JsonResponse
    {
        $notification->markAsUnread();

        return response()->json([
            'success' => true,
            'data' => $notification->fresh(),
            'message' => 'Notification marked as unread'
        ]);
    }

    /**
     * Mark all user notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        
        Notification::where('user_id', $userId)
                   ->whereNull('read_at')
                   ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification): JsonResponse
    {
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }

    /**
     * Get notification counts
     */
    public function counts(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        
        $counts = [
            'total' => Notification::where('user_id', $userId)->count(),
            'unread' => Notification::where('user_id', $userId)->unread()->count(),
            'read' => Notification::where('user_id', $userId)->read()->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $counts,
            'message' => 'Notification counts retrieved successfully'
        ]);
    }
}
