<?php

namespace App\Http\Controllers\Shopkeeper;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of shopkeeper's products
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop) {
            return redirect()->route('shopkeeper.shop.create');
        }

        $query = $shop->products()->with('category');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Stock filter
        if ($request->filled('stock')) {
            if ($request->stock === 'low') {
                $query->where('quantity', '<=', 10);
            } elseif ($request->stock === 'out') {
                $query->where('quantity', 0);
            }
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = Category::orderBy('title')->get();

        $stats = [
            'total_products' => $shop->products()->count(),
            'active_products' => $shop->products()->where('is_active', true)->count(),
            'low_stock' => $shop->products()->where('quantity', '<=', 10)->count(),
            'out_of_stock' => $shop->products()->where('quantity', 0)->count(),
        ];

        return view('shopkeeper.products.index', compact('products', 'categories', 'stats', 'shop'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop) {
            return redirect()->route('shopkeeper.shop.create');
        }

        $categories = Category::orderBy('title')->get();
        return view('shopkeeper.products.create', compact('categories', 'shop'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop) {
            return redirect()->route('shopkeeper.shop.create');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'features' => 'nullable|string',
            'specifications' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $productData = $request->except(['image']);
        $productData['shop_id'] = $shop->id;
        $productData['is_active'] = $request->has('is_active');

        // Set selling price to price if not provided
        if (!$productData['selling_price']) {
            $productData['selling_price'] = $productData['price'];
        }

        // Set stock status based on quantity
        $productData['stock_status'] = $productData['quantity'] > 0 ? 'in_stock' : 'out_of_stock';

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $productData['image'] = $imagePath;
        }

        Product::create($productData);

        return redirect()->route('shopkeeper.products.index')
                       ->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $user = auth()->user();
        $shop = $user->shop;

        // Ensure the product belongs to the current shopkeeper
        if (!$shop || $product->shop_id !== $shop->id) {
            abort(403, 'Unauthorized access to this product.');
        }

        $product->load(['category', 'shop']);
        return view('shopkeeper.products.show', compact('product', 'shop'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $user = auth()->user();
        $shop = $user->shop;

        // Ensure the product belongs to the current shopkeeper
        if (!$shop || $product->shop_id !== $shop->id) {
            abort(403, 'Unauthorized access to this product.');
        }

        $categories = Category::orderBy('title')->get();
        return view('shopkeeper.products.edit', compact('product', 'categories', 'shop'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $user = auth()->user();
        $shop = $user->shop;

        // Ensure the product belongs to the current shopkeeper
        if (!$shop || $product->shop_id !== $shop->id) {
            abort(403, 'Unauthorized access to this product.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'features' => 'nullable|string',
            'specifications' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $productData = $request->except(['image']);
        $productData['is_active'] = $request->has('is_active');

        // Set selling price to price if not provided
        if (!$productData['selling_price']) {
            $productData['selling_price'] = $productData['price'];
        }

        // Set stock status based on quantity
        $productData['stock_status'] = $productData['quantity'] > 0 ? 'in_stock' : 'out_of_stock';

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $productData['image'] = $imagePath;
        }

        $product->update($productData);

        return redirect()->route('shopkeeper.products.show', $product)
                       ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        $user = auth()->user();
        $shop = $user->shop;

        // Ensure the product belongs to the current shopkeeper
        if (!$shop || $product->shop_id !== $shop->id) {
            abort(403, 'Unauthorized access to this product.');
        }

        // Delete image if exists
        if ($product->image) {
            \Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('shopkeeper.products.index')
                       ->with('success', 'Product deleted successfully!');
    }

    /**
     * Toggle product status
     */
    public function toggleStatus(Product $product)
    {
        $user = auth()->user();
        $shop = $user->shop;

        // Ensure the product belongs to the current shopkeeper
        if (!$shop || $product->shop_id !== $shop->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $product->update(['is_active' => !$product->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully',
            'is_active' => $product->is_active
        ]);
    }
}
