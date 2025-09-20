<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories
     */
    public function index()
    {
        $categories = Category::withCount('products')
                            ->orderBy('title')
                            ->paginate(20);
        
        return view('super-admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('super-admin.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Category::create([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => \Str::slug($request->title),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('super-admin.categories.index')
                        ->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load('products.shop');
        
        return view('super-admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        return view('super-admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:categories,title,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => \Str::slug($request->title),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('super-admin.categories.index')
                        ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('super-admin.categories.index')
                            ->with('error', 'Cannot delete category that has products. Please move or delete the products first.');
        }

        $category->delete();
        
        return redirect()->route('super-admin.categories.index')
                        ->with('success', 'Category deleted successfully!');
    }
}
