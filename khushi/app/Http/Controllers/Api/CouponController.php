<?php

namespace App\Http\Controllers\Api;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CouponController extends Controller
{
    /**
     * Display a listing of coupons
     */
    public function index(Request $request): JsonResponse
    {
        $query = Coupon::query();

        if ($request->has('active')) {
            $query->active();
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $coupons = $query->orderBy('created_at', 'desc')
                        ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $coupons,
            'message' => 'Coupons retrieved successfully'
        ]);
    }

    /**
     * Store a newly created coupon
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        $coupon = Coupon::create($validated);

        return response()->json([
            'success' => true,
            'data' => $coupon,
            'message' => 'Coupon created successfully'
        ], 201);
    }

    /**
     * Display the specified coupon
     */
    public function show(Coupon $coupon): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $coupon,
            'message' => 'Coupon retrieved successfully'
        ]);
    }

    /**
     * Update the specified coupon
     */
    public function update(Request $request, Coupon $coupon): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'sometimes|string|unique:coupons,code,' . $coupon->id,
            'type' => 'sometimes|in:percentage,fixed',
            'value' => 'sometimes|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        $coupon->update($validated);

        return response()->json([
            'success' => true,
            'data' => $coupon,
            'message' => 'Coupon updated successfully'
        ]);
    }

    /**
     * Remove the specified coupon
     */
    public function destroy(Coupon $coupon): JsonResponse
    {
        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully'
        ]);
    }

    /**
     * Validate coupon code
     */
    public function validate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'order_amount' => 'required|numeric|min:0'
        ]);

        $coupon = Coupon::where('code', $validated['code'])->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ], 400);
        }

        if (!$coupon->is_valid) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is expired or inactive'
            ], 400);
        }

        if ($coupon->minimum_amount && $validated['order_amount'] < $coupon->minimum_amount) {
            return response()->json([
                'success' => false,
                'message' => "Minimum order amount is {$coupon->minimum_amount}"
            ], 400);
        }

        $discountAmount = $coupon->getDiscountAmountAttribute($validated['order_amount']);

        return response()->json([
            'success' => true,
            'data' => [
                'coupon' => $coupon,
                'discount_amount' => $discountAmount,
                'final_amount' => $validated['order_amount'] - $discountAmount
            ],
            'message' => 'Coupon is valid'
        ]);
    }

    /**
     * Apply coupon to order
     */
    public function apply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'order_id' => 'required|exists:orders,id'
        ]);

        $coupon = Coupon::where('code', $validated['code'])->first();

        if (!$coupon || !$coupon->is_valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon'
            ], 400);
        }

        // Increment usage count
        $coupon->increment('used_count');

        return response()->json([
            'success' => true,
            'data' => $coupon,
            'message' => 'Coupon applied successfully'
        ]);
    }
}
