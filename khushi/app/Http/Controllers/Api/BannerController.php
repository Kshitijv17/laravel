<?php

namespace App\Http\Controllers\Api;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class BannerController extends Controller
{
    /**
     * Display a listing of banners
     */
    public function index(Request $request): JsonResponse
    {
        $query = Banner::query();

        if ($request->has('active')) {
            $query->active();
        }

        if ($request->has('position')) {
            $query->where('position', $request->position);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $banners = $query->orderBy('sort_order')
                        ->orderBy('created_at', 'desc')
                        ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $banners,
            'message' => 'Banners retrieved successfully'
        ]);
    }

    /**
     * Store a newly created banner
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|string|max:255',
            'link_url' => 'nullable|url|max:255',
            'position' => 'required|in:header,footer,sidebar,main,popup',
            'type' => 'required|in:promotional,informational,advertisement',
            'sort_order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        $banner = Banner::create($validated);

        return response()->json([
            'success' => true,
            'data' => $banner,
            'message' => 'Banner created successfully'
        ], 201);
    }

    /**
     * Display the specified banner
     */
    public function show(Banner $banner): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $banner,
            'message' => 'Banner retrieved successfully'
        ]);
    }

    /**
     * Update the specified banner
     */
    public function update(Request $request, Banner $banner): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'sometimes|string|max:255',
            'link_url' => 'nullable|url|max:255',
            'position' => 'sometimes|in:header,footer,sidebar,main,popup',
            'type' => 'sometimes|in:promotional,informational,advertisement',
            'sort_order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        $banner->update($validated);

        return response()->json([
            'success' => true,
            'data' => $banner,
            'message' => 'Banner updated successfully'
        ]);
    }

    /**
     * Remove the specified banner
     */
    public function destroy(Banner $banner): JsonResponse
    {
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully'
        ]);
    }

    /**
     * Get active banners by position
     */
    public function getByPosition(Request $request): JsonResponse
    {
        $position = $request->get('position', 'header');
        
        $banners = Banner::active()
                        ->where('position', $position)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $banners,
            'message' => 'Banners retrieved successfully'
        ]);
    }

    /**
     * Track banner click
     */
    public function trackClick(Request $request, Banner $banner): JsonResponse
    {
        $banner->increment('click_count');

        return response()->json([
            'success' => true,
            'message' => 'Banner click tracked successfully'
        ]);
    }

    /**
     * Get banner statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_banners' => Banner::count(),
            'active_banners' => Banner::active()->count(),
            'inactive_banners' => Banner::inactive()->count(),
            'expired_banners' => Banner::where('end_date', '<', now())->count(),
            'position_breakdown' => Banner::selectRaw('position, COUNT(*) as count')
                                         ->groupBy('position')
                                         ->get(),
            'type_breakdown' => Banner::selectRaw('type, COUNT(*) as count')
                                     ->groupBy('type')
                                     ->get(),
            'most_clicked' => Banner::orderBy('click_count', 'desc')
                                   ->limit(5)
                                   ->get(['id', 'title', 'click_count'])
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Banner statistics retrieved successfully'
        ]);
    }
}
