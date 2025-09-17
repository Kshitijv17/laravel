<?php

namespace App\Http\Controllers\Web;

use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display products listing
     */
    public function index(Request $request)
    {
        $query = Product::active()
            ->with(['category'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        // Search (word-by-word across name/description/sku)
        if ($request->filled('search')) {
            $tokens = preg_split('/\s+/', trim((string) $request->search), -1, PREG_SPLIT_NO_EMPTY);
            $query->where(function ($qb) use ($tokens) {
                foreach ($tokens as $t) {
                    $qb->where(function ($sub) use ($t) {
                        $sub->where('name', 'like', "%$t%")
                            ->orWhere('description', 'like', "%$t%")
                            ->orWhere('sku', 'like', "%$t%");
                    });
                }
            });
        }

        // Filter by category slug (single)
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by multiple category IDs
        if ($request->filled('categories')) {
            $ids = array_filter((array) $request->get('categories'));
            if (!empty($ids)) {
                $query->whereIn('category_id', $ids);
            }
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->whereRaw('COALESCE(discount_price, price) >= ?', [$request->min_price]);
        }
        if ($request->filled('max_price')) {
            $query->whereRaw('COALESCE(discount_price, price) <= ?', [$request->max_price]);
        }

        // Availability
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Minimum rating
        if ($request->filled('rating')) {
            $query->having('reviews_avg_rating', '>=', (int) $request->rating);
        }

        // Sort products
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'rating':
                $query->orderBy('reviews_avg_rating', 'desc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = Category::active()->get();

        return view('web.products.index', compact('products', 'categories'));
    }

    /**
     * Display product details
     */
    public function show($slug)
    {
        $product = Product::active()
            ->with(['category', 'variants', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'reviews'])
            ->limit(4)
            ->get();

        $isWishlisted = false;
        if (Auth::check()) {
            $isWishlisted = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();
        }

        return view('web.products.show', compact('product', 'relatedProducts', 'isWishlisted'));
    }

    /**
     * Add product to wishlist
     */
    public function addToWishlist(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to add items to wishlist');
        }

        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->exists();
        
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in wishlist'
            ]);
        }

        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist'
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function removeFromWishlist(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist'
        ]);
    }

    /**
     * Submit product review
     */
    public function submitReview(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to submit a review');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000'
        ]);

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already reviewed this product');
        }

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_approved' => true
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully');
    }

    /**
     * Quick view product (AJAX)
     */
    public function quickView(Product $product)
    {
        $product->load(['category', 'variants']);
        
        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }
}
