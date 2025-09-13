<?php

namespace App\Http\Controllers\Web;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
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
            ->limit(8)
            ->get();

        $newProducts = Product::active()
            ->with(['category', 'reviews'])
            ->latest()
            ->limit(8)
            ->get();

        $categories = Category::active()
            ->limit(6)
            ->get();

        $banners = Banner::active()
            ->where('position', 'header')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('web.home', compact(
            'featuredProducts',
            'newProducts', 
            'categories',
            'banners'
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
}
