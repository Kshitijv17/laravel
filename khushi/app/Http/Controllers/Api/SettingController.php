<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    /**
     * Display a listing of settings
     */
    public function index(Request $request): JsonResponse
    {
        $query = Setting::query();

        if ($request->has('group')) {
            $query->where('group', $request->group);
        }

        if ($request->has('search')) {
            $query->where('key', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $settings = $query->orderBy('group')
                         ->orderBy('key')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $settings,
            'message' => 'Settings retrieved successfully'
        ]);
    }

    /**
     * Store a newly created setting
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:settings,key|max:255',
            'value' => 'required|string',
            'group' => 'required|string|max:255',
            'type' => 'required|in:string,integer,boolean,json,text',
            'description' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

        $setting = Setting::create($validated);

        return response()->json([
            'success' => true,
            'data' => $setting,
            'message' => 'Setting created successfully'
        ], 201);
    }

    /**
     * Display the specified setting
     */
    public function show(Setting $setting): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $setting,
            'message' => 'Setting retrieved successfully'
        ]);
    }

    /**
     * Update the specified setting
     */
    public function update(Request $request, Setting $setting): JsonResponse
    {
        $validated = $request->validate([
            'value' => 'required|string',
            'description' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

        $setting->update($validated);

        return response()->json([
            'success' => true,
            'data' => $setting,
            'message' => 'Setting updated successfully'
        ]);
    }

    /**
     * Remove the specified setting
     */
    public function destroy(Setting $setting): JsonResponse
    {
        $setting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Setting deleted successfully'
        ]);
    }

    /**
     * Get settings by group
     */
    public function getByGroup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group' => 'required|string'
        ]);

        $settings = Setting::where('group', $validated['group'])
                          ->orderBy('key')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $settings,
            'message' => 'Settings retrieved successfully'
        ]);
    }

    /**
     * Get public settings
     */
    public function getPublic(): JsonResponse
    {
        $settings = Setting::where('is_public', true)
                          ->orderBy('group')
                          ->orderBy('key')
                          ->get()
                          ->groupBy('group');

        return response()->json([
            'success' => true,
            'data' => $settings,
            'message' => 'Public settings retrieved successfully'
        ]);
    }

    /**
     * Bulk update settings
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|exists:settings,key',
            'settings.*.value' => 'required|string'
        ]);

        $updated = [];
        foreach ($validated['settings'] as $settingData) {
            $setting = Setting::where('key', $settingData['key'])->first();
            if ($setting) {
                $setting->update(['value' => $settingData['value']]);
                $updated[] = $setting;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $updated,
            'message' => 'Settings updated successfully'
        ]);
    }

    /**
     * Get all setting groups
     */
    public function getGroups(): JsonResponse
    {
        $groups = Setting::select('group')
                        ->distinct()
                        ->orderBy('group')
                        ->pluck('group');

        return response()->json([
            'success' => true,
            'data' => $groups,
            'message' => 'Setting groups retrieved successfully'
        ]);
    }
}
