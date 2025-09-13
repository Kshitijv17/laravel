<?php

namespace App\Http\Controllers\Web;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display all categories
     */
    public function index()
    {
        $categories = Category::active()
            ->with('products')
            ->get();

        return view('web.categories.index', compact('categories'));
    }

    /**
     * Display category with products
     */
    public function show($slug, Request $request)
    {
        $category = Category::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $query = Product::active()
            ->where('category_id', $category->id)
            ->with(['category', 'reviews']);

        // Note: Child categories not implemented in current schema

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort products
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);

        return view('web.categories.show', compact('category', 'products'));
    }

    /**
     * Get category navigation (AJAX)
     */
    public function navigation()
    {
        $categories = Category::active()
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }
}
