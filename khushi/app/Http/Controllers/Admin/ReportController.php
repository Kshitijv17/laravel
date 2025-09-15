<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Analytics;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        $reports = [
            'sales' => 'Sales Reports',
            'products' => 'Product Performance',
            'customers' => 'Customer Analytics',
            'inventory' => 'Inventory Reports',
            'traffic' => 'Traffic Analysis',
            'conversion' => 'Conversion Reports',
            'revenue' => 'Revenue Analysis',
            'geographic' => 'Geographic Reports'
        ];

        return view('admin.reports.index', compact('reports'));
    }

    public function sales(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);
        $endDate = now();

        $salesData = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $totalOrders = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw('products.name, SUM(order_items.quantity) as total_sold, SUM(order_items.price * order_items.quantity) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return view('admin.reports.sales', compact(
            'salesData', 'totalRevenue', 'totalOrders', 
            'averageOrderValue', 'topProducts', 'period'
        ));
    }

    public function products(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);

        $productPerformance = Product::with(['category', 'brand'])
            ->withCount(['orderItems as total_sold' => function($query) use ($startDate) {
                $query->whereHas('order', function($q) use ($startDate) {
                    $q->where('status', 'completed')
                      ->where('created_at', '>=', $startDate);
                });
            }])
            ->withSum(['orderItems as revenue' => function($query) use ($startDate) {
                $query->whereHas('order', function($q) use ($startDate) {
                    $q->where('status', 'completed')
                      ->where('created_at', '>=', $startDate);
                });
            }], DB::raw('price * quantity'))
            ->orderByDesc('revenue')
            ->paginate(20);

        $lowStockProducts = Product::where('stock_quantity', '<=', 10)
            ->where('status', 'active')
            ->orderBy('stock_quantity')
            ->limit(20)
            ->get();

        $categoryPerformance = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', $startDate)
            ->selectRaw('categories.name, COUNT(*) as orders, SUM(order_items.quantity) as items_sold, SUM(order_items.price * order_items.quantity) as revenue')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();

        return view('admin.reports.products', compact(
            'productPerformance', 'lowStockProducts', 'categoryPerformance', 'period'
        ));
    }

    public function customers(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);

        $customerStats = [
            'total_customers' => User::count(),
            'new_customers' => User::where('created_at', '>=', $startDate)->count(),
            'active_customers' => User::whereHas('orders', function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })->count(),
            'repeat_customers' => User::has('orders', '>', 1)->count()
        ];

        $topCustomers = User::withCount(['orders as total_orders' => function($query) use ($startDate) {
                $query->where('status', 'completed')
                      ->where('created_at', '>=', $startDate);
            }])
            ->withSum(['orders as total_spent' => function($query) use ($startDate) {
                $query->where('status', 'completed')
                      ->where('created_at', '>=', $startDate);
            }], 'total_amount')
            ->having('total_orders', '>', 0)
            ->orderByDesc('total_spent')
            ->limit(20)
            ->get();

        $customerRegistrations = User::selectRaw('DATE(created_at) as date, COUNT(*) as registrations')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.customers', compact(
            'customerStats', 'topCustomers', 'customerRegistrations', 'period'
        ));
    }

    public function inventory(Request $request)
    {
        $lowStockThreshold = $request->get('threshold', 10);
        
        $inventoryStats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'active')->count(),
            'out_of_stock' => Product::where('stock_quantity', 0)->count(),
            'low_stock' => Product::where('stock_quantity', '>', 0)
                                 ->where('stock_quantity', '<=', $lowStockThreshold)
                                 ->count()
        ];

        $lowStockProducts = Product::with(['category', 'brand'])
            ->where('stock_quantity', '<=', $lowStockThreshold)
            ->where('status', 'active')
            ->orderBy('stock_quantity')
            ->paginate(20);

        $categoryStock = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.status', 'active')
            ->selectRaw('categories.name, COUNT(*) as total_products, SUM(products.stock_quantity) as total_stock, AVG(products.stock_quantity) as avg_stock')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_stock')
            ->get();

        $inventoryValue = Product::where('status', 'active')
            ->selectRaw('SUM(stock_quantity * selling_price) as total_value')
            ->first()
            ->total_value ?? 0;

        return view('admin.reports.inventory', compact(
            'inventoryStats', 'lowStockProducts', 'categoryStock', 
            'inventoryValue', 'lowStockThreshold'
        ));
    }

    public function traffic(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);

        $trafficData = $this->analyticsService->getTrafficAnalytics($startDate, now());
        
        $pageViews = Analytics::where('event_type', 'page_view')
            ->whereBetween('created_at', [$startDate, now()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as views')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topPages = Analytics::where('event_type', 'page_view')
            ->whereBetween('created_at', [$startDate, now()])
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.url")) as url, COUNT(*) as views')
            ->groupBy('url')
            ->orderByDesc('views')
            ->limit(20)
            ->get();

        $referrerData = Analytics::where('event_type', 'page_view')
            ->whereBetween('created_at', [$startDate, now()])
            ->whereNotNull('referrer')
            ->selectRaw('referrer, COUNT(*) as visits')
            ->groupBy('referrer')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        return view('admin.reports.traffic', compact(
            'trafficData', 'pageViews', 'topPages', 'referrerData', 'period'
        ));
    }

    public function conversion(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);

        $conversionData = $this->analyticsService->getConversionAnalytics($startDate, now());

        $funnelData = [
            'visitors' => Analytics::where('event_type', 'page_view')
                ->whereBetween('created_at', [$startDate, now()])
                ->distinct('session_id')
                ->count(),
            'product_views' => Analytics::where('event_type', 'product_view')
                ->whereBetween('created_at', [$startDate, now()])
                ->distinct('session_id')
                ->count(),
            'add_to_cart' => Analytics::where('event_type', 'add_to_cart')
                ->whereBetween('created_at', [$startDate, now()])
                ->distinct('session_id')
                ->count(),
            'checkout_started' => Analytics::where('event_type', 'checkout_started')
                ->whereBetween('created_at', [$startDate, now()])
                ->distinct('session_id')
                ->count(),
            'purchases' => Analytics::where('event_type', 'purchase')
                ->whereBetween('created_at', [$startDate, now()])
                ->distinct('session_id')
                ->count()
        ];

        $abandonedCarts = DB::table('cart_items')
            ->join('users', 'cart_items.user_id', '=', 'users.id')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('orders')
                      ->whereColumn('orders.user_id', 'cart_items.user_id')
                      ->where('orders.created_at', '>', DB::raw('cart_items.created_at'));
            })
            ->where('cart_items.created_at', '>=', $startDate)
            ->selectRaw('users.email, users.name, COUNT(*) as items, SUM(products.selling_price * cart_items.quantity) as cart_value')
            ->groupBy('users.id', 'users.email', 'users.name')
            ->orderByDesc('cart_value')
            ->limit(20)
            ->get();

        return view('admin.reports.conversion', compact(
            'conversionData', 'funnelData', 'abandonedCarts', 'period'
        ));
    }

    public function revenue(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);

        $revenueData = $this->analyticsService->getRevenueAnalytics($startDate, now());

        $monthlyRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, now()])
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $paymentMethodRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, now()])
            ->selectRaw('payment_method, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('payment_method')
            ->orderByDesc('revenue')
            ->get();

        $revenueByCategory = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate, now()])
            ->selectRaw('categories.name, SUM(order_items.price * order_items.quantity) as revenue')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();

        return view('admin.reports.revenue', compact(
            'revenueData', 'monthlyRevenue', 'paymentMethodRevenue', 
            'revenueByCategory', 'period'
        ));
    }

    public function geographic(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);

        $ordersByCountry = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, now()])
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(shipping_address, "$.country")) as country, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('country')
            ->orderByDesc('revenue')
            ->get();

        $ordersByState = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, now()])
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(shipping_address, "$.state")) as state, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('state')
            ->orderByDesc('revenue')
            ->limit(20)
            ->get();

        $ordersByCity = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, now()])
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(shipping_address, "$.city")) as city, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('city')
            ->orderByDesc('revenue')
            ->limit(20)
            ->get();

        return view('admin.reports.geographic', compact(
            'ordersByCountry', 'ordersByState', 'ordersByCity', 'period'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->get('type');
        $format = $request->get('format', 'csv');
        $period = $request->get('period', '30');

        // Implementation would depend on your export library
        // This is a placeholder for the actual implementation
        
        return response()->json([
            'message' => 'Export functionality will be implemented based on your preferred export library',
            'type' => $type,
            'format' => $format,
            'period' => $period
        ]);
    }
}
