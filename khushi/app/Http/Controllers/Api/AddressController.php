<?php

namespace App\Http\Controllers\Api;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class AddressController extends Controller
{
    /**
     * Display user addresses
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        
        $addresses = Address::where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $addresses,
            'message' => 'Addresses retrieved successfully'
        ]);
    }

    /**
     * Store a new address
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:home,office,other',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean'
        ]);

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            Address::where('user_id', $validated['user_id'])
                   ->update(['is_default' => false]);
        }

        $address = Address::create($validated);

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Address created successfully'
        ], 201);
    }

    /**
     * Display the specified address
     */
    public function show(Address $address): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Address retrieved successfully'
        ]);
    }

    /**
     * Update the specified address
     */
    public function update(Request $request, Address $address): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'sometimes|in:home,office,other',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line_1' => 'sometimes|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'sometimes|string|max:255',
            'state' => 'sometimes|string|max:255',
            'postal_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean'
        ]);

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            Address::where('user_id', $address->user_id)
                   ->where('id', '!=', $address->id)
                   ->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Address updated successfully'
        ]);
    }

    /**
     * Remove the specified address
     */
    public function destroy(Address $address): JsonResponse
    {
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    /**
     * Set address as default
     */
    public function setDefault(Address $address): JsonResponse
    {
        // Unset all other defaults for this user
        Address::where('user_id', $address->user_id)
               ->update(['is_default' => false]);

        // Set this address as default
        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Address set as default successfully'
        ]);
    }
}
