<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'images')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'specifications' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'discount_tag' => 'nullable|string|max:50',
            'discount_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'quantity' => 'required|integer|min:0',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'is_active' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Removed strict dimensions
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Removed strict dimensions
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        // Handle multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product added!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load('category', 'images');
        return view('admin.products.show', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'specifications' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'discount_tag' => 'nullable|string|max:50',
            'discount_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'quantity' => 'required|integer|min:0',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'is_active' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Removed strict dimensions
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Removed strict dimensions
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        // Handle multiple images
        if ($request->hasFile('images')) {
            $nextSortOrder = $product->images()->max('sort_order') ?? 0;
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => ++$nextSortOrder,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated!');
    }

    public function destroy(Product $product)
    {
        // Delete main image if exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Delete additional images
        foreach ($product->images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted!');
    }

    public function deleteImage(ProductImage $image)
    {
        // Check if the image belongs to a product (for security)
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return response()->json(['success' => true]);
    }

    public function bulkUploadForm()
    {
        return view('admin.products.bulk-upload');
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $data = array_map('str_getcsv', file($file->getRealPath()));

        // Remove header row
        array_shift($data);

        $errors = [];
        $successCount = 0;
        $totalCount = count($data);

        foreach ($data as $index => $row) {
            try {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Map CSV columns to product fields
                $productData = [
                    'title' => $row[0] ?? '',
                    'description' => $row[1] ?? '',
                    'features' => $row[2] ?? '',
                    'specifications' => $row[3] ?? '',
                    'price' => $row[4] ?? '',
                    'selling_price' => $row[5] ?? null,
                    'quantity' => $row[6] ?? 0,
                    'stock_status' => $row[7] ?? 'in_stock',
                    'is_active' => strtolower($row[8] ?? 'active') === 'active' ? 1 : 0,
                    'category_id' => $this->getCategoryId($row[9] ?? ''),
                    'discount_tag' => $row[10] ?? null,
                    'discount_color' => $row[11] ?? '#FF0000',
                ];

                // Validate individual product data
                $validator = Validator::make($productData, [
                    'title' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'features' => 'nullable|string',
                    'specifications' => 'nullable|string',
                    'price' => 'required|numeric|min:0',
                    'selling_price' => 'nullable|numeric|min:0',
                    'quantity' => 'required|integer|min:0',
                    'stock_status' => 'required|in:in_stock,out_of_stock',
                    'is_active' => 'required|boolean',
                    'category_id' => 'required|exists:categories,id',
                    'discount_tag' => 'nullable|string|max:50',
                    'discount_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Row " . ($index + 2) . ": " . implode(', ', $validator->errors()->all());
                    continue;
                }

                // Create the product
                Product::create($productData);
                $successCount++;

            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        if ($successCount > 0) {
            $message = "Successfully uploaded {$successCount} out of {$totalCount} products.";
            if (!empty($errors)) {
                $message .= " Some products had errors and were skipped.";
            }
            return redirect()->route('admin.products.index')->with('success', $message)->with('bulk_errors', $errors);
        } else {
            return redirect()->back()->with('error', 'No products were uploaded. Please check your CSV file format.')->with('bulk_errors', $errors);
        }
    }

    public function downloadCsvTemplate()
    {
        $filename = 'products_bulk_upload_template.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $template = [
            ['Title', 'Description', 'Features', 'Specifications', 'Price', 'Selling Price', 'Quantity', 'Stock Status', 'Status', 'Category Title', 'Discount Tag', 'Discount Color'],
            ['Sample Product 1', 'This is a sample product description', 'Feature 1, Feature 2, Feature 3', 'Spec 1: Value, Spec 2: Value', '100.00', '80.00', '50', 'in_stock', 'active', 'Electronics', '20% OFF', '#FF0000'],
            ['Sample Product 2', 'Another product description', '', '', '200.00', '', '25', 'out_of_stock', 'inactive', 'Clothing', '', '#00FF00'],
        ];

        $callback = function() use ($template) {
            $file = fopen('php://output', 'w');
            foreach ($template as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getCategoryId($categoryName)
    {
        if (empty($categoryName)) {
            return null;
        }

        $category = Category::where('title', 'LIKE', '%' . trim($categoryName) . '%')->first();

        if (!$category) {
            // Create category if it doesn't exist
            $category = Category::create([
                'title' => trim($categoryName),
                'slug' => Str::slug(trim($categoryName)),
                'description' => 'Auto-created from bulk upload',
                'is_active' => true,
            ]);
        }

        return $category->id;
    }
}
