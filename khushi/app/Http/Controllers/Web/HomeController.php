<?php

namespace App\Http\Controllers\Web;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Faq;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Display the homepage
     */
    public function index()
    {
        $featuredProducts = Product::active()
            ->featured()
            ->with(['category', 'reviews'])
            ->limit(16)
            ->get();

        $newProducts = Product::active()
            ->with(['category', 'reviews'])
            ->latest()
            ->limit(16)
            ->get();

        $categories = Category::active()
            ->limit(12)
            ->get();

        // Dynamic banners
        $heroBanners = Banner::active()
            ->byPosition('hero') // hero/top of page
            ->orderBy('created_at', 'desc')
            ->get();

        $midBanners = Banner::active()
            ->byPosition('sidebar') // sidebar banners
            ->orderBy('created_at', 'desc')
            ->get();

        // Dynamic brands for brand strip
        $brands = Brand::active()
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        return view('web.home', compact(
            'featuredProducts',
            'newProducts', 
            'categories',
            'heroBanners',
            'midBanners',
            'brands'
        ));
    }

    /**
     * Display the enhanced homepage with modern UI
     */
    public function enhanced()
    {
        // Get featured products with category and review count
        $featuredProducts = Product::where('status', true)
            ->where('is_featured', true)
            ->with(['category'])
            ->withCount('reviews')
            ->limit(8)
            ->get();

        // Get new products
        $newProducts = Product::where('status', true)
            ->with(['category'])
            ->withCount('reviews')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Get categories with product count
        $categories = Category::where('status', true)
            ->whereNull('parent_id')
            ->withCount('products')
            ->limit(6)
            ->get();

        // Get banners
        $heroBanners = Banner::where('status', true)
            ->where('position', 'hero')
            ->limit(3)
            ->get();

        $sidebarBanners = Banner::where('status', true)
            ->where('position', 'sidebar')
            ->limit(2)
            ->get();

        return view('web.enhanced-home', compact(
            'featuredProducts',
            'newProducts', 
            'categories',
            'heroBanners',
            'sidebarBanners'
        ));
    }

    public function simple()
    {
        // Get featured products with category and review count
        $featuredProducts = Product::where('status', true)
            ->where('is_featured', true)
            ->with(['category'])
            ->withCount('reviews')
            ->limit(8)
            ->get();

        // Get categories with product count
        $categories = Category::where('status', true)
            ->whereNull('parent_id')
            ->withCount('products')
            ->limit(6)
            ->get();

        return view('web.simple-enhanced', compact(
            'featuredProducts',
            'categories'
        ));
    }

    /**
     * Display about page
     */
    public function about()
    {
        return view('web.about');
    }

    /**
     * Display contact page
     */
    public function contact()
    {
        return view('web.contact');
    }

    /**
     * Handle contact form submission
     */
    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000'
        ]);

        // Here you can add logic to send email or store in database
        // For now, we'll just return success message

        return redirect()->route('contact')
            ->with('success', 'Thank you for your message. We will get back to you soon!');
    }

    /**
     * Display FAQ page
     */
    public function faq()
    {
        $faqs = Faq::active()
            ->orderBy('id')
            ->get();

        return view('web.faq', compact('faqs'));
    }

    /**
     * Handle newsletter subscription
     */
    public function newsletterSubscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:newsletters,email'
        ]);

        Newsletter::create([
            'email' => $validated['email'],
            'is_active' => true,
            'subscribed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to newsletter!'
        ]);
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return redirect()->route('products.index');
        }

        $products = Product::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with(['category', 'reviews'])
            ->paginate(12);

        return view('web.search', compact('products', 'query'));
    }

    /**
     * Live search suggestions (JSON)
     */
    public function suggest(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([
                'success' => true,
                'query' => $q,
                'products' => [],
                'suggestions' => []
            ]);
        }

        $tokens = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);

        // Products matching all tokens across name/description/sku
        $products = Product::active()
            ->select(['id', 'name', 'slug', 'image', 'price', 'discount_price'])
            ->where(function ($qB) use ($tokens) {
                foreach ($tokens as $t) {
                    $qB->where(function ($sub) use ($t) {
                        $sub->where('name', 'like', "%$t%")
                            ->orWhere('description', 'like', "%$t%")
                            ->orWhere('sku', 'like', "%$t%");
                    });
                }
            })
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'image_url' => $p->image_url,
                    'price' => $p->price,
                    'discount_price' => $p->discount_price,
                    'final_price' => $p->final_price,
                ];
            });

        // Name suggestions
        $suggestions = Product::active()
            ->where(function ($qB) use ($tokens) {
                foreach ($tokens as $t) {
                    $qB->where('name', 'like', "%$t%");
                }
            })
            ->distinct()
            ->orderBy('name')
            ->limit(5)
            ->pluck('name');

        return response()->json([
            'success' => true,
            'query' => $q,
            'products' => $products,
            'suggestions' => $suggestions,
        ]);
    }
}
