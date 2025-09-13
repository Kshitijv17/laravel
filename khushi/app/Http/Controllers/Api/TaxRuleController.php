<?php

namespace App\Http\Controllers\Api;

use App\Models\TaxRule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class TaxRuleController extends Controller
{
    /**
     * Display a listing of tax rules
     */
    public function index(Request $request): JsonResponse
    {
        $query = TaxRule::query();

        if ($request->has('active')) {
            $query->active();
        }

        if ($request->has('country')) {
            $query->where('country', $request->country);
        }

        if ($request->has('state')) {
            $query->where('state', $request->state);
        }

        if ($request->has('tax_type')) {
            $query->where('tax_type', $request->tax_type);
        }

        $taxRules = $query->orderBy('priority', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $taxRules,
            'message' => 'Tax rules retrieved successfully'
        ]);
    }

    /**
     * Store a newly created tax rule
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tax_type' => 'required|in:percentage,fixed',
            'rate' => 'required|numeric|min:0',
            'country' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'priority' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $taxRule = TaxRule::create($validated);

        return response()->json([
            'success' => true,
            'data' => $taxRule,
            'message' => 'Tax rule created successfully'
        ], 201);
    }

    /**
     * Display the specified tax rule
     */
    public function show(TaxRule $taxRule): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $taxRule,
            'message' => 'Tax rule retrieved successfully'
        ]);
    }

    /**
     * Update the specified tax rule
     */
    public function update(Request $request, TaxRule $taxRule): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'tax_type' => 'sometimes|in:percentage,fixed',
            'rate' => 'sometimes|numeric|min:0',
            'country' => 'sometimes|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'priority' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $taxRule->update($validated);

        return response()->json([
            'success' => true,
            'data' => $taxRule,
            'message' => 'Tax rule updated successfully'
        ]);
    }

    /**
     * Remove the specified tax rule
     */
    public function destroy(TaxRule $taxRule): JsonResponse
    {
        $taxRule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tax rule deleted successfully'
        ]);
    }

    /**
     * Calculate tax for given location and amount
     */
    public function calculateTax(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'country' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20'
        ]);

        $taxRules = TaxRule::active()
                          ->forLocation(
                              $validated['country'],
                              $validated['state'] ?? null,
                              $validated['city'] ?? null,
                              $validated['postal_code'] ?? null
                          )
                          ->orderBy('priority', 'desc')
                          ->get();

        $totalTax = 0;
        $appliedRules = [];

        foreach ($taxRules as $rule) {
            $taxAmount = $rule->calculateTax($validated['amount']);
            $totalTax += $taxAmount;
            
            $appliedRules[] = [
                'rule' => $rule,
                'tax_amount' => $taxAmount
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'subtotal' => $validated['amount'],
                'total_tax' => $totalTax,
                'total_amount' => $validated['amount'] + $totalTax,
                'applied_rules' => $appliedRules
            ],
            'message' => 'Tax calculated successfully'
        ]);
    }
}
