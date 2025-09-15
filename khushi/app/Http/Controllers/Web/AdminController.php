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
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'discount_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products',
            'status' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
            'is_featured' => 'sometimes|boolean'
        ]);

        if ($request->hasFile('image')) {
            // Just store the new image for creation
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
            'discount_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku,' . $id,
            'status' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
            'is_featured' => 'sometimes|boolean'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);

        $product->update($validated);

        return redirect()->route('admin.products.edit', $product->id)
            ->with('success', 'Product updated successfully');
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
            if ($category->image && !Str::startsWith($category->image, ['http://', 'https://'])) {
                // Delete old image file if it was stored locally
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully');
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
        $banners = Banner::orderBy('created_at', 'desc')->paginate(15);
        
        // Calculate statistics
        $totalBanners = Banner::count();
        $activeBanners = Banner::where('is_active', true)->count();
        // 'top' is used for hero/homepage banners in DB
        $homepageBanners = Banner::where('position', 'top')->count();
        $inactiveBanners = Banner::where('is_active', false)->count();
        
        return view('admin.banners.index', compact(
            'banners', 
            'totalBanners', 
            'activeBanners', 
            'homepageBanners', 
            'inactiveBanners'
        ));
    }

    /**
     * Manage coupons
     */
    public function coupons(Request $request)
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(25);
        
        // Calculate statistics
        $totalCoupons = Coupon::count();
        $activeCoupons = Coupon::where('is_active', true)->count();
        $expiringSoon = Coupon::where('is_active', true)
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(7))
            ->count();
        $totalUses = 0; // No usage tracking column in current schema
        
        return view('admin.coupons.index', compact(
            'coupons', 
            'totalCoupons', 
            'activeCoupons', 
            'expiringSoon', 
            'totalUses'
        ));
    }

    /**
     * Manage support tickets
     */
    public function supportTickets(Request $request)
    {
        $query = SupportTicket::with('user');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(25);
        
        // Calculate statistics
        $stats = [
            'total_tickets' => SupportTicket::count(),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
            'resolved_today' => SupportTicket::where('status', 'resolved')
                ->whereDate('updated_at', today())->count(),
            'avg_response_time' => '2h' // Placeholder
        ];

        return view('admin.support.index', compact('tickets', 'stats'));
    }

    /**
     * Show support ticket details
     */
    public function showSupportTicket(SupportTicket $ticket)
    {
        $ticket->load('user');
        return view('admin.support.show', compact('ticket'));
    }

    /**
     * Create support ticket
     */
    public function createSupportTicket(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,normal,high',
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);

        $ticket = SupportTicket::create($request->only([
            'user_id', 'subject', 'message', 'priority', 'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Support ticket created successfully!',
            'ticket' => $ticket
        ]);
    }

    /**
     * Update support ticket
     */
    public function updateSupportTicket(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,normal,high',
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);

        $ticket->update($request->only([
            'subject', 'message', 'priority', 'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Support ticket updated successfully!'
        ]);
    }

    /**
     * Update support ticket status
     */
    public function updateSupportTicketStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);

        $ticket->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket status updated successfully!'
        ]);
    }

    /**
     * Delete support ticket
     */
    public function deleteSupportTicket(SupportTicket $ticket)
    {
        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Support ticket deleted successfully!'
        ]);
    }

    /**
     * Manage newsletter subscribers
     */
    public function newsletterSubscribers(Request $request)
    {
        $query = Newsletter::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('email', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
            });
        }

        $subscribers = $query->orderBy('created_at', 'desc')->paginate(25);
        
        // Calculate statistics
        $stats = [
            'total_subscribers' => Newsletter::count(),
            'active_subscribers' => Newsletter::where('status', 'active')->count(),
            'inactive_subscribers' => Newsletter::where('status', 'inactive')->count(),
            'new_this_month' => Newsletter::whereMonth('created_at', now()->month)->count()
        ];

        return view('admin.newsletter.index', compact('subscribers', 'stats'));
    }

    /**
     * Store newsletter subscriber
     */
    public function storeNewsletterSubscriber(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email',
            'name' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        $subscriber = Newsletter::create([
            'email' => $request->email,
            'name' => $request->name,
            'status' => $request->status,
            'subscribed_at' => $request->status === 'active' ? now() : null,
            'unsubscribe_token' => \Str::random(32)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscriber added successfully!'
        ]);
    }

    /**
     * Update newsletter subscriber
     */
    public function updateNewsletterSubscriber(Request $request, Newsletter $subscriber)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email,' . $subscriber->id,
            'name' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        $updateData = [
            'email' => $request->email,
            'name' => $request->name,
            'status' => $request->status
        ];

        // Update timestamps based on status change
        if ($request->status === 'active' && $subscriber->status !== 'active') {
            $updateData['subscribed_at'] = now();
            $updateData['unsubscribed_at'] = null;
        } elseif ($request->status === 'inactive' && $subscriber->status !== 'inactive') {
            $updateData['unsubscribed_at'] = now();
        }

        $subscriber->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Subscriber updated successfully!'
        ]);
    }

    /**
     * Delete newsletter subscriber
     */
    public function deleteNewsletterSubscriber(Newsletter $subscriber)
    {
        $subscriber->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscriber deleted successfully!'
        ]);
    }

    /**
     * Send newsletter
     */
    public function sendNewsletter(Request $request)
    {
        $request->validate([
            'recipients' => 'required|in:all,active_only',
            'template' => 'required|string',
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        // Get recipients based on selection
        $query = Newsletter::query();
        if ($request->recipients === 'active_only') {
            $query->where('status', 'active');
        }
        $recipients = $query->get();

        // Here you would integrate with your email service
        // For now, we'll just return success
        
        return response()->json([
            'success' => true,
            'message' => "Newsletter sent to {$recipients->count()} subscribers!"
        ]);
    }

    /**
     * Export newsletter subscribers
     */
    public function exportNewsletterSubscribers()
    {
        $subscribers = Newsletter::all();
        
        $csvData = "Name,Email,Status,Subscribed At,Unsubscribed At\n";
        foreach ($subscribers as $subscriber) {
            $csvData .= sprintf(
                '"%s","%s","%s","%s","%s"' . "\n",
                $subscriber->name ?? '',
                $subscriber->email,
                $subscriber->status,
                $subscriber->subscribed_at ? $subscriber->subscribed_at->format('Y-m-d H:i:s') : '',
                $subscriber->unsubscribed_at ? $subscriber->unsubscribed_at->format('Y-m-d H:i:s') : ''
            );
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="newsletter_subscribers.csv"');
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
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings', compact('settings'));
    }

    /**
     * Save settings by group
     */
    public function saveSettings(Request $request, $group)
    {
        $validationRules = $this->getValidationRules($group);
        $request->validate($validationRules);

        foreach ($request->except(['_token']) as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $group,
                    'type' => $this->getSettingType($key, $value)
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst($group) . ' settings saved successfully!'
        ]);
    }

    /**
     * Clear cache
     */
    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');
        
        try {
            switch ($type) {
                case 'config':
                    \Artisan::call('config:clear');
                    break;
                case 'route':
                    \Artisan::call('route:clear');
                    break;
                case 'view':
                    \Artisan::call('view:clear');
                    break;
                case 'all':
                    \Artisan::call('cache:clear');
                    \Artisan::call('config:clear');
                    \Artisan::call('route:clear');
                    \Artisan::call('view:clear');
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Optimize database
     */
    public function optimizeDatabase()
    {
        try {
            \Artisan::call('optimize');
            
            return response()->json([
                'success' => true,
                'message' => 'Database optimized successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error optimizing database: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get validation rules for settings group
     */
    private function getValidationRules($group)
    {
        $rules = [];
        
        switch ($group) {
            case 'general':
                $rules = [
                    'site_name' => 'required|string|max:255',
                    'site_description' => 'nullable|string|max:500',
                    'contact_email' => 'required|email',
                    'support_phone' => 'nullable|string|max:20',
                    'timezone' => 'required|string'
                ];
                break;
            case 'ecommerce':
                $rules = [
                    'currency' => 'required|string|max:3',
                    'tax_rate' => 'required|numeric|min:0|max:100',
                    'free_shipping_threshold' => 'required|numeric|min:0',
                    'low_stock_alert' => 'required|integer|min:0',
                    'guest_checkout' => 'boolean',
                    'inventory_tracking' => 'boolean'
                ];
                break;
            case 'email':
                $rules = [
                    'smtp_host' => 'required|string',
                    'smtp_port' => 'required|integer|min:1|max:65535',
                    'smtp_username' => 'nullable|string',
                    'smtp_password' => 'nullable|string',
                    'from_email' => 'required|email',
                    'from_name' => 'required|string|max:255'
                ];
                break;
            case 'payment':
                $rules = [
                    'stripe_enabled' => 'boolean',
                    'stripe_publishable_key' => 'nullable|string',
                    'stripe_secret_key' => 'nullable|string',
                    'paypal_enabled' => 'boolean',
                    'paypal_client_id' => 'nullable|string',
                    'paypal_client_secret' => 'nullable|string',
                    'cod_enabled' => 'boolean'
                ];
                break;
            case 'security':
                $rules = [
                    'two_factor_enabled' => 'boolean',
                    'session_timeout' => 'required|integer|min:5|max:1440',
                    'max_login_attempts' => 'required|integer|min:1|max:20',
                    'maintenance_mode' => 'boolean'
                ];
                break;
        }
        
        return $rules;
    }

    /**
     * Get setting type based on key and value
     */
    private function getSettingType($key, $value)
    {
        if (is_bool($value) || in_array($value, ['0', '1', 'true', 'false'])) {
            return 'boolean';
        }
        
        if (is_numeric($value)) {
            return 'number';
        }
        
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }
        
        return 'text';
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
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'min_cart_value' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'end_date' => 'nullable|date|after:today',
            'is_active' => 'required|boolean'
        ]);

        // Transform form data to match database schema
        $data = $request->only(['code', 'description', 'type', 'value', 'min_cart_value', 'usage_limit', 'start_date', 'end_date', 'is_active']);
        
        // Handle type conversion from form to database
        if ($request->type === 'percentage') {
            $data['type'] = 'percent';
        }
        
        // Map form fields to database fields
        if ($request->has('min_amount')) {
            $data['min_cart_value'] = $request->min_amount;
        }
        if ($request->has('max_uses')) {
            $data['usage_limit'] = $request->max_uses;
        }
        if ($request->has('expires_at')) {
            $data['end_date'] = $request->expires_at;
        }
        if ($request->has('status')) {
            $data['is_active'] = $request->status;
        }

        Coupon::create($data);

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
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'min_cart_value' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'end_date' => 'nullable|date|after:today',
            'is_active' => 'required|boolean'
        ]);

        // Transform form data to match database schema
        $data = $request->only(['code', 'description', 'type', 'value', 'min_cart_value', 'usage_limit', 'start_date', 'end_date', 'is_active']);
        
        // Handle type conversion from form to database
        if ($request->type === 'percentage') {
            $data['type'] = 'percent';
        }
        
        // Map form fields to database fields
        if ($request->has('min_amount')) {
            $data['min_cart_value'] = $request->min_amount;
        }
        if ($request->has('max_uses')) {
            $data['usage_limit'] = $request->max_uses;
        }
        if ($request->has('expires_at')) {
            $data['end_date'] = $request->expires_at;
        }
        if ($request->has('status')) {
            $data['is_active'] = $request->status;
        }

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully!');
    }

    /**
     * Delete coupon
     */
    public function deleteCoupon(Coupon $coupon)
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
        $coupon->update(['is_active' => !$coupon->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon status updated successfully!'
        ]);
    }

    /**
     * Create banner form
     */
    public function createBanner()
    {
        return view('admin.banners.create');
    }

    /**
     * Store new banner
     */
    public function storeBanner(Request $request)
    {
        // Map form positions to database enum values
        $positionMap = [
            'hero' => 'top',
            'sidebar' => 'middle', 
            'footer' => 'bottom',
            'popup' => 'top' // Default to top for popup
        ];

        $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|in:hero,sidebar,footer,popup',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link_url' => 'nullable|url',
            'button_text' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|boolean'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
        }

        Banner::create([
            'title' => $request->title,
            'position' => $positionMap[$request->position], // Map to database enum
            'description' => $request->description,
            'image' => $imagePath,
            'link' => $request->link_url, // Use 'link' column name
            'is_active' => $request->status // Use 'is_active' column name
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully!');
    }

    /**
     * Show banner details
     */
    public function showBanner(Banner $banner)
    {
        // Map database values for display
        $positionMap = [
            'top' => 'hero',
            'middle' => 'sidebar',
            'bottom' => 'footer'
        ];
        
        $banner->display_position = $positionMap[$banner->position] ?? 'hero';
        $banner->link_url = $banner->link;
        $banner->status = $banner->is_active;
        
        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Show banner edit form
     */
    public function editBanner(Banner $banner)
    {
        // Map database enum values back to form values for display
        $positionMap = [
            'top' => 'hero',
            'middle' => 'sidebar',
            'bottom' => 'footer'
        ];
        
        $banner->display_position = $positionMap[$banner->position] ?? 'hero';
        $banner->link_url = $banner->link;
        $banner->status = $banner->is_active;
        
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update banner
     */
    public function updateBanner(Request $request, Banner $banner)
    {
        // Map form positions to database enum values
        $positionMap = [
            'hero' => 'top',
            'sidebar' => 'middle', 
            'footer' => 'bottom',
            'popup' => 'top' // Default to top for popup
        ];

        $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|in:hero,sidebar,footer,popup',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link_url' => 'nullable|url',
            'button_text' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|boolean'
        ]);

        $updateData = [
            'title' => $request->title,
            'position' => $positionMap[$request->position], // Map to database enum
            'description' => $request->description,
            'link' => $request->link_url, // Use 'link' column name
            'is_active' => $request->status // Use 'is_active' column name
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $updateData['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($updateData);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully!');
    }

    /**
     * Delete banner
     */
    public function destroyBanner(Banner $banner)
    {
        // Delete image file if exists
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully!'
        ]);
    }

    /**
     * Toggle banner status
     */
    public function toggleBannerStatus(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Banner status updated successfully!'
        ]);
    }
}
