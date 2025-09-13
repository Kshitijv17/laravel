<?php

namespace App\Http\Controllers\Api;

use App\Models\ShippingZone;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ShippingZoneController extends Controller
{
    /**
     * Display a listing of shipping zones
     */
    public function index(Request $request): JsonResponse
    {
        $query = ShippingZone::query();

        if ($request->has('active')) {
            $query->active();
        }

        if ($request->has('country')) {
            $query->where('countries', 'like', '%' . $request->country . '%');
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $shippingZones = $query->orderBy('created_at', 'desc')
                              ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $shippingZones,
            'message' => 'Shipping zones retrieved successfully'
        ]);
    }

    /**
     * Store a newly created shipping zone
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'countries' => 'required|array',
            'countries.*' => 'string|max:255',
            'states' => 'nullable|array',
            'states.*' => 'string|max:255',
            'postal_codes' => 'nullable|array',
            'postal_codes.*' => 'string|max:20',
            'base_rate' => 'required|numeric|min:0',
            'per_kg_rate' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0',
            'delivery_time_min' => 'nullable|integer|min:1',
            'delivery_time_max' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $shippingZone = ShippingZone::create($validated);

        return response()->json([
            'success' => true,
            'data' => $shippingZone,
            'message' => 'Shipping zone created successfully'
        ], 201);
    }

    /**
     * Display the specified shipping zone
     */
    public function show(ShippingZone $shippingZone): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $shippingZone,
            'message' => 'Shipping zone retrieved successfully'
        ]);
    }

    /**
     * Update the specified shipping zone
     */
    public function update(Request $request, ShippingZone $shippingZone): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'countries' => 'sometimes|array',
            'countries.*' => 'string|max:255',
            'states' => 'nullable|array',
            'states.*' => 'string|max:255',
            'postal_codes' => 'nullable|array',
            'postal_codes.*' => 'string|max:20',
            'base_rate' => 'sometimes|numeric|min:0',
            'per_kg_rate' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0',
            'delivery_time_min' => 'nullable|integer|min:1',
            'delivery_time_max' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $shippingZone->update($validated);

        return response()->json([
            'success' => true,
            'data' => $shippingZone,
            'message' => 'Shipping zone updated successfully'
        ]);
    }

    /**
     * Remove the specified shipping zone
     */
    public function destroy(ShippingZone $shippingZone): JsonResponse
    {
        $shippingZone->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shipping zone deleted successfully'
        ]);
    }

    /**
     * Calculate shipping rate for location
     */
    public function calculateRate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'weight' => 'required|numeric|min:0',
            'order_total' => 'nullable|numeric|min:0'
        ]);

        $shippingZones = ShippingZone::active()->get();
        $applicableZones = [];

        foreach ($shippingZones as $zone) {
            if ($zone->coversLocation($validated['country'], $validated['state'], $validated['postal_code'])) {
                $rate = $zone->getShippingRate($validated['weight'], $validated['order_total'] ?? 0);
                
                $applicableZones[] = [
                    'zone' => $zone,
                    'rate' => $rate,
                    'delivery_time' => $zone->delivery_time_range
                ];
            }
        }

        if (empty($applicableZones)) {
            return response()->json([
                'success' => false,
                'message' => 'No shipping zones available for this location'
            ], 400);
        }

        // Sort by rate (cheapest first)
        usort($applicableZones, function($a, $b) {
            return $a['rate'] <=> $b['rate'];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'location' => [
                    'country' => $validated['country'],
                    'state' => $validated['state'],
                    'postal_code' => $validated['postal_code']
                ],
                'weight' => $validated['weight'],
                'order_total' => $validated['order_total'] ?? 0,
                'shipping_options' => $applicableZones
            ],
            'message' => 'Shipping rates calculated successfully'
        ]);
    }
}
