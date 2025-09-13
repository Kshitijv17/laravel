<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of product variants
     */
    public function index(Request $request): JsonResponse
    {
        $query = ProductVariant::with(['product']);

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('type')) {
            $query->byType($request->type);
        }

        if ($request->has('in_stock')) {
            $query->inStock();
        }

        if ($request->has('active')) {
            $query->active();
        }

        $variants = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $variants,
            'message' => 'Product variants retrieved successfully'
        ]);
    }

    /**
     * Store a newly created product variant
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:size,color,material,style',
            'value' => 'required|string|max:255',
            'price_adjustment' => 'nullable|numeric',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:product_variants,sku|max:255',
            'image' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        // Generate SKU if not provided
        if (!isset($validated['sku'])) {
            $product = Product::find($validated['product_id']);
            $validated['sku'] = $product->sku . '-' . strtoupper(substr($validated['type'], 0, 3)) . '-' . strtoupper(substr($validated['value'], 0, 3));
        }

        $variant = ProductVariant::create($validated);
        $variant->load(['product']);

        return response()->json([
            'success' => true,
            'data' => $variant,
            'message' => 'Product variant created successfully'
        ], 201);
    }

    /**
     * Display the specified product variant
     */
    public function show(ProductVariant $productVariant): JsonResponse
    {
        $productVariant->load(['product', 'cartItems', 'orderItems']);

        return response()->json([
            'success' => true,
            'data' => $productVariant,
            'message' => 'Product variant retrieved successfully'
        ]);
    }

    /**
     * Update the specified product variant
     */
    public function update(Request $request, ProductVariant $productVariant): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'sometimes|in:size,color,material,style',
            'value' => 'sometimes|string|max:255',
            'price_adjustment' => 'nullable|numeric',
            'stock_quantity' => 'sometimes|integer|min:0',
            'sku' => 'sometimes|string|unique:product_variants,sku,' . $productVariant->id . '|max:255',
            'image' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $productVariant->update($validated);
        $productVariant->load(['product']);

        return response()->json([
            'success' => true,
            'data' => $productVariant,
            'message' => 'Product variant updated successfully'
        ]);
    }

    /**
     * Remove the specified product variant
     */
    public function destroy(ProductVariant $productVariant): JsonResponse
    {
        $productVariant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product variant deleted successfully'
        ]);
    }

    /**
     * Get variants for a specific product
     */
    public function productVariants(Product $product): JsonResponse
    {
        $variants = $product->variants()->active()->get();

        return response()->json([
            'success' => true,
            'data' => $variants,
            'message' => 'Product variants retrieved successfully'
        ]);
    }

    /**
     * Update variant stock
     */
    public function updateStock(Request $request, ProductVariant $productVariant): JsonResponse
    {
        $validated = $request->validate([
            'stock_quantity' => 'required|integer|min:0'
        ]);

        $oldStock = $productVariant->stock_quantity;
        $productVariant->update($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'variant' => $productVariant,
                'old_stock' => $oldStock,
                'new_stock' => $validated['stock_quantity']
            ],
            'message' => 'Variant stock updated successfully'
        ]);
    }
}
