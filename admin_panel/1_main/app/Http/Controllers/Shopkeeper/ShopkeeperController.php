<?php

namespace App\Http\Controllers\Shopkeeper;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopkeeperController extends Controller
{
    /**
     * Shopkeeper dashboard
     */
    public function dashboard()
    {
        // Ensure user is authenticated and is an admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return redirect()->route('admin.login')->with('error', 'Access denied. Admin privileges required.');
        }

        // Redirect super admins to their own dashboard
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        $user = auth()->user();
        $shop = $user->shop;

        // If admin doesn't have a shop, redirect to shop creation
        if (!$shop) {
            return redirect()->route('shopkeeper.shop.create')
                           ->with('info', 'Please set up your shop to get started.');
        }

        // Get shop statistics
        $stats = [
            'total_products' => $shop->products()->count(),
            'active_products' => $shop->products()->where('is_active', true)->count(),
            'total_orders' => $shop->orders()->count(),
            'pending_orders' => $shop->orders()->where('status', 'pending')->count(),
            'processing_orders' => $shop->orders()->where('status', 'processing')->count(),
            'shipped_orders' => $shop->orders()->where('status', 'shipped')->count(),
            'delivered_orders' => $shop->orders()->where('status', 'delivered')->count(),
            'total_revenue' => $shop->orders()->where('payment_status', 'paid')->sum('total_amount'),
            'pending_revenue' => $shop->orders()->where('payment_status', 'pending')->sum('total_amount'),
        ];

        // Get recent orders
        $recentOrders = $shop->orders()
                            ->with(['user', 'items'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();

        // Get low stock products
        $lowStockProducts = $shop->products()
                                ->where('quantity', '<=', 10)
                                ->where('is_active', true)
                                ->orderBy('quantity', 'asc')
                                ->limit(5)
                                ->get();

        return view('shopkeeper.dashboard', compact('shop', 'stats', 'recentOrders', 'lowStockProducts'));
    }

    /**
     * Show shop creation form
     */
    public function createShop()
    {
        // Ensure user is authenticated and is an admin without a shop
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return redirect()->route('admin.login')->with('error', 'Access denied.');
        }

        if (auth()->user()->shop) {
            return redirect()->route('shopkeeper.dashboard');
        }

        return view('shopkeeper.shop.create');
    }

    /**
     * Store new shop
     */
    public function storeShop(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        // Check if user already has a shop
        if ($user->shop) {
            return redirect()->route('shopkeeper.dashboard')
                           ->with('error', 'You already have a shop.');
        }

        $shopData = [
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'website' => $request->website,
            'admin_id' => $user->id,
            'is_active' => true,
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('shops/logos', 'public');
            $shopData['logo'] = $logoPath;
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('shops/banners', 'public');
            $shopData['banner'] = $bannerPath;
        }

        Shop::create($shopData);

        return redirect()->route('shopkeeper.dashboard')
                       ->with('success', 'Shop created successfully! You can now start adding products.');
    }

    /**
     * Show shop edit form
     */
    public function editShop()
    {
        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop) {
            return redirect()->route('shopkeeper.shop.create');
        }

        return view('shopkeeper.shop.edit', compact('shop'));
    }

    /**
     * Update shop
     */
    public function updateShop(Request $request)
    {
        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop) {
            return redirect()->route('shopkeeper.shop.create');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $shopData = [
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'website' => $request->website,
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($shop->logo) {
                \Storage::disk('public')->delete($shop->logo);
            }
            $logoPath = $request->file('logo')->store('shops/logos', 'public');
            $shopData['logo'] = $logoPath;
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner if exists
            if ($shop->banner) {
                \Storage::disk('public')->delete($shop->banner);
            }
            $bannerPath = $request->file('banner')->store('shops/banners', 'public');
            $shopData['banner'] = $bannerPath;
        }

        $shop->update($shopData);

        return redirect()->route('shopkeeper.dashboard')
                       ->with('success', 'Shop updated successfully!');
    }
}
