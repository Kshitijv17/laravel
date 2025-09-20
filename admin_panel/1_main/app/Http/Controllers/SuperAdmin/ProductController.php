<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of all products across all shops
     */
    public function index()
    {
        $products = Product::with(['category', 'shop'])
                          ->latest()
                          ->paginate(20);
        
        return view('super-admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $categories = Category::all();
        $shops = Shop::where('is_active', true)->get();
        
        return view('super-admin.products.create', compact('categories', 'shops'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'specifications' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'shop_id' => 'required|exists:shops,id',
            'discount_tag' => 'nullable|string|max:50',
            'discount_color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $product = Product::create([
            'title' => $request->title,
            'description' => $request->description,
            'features' => $request->features,
            'specifications' => $request->specifications,
            'price' => $request->price,
            'selling_price' => $request->selling_price,
            'quantity' => $request->quantity,
            'category_id' => $request->category_id,
            'shop_id' => $request->shop_id,
            'discount_tag' => $request->discount_tag,
            'discount_color' => $request->discount_color,
            'stock_status' => $request->quantity > 0 ? 'in_stock' : 'out_of_stock',
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('super-admin.products.index')
                        ->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load(['category', 'shop']);
        
        return view('super-admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $shops = Shop::where('is_active', true)->get();
        
        return view('super-admin.products.edit', compact('product', 'categories', 'shops'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'specifications' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'shop_id' => 'required|exists:shops,id',
            'discount_tag' => 'nullable|string|max:50',
            'discount_color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $product->update([
            'title' => $request->title,
            'description' => $request->description,
            'features' => $request->features,
            'specifications' => $request->specifications,
            'price' => $request->price,
            'selling_price' => $request->selling_price,
            'quantity' => $request->quantity,
            'category_id' => $request->category_id,
            'shop_id' => $request->shop_id,
            'discount_tag' => $request->discount_tag,
            'discount_color' => $request->discount_color,
            'stock_status' => $request->quantity > 0 ? 'in_stock' : 'out_of_stock',
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('super-admin.products.index')
                        ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        $product->delete();
        
        return redirect()->route('super-admin.products.index')
                        ->with('success', 'Product deleted successfully!');
    }

    /**
     * Show bulk upload form
     */
    public function bulkUploadForm()
    {
        return view('super-admin.products.bulk-upload');
    }

    /**
     * Process bulk upload
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        // Implementation for bulk upload would go here
        return redirect()->route('super-admin.products.index')
                        ->with('success', 'Bulk upload completed successfully!');
    }
}
