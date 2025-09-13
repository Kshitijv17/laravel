<?php

namespace App\Http\Controllers\Api;

use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class UserProfileController extends Controller
{
    /**
     * Display a listing of user profiles
     */
    public function index(Request $request): JsonResponse
    {
        $query = UserProfile::with(['user']);

        if ($request->has('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('country')) {
            $query->where('country', $request->country);
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        $profiles = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $profiles,
            'message' => 'User profiles retrieved successfully'
        ]);
    }

    /**
     * Store a newly created user profile
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:user_profiles,user_id',
            'avatar' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'timezone' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:10',
            'social_links' => 'nullable|array',
            'preferences' => 'nullable|array'
        ]);

        $profile = UserProfile::create($validated);

        return response()->json([
            'success' => true,
            'data' => $profile->load(['user']),
            'message' => 'User profile created successfully'
        ], 201);
    }

    /**
     * Display the specified user profile
     */
    public function show(UserProfile $userProfile): JsonResponse
    {
        $userProfile->load(['user']);

        return response()->json([
            'success' => true,
            'data' => $userProfile,
            'message' => 'User profile retrieved successfully'
        ]);
    }

    /**
     * Update the specified user profile
     */
    public function update(Request $request, UserProfile $userProfile): JsonResponse
    {
        $validated = $request->validate([
            'avatar' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'timezone' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:10',
            'social_links' => 'nullable|array',
            'preferences' => 'nullable|array'
        ]);

        $userProfile->update($validated);

        return response()->json([
            'success' => true,
            'data' => $userProfile->load(['user']),
            'message' => 'User profile updated successfully'
        ]);
    }

    /**
     * Remove the specified user profile
     */
    public function destroy(UserProfile $userProfile): JsonResponse
    {
        $userProfile->delete();

        return response()->json([
            'success' => true,
            'message' => 'User profile deleted successfully'
        ]);
    }

    /**
     * Get profile by user ID
     */
    public function getByUser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $profile = UserProfile::with(['user'])
                             ->where('user_id', $validated['user_id'])
                             ->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'User profile not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $profile,
            'message' => 'User profile retrieved successfully'
        ]);
    }

    /**
     * Update user preferences
     */
    public function updatePreferences(Request $request, UserProfile $userProfile): JsonResponse
    {
        $validated = $request->validate([
            'preferences' => 'required|array',
            'preferences.notifications' => 'nullable|array',
            'preferences.privacy' => 'nullable|array',
            'preferences.display' => 'nullable|array'
        ]);

        $currentPreferences = $userProfile->preferences ?? [];
        $newPreferences = array_merge($currentPreferences, $validated['preferences']);

        $userProfile->update(['preferences' => $newPreferences]);

        return response()->json([
            'success' => true,
            'data' => $userProfile,
            'message' => 'User preferences updated successfully'
        ]);
    }
}
