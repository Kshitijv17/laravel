<?php

namespace App\Http\Controllers\Api;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors
     */
    public function index(Request $request): JsonResponse
    {
        $query = Vendor::with(['products']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('verified')) {
            if ($request->verified) {
                $query->verified();
            } else {
                $query->where('is_verified', false);
            }
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('company_name', 'like', '%' . $request->search . '%');
        }

        $vendors = $query->orderBy('created_at', 'desc')
                        ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $vendors,
            'message' => 'Vendors retrieved successfully'
        ]);
    }

    /**
     * Store a newly created vendor
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'required|string|max:255',
            'company_registration' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_routing_number' => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'in:active,inactive,suspended'
        ]);

        $vendor = Vendor::create($validated);

        return response()->json([
            'success' => true,
            'data' => $vendor,
            'message' => 'Vendor created successfully'
        ], 201);
    }

    /**
     * Display the specified vendor
     */
    public function show(Vendor $vendor): JsonResponse
    {
        $vendor->load(['products', 'purchaseOrders']);

        return response()->json([
            'success' => true,
            'data' => $vendor,
            'message' => 'Vendor retrieved successfully'
        ]);
    }

    /**
     * Update the specified vendor
     */
    public function update(Request $request, Vendor $vendor): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:vendors,email,' . $vendor->id,
            'phone' => 'nullable|string|max:20',
            'company_name' => 'sometimes|string|max:255',
            'company_registration' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'address_line_1' => 'sometimes|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'sometimes|string|max:255',
            'state' => 'sometimes|string|max:255',
            'postal_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_routing_number' => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'sometimes|in:active,inactive,suspended'
        ]);

        $vendor->update($validated);

        return response()->json([
            'success' => true,
            'data' => $vendor,
            'message' => 'Vendor updated successfully'
        ]);
    }

    /**
     * Remove the specified vendor
     */
    public function destroy(Vendor $vendor): JsonResponse
    {
        $vendor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor deleted successfully'
        ]);
    }

    /**
     * Verify vendor
     */
    public function verify(Vendor $vendor): JsonResponse
    {
        $vendor->update([
            'is_verified' => true,
            'verified_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $vendor,
            'message' => 'Vendor verified successfully'
        ]);
    }

    /**
     * Unverify vendor
     */
    public function unverify(Vendor $vendor): JsonResponse
    {
        $vendor->update([
            'is_verified' => false,
            'verified_at' => null
        ]);

        return response()->json([
            'success' => true,
            'data' => $vendor,
            'message' => 'Vendor verification removed'
        ]);
    }

    /**
     * Get vendor products
     */
    public function products(Vendor $vendor, Request $request): JsonResponse
    {
        $query = $vendor->products();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Vendor products retrieved successfully'
        ]);
    }

    /**
     * Get vendor statistics
     */
    public function statistics(Vendor $vendor): JsonResponse
    {
        $stats = [
            'total_products' => $vendor->products()->count(),
            'active_products' => $vendor->products()->where('status', 'active')->count(),
            'total_purchase_orders' => $vendor->purchaseOrders()->count(),
            'pending_orders' => $vendor->purchaseOrders()->where('status', 'pending')->count(),
            'completed_orders' => $vendor->purchaseOrders()->where('status', 'completed')->count(),
            'total_order_value' => $vendor->purchaseOrders()->sum('total_amount')
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Vendor statistics retrieved successfully'
        ]);
    }
}
