<?php

namespace App\Http\Controllers\Api;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class WarehouseController extends Controller
{
    /**
     * Display a listing of warehouses
     */
    public function index(Request $request): JsonResponse
    {
        $query = Warehouse::with(['inventories.product']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
        }

        $warehouses = $query->orderBy('created_at', 'desc')
                           ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $warehouses,
            'message' => 'Warehouses retrieved successfully'
        ]);
    }

    /**
     * Store a newly created warehouse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:warehouses,code|max:50',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'status' => 'in:active,inactive'
        ]);

        $warehouse = Warehouse::create($validated);

        return response()->json([
            'success' => true,
            'data' => $warehouse,
            'message' => 'Warehouse created successfully'
        ], 201);
    }

    /**
     * Display the specified warehouse
     */
    public function show(Warehouse $warehouse): JsonResponse
    {
        $warehouse->load(['inventories.product']);

        return response()->json([
            'success' => true,
            'data' => $warehouse,
            'message' => 'Warehouse retrieved successfully'
        ]);
    }

    /**
     * Update the specified warehouse
     */
    public function update(Request $request, Warehouse $warehouse): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:warehouses,code,' . $warehouse->id . '|max:50',
            'address_line_1' => 'sometimes|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'sometimes|string|max:255',
            'state' => 'sometimes|string|max:255',
            'postal_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'status' => 'sometimes|in:active,inactive'
        ]);

        $warehouse->update($validated);

        return response()->json([
            'success' => true,
            'data' => $warehouse,
            'message' => 'Warehouse updated successfully'
        ]);
    }

    /**
     * Remove the specified warehouse
     */
    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $warehouse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Warehouse deleted successfully'
        ]);
    }

    /**
     * Get warehouse inventory
     */
    public function inventory(Warehouse $warehouse, Request $request): JsonResponse
    {
        $query = $warehouse->inventories()->with(['product']);

        if ($request->has('low_stock')) {
            $query->lowStock();
        }

        if ($request->has('product_search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->product_search . '%');
            });
        }

        $inventory = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $inventory,
            'message' => 'Warehouse inventory retrieved successfully'
        ]);
    }

    /**
     * Get warehouse statistics
     */
    public function statistics(Warehouse $warehouse): JsonResponse
    {
        $stats = [
            'total_products' => $warehouse->inventories()->distinct('product_id')->count(),
            'total_inventory_value' => $warehouse->total_inventory_value,
            'total_stock' => $warehouse->total_stock,
            'low_stock_items' => $warehouse->inventories()->lowStock()->count(),
            'out_of_stock_items' => $warehouse->inventories()->where('quantity', 0)->count(),
            'capacity_utilization' => $warehouse->capacity ? 
                ($warehouse->total_stock / $warehouse->capacity) * 100 : 0
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Warehouse statistics retrieved successfully'
        ]);
    }
}
