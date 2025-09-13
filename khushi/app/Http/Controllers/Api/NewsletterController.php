<?php

namespace App\Http\Controllers\Api;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class NewsletterController extends Controller
{
    /**
     * Display a listing of newsletter subscriptions
     */
    public function index(Request $request): JsonResponse
    {
        $query = Newsletter::query();

        if ($request->has('active')) {
            $query->active();
        }

        if ($request->has('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $newsletters = $query->orderBy('created_at', 'desc')
                            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $newsletters,
            'message' => 'Newsletter subscriptions retrieved successfully'
        ]);
    }

    /**
     * Store a newly created newsletter subscription
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:newsletters,email|max:255',
            'name' => 'nullable|string|max:255',
            'preferences' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $newsletter = Newsletter::create([
            ...$validated,
            'subscription_token' => bin2hex(random_bytes(32))
        ]);

        return response()->json([
            'success' => true,
            'data' => $newsletter,
            'message' => 'Newsletter subscription created successfully'
        ], 201);
    }

    /**
     * Display the specified newsletter subscription
     */
    public function show(Newsletter $newsletter): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $newsletter,
            'message' => 'Newsletter subscription retrieved successfully'
        ]);
    }

    /**
     * Update the specified newsletter subscription
     */
    public function update(Request $request, Newsletter $newsletter): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'preferences' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $newsletter->update($validated);

        return response()->json([
            'success' => true,
            'data' => $newsletter,
            'message' => 'Newsletter subscription updated successfully'
        ]);
    }

    /**
     * Remove the specified newsletter subscription
     */
    public function destroy(Newsletter $newsletter): JsonResponse
    {
        $newsletter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Newsletter subscription deleted successfully'
        ]);
    }

    /**
     * Subscribe to newsletter
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255'
        ]);

        $existing = Newsletter::where('email', $validated['email'])->first();

        if ($existing) {
            if ($existing->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is already subscribed to newsletter'
                ], 400);
            } else {
                $existing->update(['is_active' => true]);
                return response()->json([
                    'success' => true,
                    'data' => $existing,
                    'message' => 'Newsletter subscription reactivated successfully'
                ]);
            }
        }

        $newsletter = Newsletter::create([
            ...$validated,
            'subscription_token' => bin2hex(random_bytes(32)),
            'is_active' => true
        ]);

        return response()->json([
            'success' => true,
            'data' => $newsletter,
            'message' => 'Successfully subscribed to newsletter'
        ], 201);
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'token' => 'nullable|string'
        ]);

        $query = Newsletter::where('email', $validated['email']);

        if (isset($validated['token'])) {
            $query->where('subscription_token', $validated['token']);
        }

        $newsletter = $query->first();

        if (!$newsletter) {
            return response()->json([
                'success' => false,
                'message' => 'Newsletter subscription not found'
            ], 404);
        }

        $newsletter->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully unsubscribed from newsletter'
        ]);
    }

    /**
     * Get newsletter statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_subscriptions' => Newsletter::count(),
            'active_subscriptions' => Newsletter::active()->count(),
            'inactive_subscriptions' => Newsletter::inactive()->count(),
            'today_subscriptions' => Newsletter::whereDate('created_at', today())->count(),
            'this_week_subscriptions' => Newsletter::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month_subscriptions' => Newsletter::whereMonth('created_at', now()->month)->count(),
            'subscription_growth' => Newsletter::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                              ->whereDate('created_at', '>=', now()->subDays(30))
                                              ->groupBy('date')
                                              ->orderBy('date')
                                              ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Newsletter statistics retrieved successfully'
        ]);
    }

    /**
     * Export newsletter subscribers
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'active_only' => 'boolean'
        ]);

        $query = Newsletter::select(['email', 'name', 'created_at', 'is_active']);

        if ($validated['active_only'] ?? false) {
            $query->active();
        }

        $subscribers = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $subscribers,
            'message' => 'Newsletter subscribers exported successfully'
        ]);
    }
}
