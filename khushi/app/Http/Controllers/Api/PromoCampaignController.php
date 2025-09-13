<?php

namespace App\Http\Controllers\Api;

use App\Models\PromoCampaign;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class PromoCampaignController extends Controller
{
    /**
     * Display a listing of promo campaigns
     */
    public function index(Request $request): JsonResponse
    {
        $query = PromoCampaign::query();

        if ($request->has('active')) {
            $query->active();
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $campaigns = $query->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $campaigns,
            'message' => 'Promo campaigns retrieved successfully'
        ]);
    }

    /**
     * Store a newly created promo campaign
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount,buy_x_get_y,free_shipping',
            'discount_value' => 'nullable|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'applicable_products' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $campaign = PromoCampaign::create($validated);

        return response()->json([
            'success' => true,
            'data' => $campaign,
            'message' => 'Promo campaign created successfully'
        ], 201);
    }

    /**
     * Display the specified promo campaign
     */
    public function show(PromoCampaign $promoCampaign): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $promoCampaign,
            'message' => 'Promo campaign retrieved successfully'
        ]);
    }

    /**
     * Update the specified promo campaign
     */
    public function update(Request $request, PromoCampaign $promoCampaign): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:percentage,fixed_amount,buy_x_get_y,free_shipping',
            'discount_value' => 'nullable|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'applicable_products' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $promoCampaign->update($validated);

        return response()->json([
            'success' => true,
            'data' => $promoCampaign,
            'message' => 'Promo campaign updated successfully'
        ]);
    }

    /**
     * Remove the specified promo campaign
     */
    public function destroy(PromoCampaign $promoCampaign): JsonResponse
    {
        $promoCampaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Promo campaign deleted successfully'
        ]);
    }

    /**
     * Check if product is applicable for campaign
     */
    public function checkApplicability(Request $request, PromoCampaign $promoCampaign): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $isApplicable = $promoCampaign->isProductApplicable($validated['product_id']);

        return response()->json([
            'success' => true,
            'data' => [
                'campaign' => $promoCampaign,
                'product_id' => $validated['product_id'],
                'is_applicable' => $isApplicable
            ],
            'message' => 'Product applicability checked successfully'
        ]);
    }

    /**
     * Get active campaigns for products
     */
    public function getActiveCampaigns(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id'
        ]);

        $campaigns = PromoCampaign::active()->get();
        $applicableCampaigns = [];

        foreach ($campaigns as $campaign) {
            $applicable = false;

            // Check product applicability
            if (isset($validated['product_ids'])) {
                foreach ($validated['product_ids'] as $productId) {
                    if ($campaign->isProductApplicable($productId)) {
                        $applicable = true;
                        break;
                    }
                }
            }

            // Check category applicability
            if (!$applicable && isset($validated['category_ids'])) {
                $applicableCategories = $campaign->applicable_categories ?? [];
                if (array_intersect($validated['category_ids'], $applicableCategories)) {
                    $applicable = true;
                }
            }

            if ($applicable) {
                $applicableCampaigns[] = $campaign;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $applicableCampaigns,
            'message' => 'Active campaigns retrieved successfully'
        ]);
    }
}
