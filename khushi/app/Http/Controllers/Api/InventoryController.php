<?php

namespace App\Http\Controllers\Api;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory
     */
    public function index(Request $request): JsonResponse
    {
        $query = Inventory::with(['product', 'warehouse']);

        if ($request->has('warehouse_id')) {
            $query->byWarehouse($request->warehouse_id);
        }

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('low_stock')) {
            $query->lowStock();
        }

        if ($request->has('in_stock')) {
            $query->inStock();
        }

        if ($request->has('expiring_soon')) {
            $query->expiringSoon();
        }

        $inventory = $query->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $inventory,
            'message' => 'Inventory retrieved successfully'
        ]);
    }

    /**
     * Store a newly created inventory record
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:0',
            'reserved_quantity' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'in:active,inactive'
        ]);

        $inventory = Inventory::create($validated);
        $inventory->load(['product', 'warehouse']);

        return response()->json([
            'success' => true,
            'data' => $inventory,
            'message' => 'Inventory record created successfully'
        ], 201);
    }

    /**
     * Display the specified inventory record
     */
    public function show(Inventory $inventory): JsonResponse
    {
        $inventory->load(['product', 'warehouse']);

        return response()->json([
            'success' => true,
            'data' => $inventory,
            'message' => 'Inventory record retrieved successfully'
        ]);
    }

    /**
     * Update the specified inventory record
     */
    public function update(Request $request, Inventory $inventory): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'sometimes|integer|min:0',
            'reserved_quantity' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'sometimes|in:active,inactive'
        ]);

        $inventory->update($validated);
        $inventory->load(['product', 'warehouse']);

        return response()->json([
            'success' => true,
            'data' => $inventory,
            'message' => 'Inventory record updated successfully'
        ]);
    }

    /**
     * Remove the specified inventory record
     */
    public function destroy(Inventory $inventory): JsonResponse
    {
        $inventory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inventory record deleted successfully'
        ]);
    }

    /**
     * Adjust inventory quantity
     */
    public function adjustQuantity(Request $request, Inventory $inventory): JsonResponse
    {
        $validated = $request->validate([
            'adjustment' => 'required|integer',
            'reason' => 'required|string|max:255'
        ]);

        $oldQuantity = $inventory->quantity;
        $newQuantity = $oldQuantity + $validated['adjustment'];

        if ($newQuantity < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Adjustment would result in negative inventory'
            ], 400);
        }

        $inventory->update(['quantity' => $newQuantity]);

        return response()->json([
            'success' => true,
            'data' => [
                'inventory' => $inventory,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'adjustment' => $validated['adjustment']
            ],
            'message' => 'Inventory quantity adjusted successfully'
        ]);
    }

    /**
     * Reserve inventory
     */
    public function reserve(Request $request, Inventory $inventory): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $availableQuantity = $inventory->quantity - $inventory->reserved_quantity;

        if ($validated['quantity'] > $availableQuantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient inventory to reserve'
            ], 400);
        }

        $inventory->increment('reserved_quantity', $validated['quantity']);

        return response()->json([
            'success' => true,
            'data' => $inventory->fresh(),
            'message' => 'Inventory reserved successfully'
        ]);
    }

    /**
     * Release reserved inventory
     */
    public function release(Request $request, Inventory $inventory): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validated['quantity'] > $inventory->reserved_quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot release more than reserved quantity'
            ], 400);
        }

        $inventory->decrement('reserved_quantity', $validated['quantity']);

        return response()->json([
            'success' => true,
            'data' => $inventory->fresh(),
            'message' => 'Reserved inventory released successfully'
        ]);
    }

    /**
     * Get inventory statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_products' => Inventory::distinct('product_id')->count(),
            'total_quantity' => Inventory::sum('quantity'),
            'total_reserved' => Inventory::sum('reserved_quantity'),
            'low_stock_items' => Inventory::lowStock()->count(),
            'out_of_stock_items' => Inventory::where('quantity', 0)->count(),
            'expiring_soon' => Inventory::expiringSoon()->count(),
            'total_value' => Inventory::selectRaw('SUM(quantity * cost_price) as total_value')->value('total_value') ?? 0
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Inventory statistics retrieved successfully'
        ]);
    }
}
