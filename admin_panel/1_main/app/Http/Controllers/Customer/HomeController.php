<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the main customer homepage with products
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'shop'])
                        ->where('is_active', true)
                        ->whereHas('shop', function($q) {
                            $q->where('is_active', true);
                        });

        // Search functionality
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Shop filter
        if ($request->filled('shop')) {
            $query->where('shop_id', $request->shop);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort options
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('title', 'asc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);
        
        // Get filter data
        $categories = Category::orderBy('title')->get();
        $shops = Shop::where('is_active', true)->orderBy('name')->get();
        
        // Get featured products for hero section
        $featuredProducts = Product::with(['category', 'shop'])
                                  ->where('is_active', true)
                                  ->where('is_featured', true)
                                  ->whereHas('shop', function($q) {
                                      $q->where('is_active', true);
                                  })
                                  ->limit(6)
                                  ->get();

        // Statistics for homepage
        $stats = [
            'total_products' => Product::where('is_active', true)->count(),
            'total_shops' => Shop::where('is_active', true)->count(),
            'total_categories' => Category::count(),
        ];

        return view('customer.home', compact(
            'products', 
            'categories', 
            'shops', 
            'featuredProducts', 
            'stats'
        ));
    }

    /**
     * Display products by category
     */
    public function category(Category $category, Request $request)
    {
        $query = Product::with(['category', 'shop'])
                        ->where('is_active', true)
                        ->where('category_id', $category->id)
                        ->whereHas('shop', function($q) {
                            $q->where('is_active', true);
                        });

        // Search within category
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Sort options
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);
        
        return view('customer.category', compact('category', 'products'));
    }

    /**
     * Display products by shop
     */
    public function shop(Shop $shop, Request $request)
    {
        if (!$shop->is_active) {
            abort(404, 'Shop not found or inactive');
        }

        $query = Product::with(['category'])
                        ->where('is_active', true)
                        ->where('shop_id', $shop->id);

        // Search within shop
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Category filter within shop
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Sort options
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);
        
        // Get categories available in this shop
        $categories = Category::whereHas('products', function($q) use ($shop) {
            $q->where('shop_id', $shop->id)->where('is_active', true);
        })->orderBy('title')->get();

        return view('customer.shop', compact('shop', 'products', 'categories'));
    }
}
