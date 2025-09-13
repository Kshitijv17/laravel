<?php

namespace App\Http\Controllers\Web;

use App\Models\Admin;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Coupon;
use App\Models\SupportTicket;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        // Middleware is handled in routes, no need to define here
    }

    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'active_products' => Product::where('status', true)->count(),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
            'newsletter_subscribers' => Newsletter::count()
        ];

        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->limit(10)
            ->get();

        $recentUsers = User::latest()
            ->limit(10)
            ->get();

        $monthlyRevenue = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentUsers', 'monthlyRevenue'));
    }

    /**
     * Admin profile
     */
    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'phone' => 'nullable|string|max:20'
        ]);

        $admin->update($validated);

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Change password form
     */
    public function changePasswordForm()
    {
        return view('admin.change-password');
    }

    /**
     * Update password
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($validated['current_password'], $admin->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $admin->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('admin.profile')->with('success', 'Password changed successfully');
    }

    /**
     * Manage users
     */
    public function users(Request $request)
    {
        $users = User::withCount('orders')
            ->with('orders')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // Calculate stats
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'new_users' => User::whereMonth('created_at', now()->month)->count(),
            'pending_verification' => User::whereNull('email_verified_at')->count()
        ];

        // Add total spent for each user
        foreach ($users as $user) {
            $user->total_spent = $user->orders->sum('total_amount');
        }

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show user details
     */
    public function showUser($id)
    {
        $user = User::with(['orders', 'addresses', 'wishlist'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Create new user form
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,banned'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    /**
     * Edit user form
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,banned'
        ]);

        $user->update($validated);

        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }

    /**
     * Manage orders
     */
    public function orders(Request $request)
    {
        $orders = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // Calculate stats
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'delivered')->count(),
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total_amount')
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Show order details
     */
    public function showOrder($id)
    {
        $order = Order::with(['user', 'items.product', 'address'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Edit order form
     */
    public function editOrder($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update order
     */
    public function updateOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded'
        ]);

        $order->update($validated);

        return response()->json(['success' => true, 'message' => 'Order updated successfully']);
    }

    /**
     * Manage products
     */
    public function products(Request $request)
    {
        $products = Product::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $categories = Category::where('status', true)->get();

        // Calculate stats
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('status', true)->count(),
            'low_stock' => Product::where('stock', '<=', 5)->count(),
            'categories' => Category::count()
        ];

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    /**
     * Show product details
     */
    public function showProduct($id)
    {
        $product = Product::with(['category', 'reviews.user'])->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Create new product form
     */
    public function createProduct()
    {
        $categories = Category::where('status', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store new product
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products',
            'status' => 'required|boolean',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    }

    /**
     * Edit product form
     */
    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('status', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update product
     */
    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku,' . $id,
            'status' => 'required|boolean',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);

        $product->update($validated);

        return response()->json(['success' => true, 'message' => 'Product updated successfully']);
    }

    /**
     * Delete product
     */
    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product deleted successfully']);
    }

    /**
     * Manage categories
     */
    public function categories(Request $request)
    {
        $categories = Category::withCount('products')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // Calculate stats
        $stats = [
            'total_categories' => Category::count(),
            'active_categories' => Category::where('status', true)->count(),
            'products_with_categories' => Product::whereNotNull('category_id')->count()
        ];

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    /**
     * Show category details
     */
    public function showCategory($id)
    {
        $category = Category::with('products')->findOrFail($id);
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Create new category form
     */
    public function createCategory()
    {
        return view('admin.categories.create');
    }

    /**
     * Store new category
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully');
    }

    /**
     * Edit category form
     */
    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update category
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return response()->json(['success' => true, 'message' => 'Category updated successfully']);
    }

    /**
     * Delete category
     */
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete category with products']);
        }

        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
    }

    /**
     * Manage banners
     */
    public function banners(Request $request)
    {
        $banners = Banner::orderBy('created_at', 'desc')->paginate(25);
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Manage coupons
     */
    public function coupons(Request $request)
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(25);
        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Manage support tickets
     */
    public function supportTickets(Request $request)
    {
        $tickets = SupportTicket::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('admin.support.index', compact('tickets'));
    }

    /**
     * Manage newsletter subscribers
     */
    public function newsletterSubscribers(Request $request)
    {
        $subscribers = Newsletter::orderBy('created_at', 'desc')->paginate(25);
        return view('admin.newsletter.index', compact('subscribers'));
    }

    /**
     * Analytics dashboard
     */
    public function analytics()
    {
        return view('admin.analytics');
    }

    /**
     * Settings page
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Create coupon form
     */
    public function createCoupon()
    {
        return view('admin.coupons.create');
    }

    /**
     * Store new coupon
     */
    public function storeCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:today',
            'status' => 'required|boolean'
        ]);

        Coupon::create($request->all());

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully!');
    }

    /**
     * Show coupon details
     */
    public function showCoupon(Coupon $coupon)
    {
        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Edit coupon form
     */
    public function editCoupon(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update coupon
     */
    public function updateCoupon(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:today',
            'status' => 'required|boolean'
        ]);

        $coupon->update($request->all());

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully!');
    }

    /**
     * Delete coupon
     */
    public function destroyCoupon(Coupon $coupon)
    {
        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully!'
        ]);
    }

    /**
     * Toggle coupon status
     */
    public function toggleCouponStatus(Coupon $coupon)
    {
        $coupon->update(['status' => !$coupon->status]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon status updated successfully!'
        ]);
    }
}
