<?php

namespace App\Http\Controllers\Api;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ReferralController extends Controller
{
    /**
     * Display a listing of referrals
     */
    public function index(Request $request): JsonResponse
    {
        $query = Referral::with(['referrer', 'referred', 'rewards']);

        if ($request->has('referrer_id')) {
            $query->where('referrer_id', $request->referrer_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $referrals = $query->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $referrals,
            'message' => 'Referrals retrieved successfully'
        ]);
    }

    /**
     * Store a newly created referral
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'referrer_id' => 'required|exists:users,id',
            'referred_id' => 'required|exists:users,id|different:referrer_id',
            'referral_code' => 'required|string|exists:users,referral_code'
        ]);

        // Check if referral already exists
        $existing = Referral::where('referrer_id', $validated['referrer_id'])
                           ->where('referred_id', $validated['referred_id'])
                           ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Referral already exists'
            ], 400);
        }

        $referral = Referral::create($validated);
        $referral->load(['referrer', 'referred']);

        return response()->json([
            'success' => true,
            'data' => $referral,
            'message' => 'Referral created successfully'
        ], 201);
    }

    /**
     * Display the specified referral
     */
    public function show(Referral $referral): JsonResponse
    {
        $referral->load(['referrer', 'referred', 'rewards']);

        return response()->json([
            'success' => true,
            'data' => $referral,
            'message' => 'Referral retrieved successfully'
        ]);
    }

    /**
     * Update the specified referral
     */
    public function update(Request $request, Referral $referral): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,completed,cancelled',
            'completed_at' => 'nullable|date'
        ]);

        $referral->update($validated);
        $referral->load(['referrer', 'referred', 'rewards']);

        return response()->json([
            'success' => true,
            'data' => $referral,
            'message' => 'Referral updated successfully'
        ]);
    }

    /**
     * Complete referral and award rewards
     */
    public function complete(Referral $referral): JsonResponse
    {
        if ($referral->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Referral already completed'
            ], 400);
        }

        $referral->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        // Award referral rewards (this would typically be handled by a job/event)
        // For now, we'll just mark it as completed

        return response()->json([
            'success' => true,
            'data' => $referral,
            'message' => 'Referral completed successfully'
        ]);
    }

    /**
     * Get user referrals
     */
    public function userReferrals(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        
        $query = Referral::where('referrer_id', $userId)
                        ->with(['referred', 'rewards']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $referrals = $query->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $referrals,
            'message' => 'User referrals retrieved successfully'
        ]);
    }

    /**
     * Get referral statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = Referral::query();

        if ($request->has('user_id')) {
            $query->where('referrer_id', $request->user_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total_referrals' => $query->count(),
            'pending_referrals' => $query->where('status', 'pending')->count(),
            'completed_referrals' => $query->where('status', 'completed')->count(),
            'cancelled_referrals' => $query->where('status', 'cancelled')->count(),
            'total_rewards' => $query->with('rewards')->get()->sum('total_rewards'),
            'conversion_rate' => $query->count() > 0 ? 
                ($query->where('status', 'completed')->count() / $query->count()) * 100 : 0
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Referral statistics retrieved successfully'
        ]);
    }

    /**
     * Validate referral code
     */
    public function validateCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'referral_code' => 'required|string'
        ]);

        $user = User::where('referral_code', $validated['referral_code'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid referral code'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'referrer' => $user,
                'valid' => true
            ],
            'message' => 'Referral code is valid'
        ]);
    }
}
