<?php

namespace App\Services;

use App\Models\Analytics;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AnalyticsService
{
    public function trackEvent(string $eventType, array $data = [])
    {
        $sessionId = session()->getId();
        $userId = auth()->id();
        
        Analytics::create([
            'event_type' => $eventType,
            'event_name' => $data['event_name'] ?? null,
            'user_id' => $userId,
            'session_id' => $sessionId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'referrer' => request()->header('referer'),
            'data' => $data,
            'created_at' => now()
        ]);
    }

    public function getDashboardStats($period = 'month')
    {
        $cacheKey = "analytics_dashboard_{$period}";
        
        return Cache::remember($cacheKey, 3600, function () use ($period) {
            $dateRange = $this->getDateRange($period);
            
            return [
                'revenue' => $this->getRevenueStats($dateRange),
                'orders' => $this->getOrderStats($dateRange),
                'products' => $this->getProductStats($dateRange),
                'users' => $this->getUserStats($dateRange),
                'traffic' => $this->getTrafficStats($dateRange),
                'conversion' => $this->getConversionStats($dateRange)
            ];
        });
    }

    public function getRevenueStats($dateRange)
    {
        $current = Order::whereBetween('created_at', $dateRange['current'])
            ->where('status', 'completed')
            ->sum('total_amount');
            
        $previous = Order::whereBetween('created_at', $dateRange['previous'])
            ->where('status', 'completed')
            ->sum('total_amount');
            
        $growth = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
        
        return [
            'current' => $current,
            'previous' => $previous,
            'growth' => round($growth, 2),
            'chart_data' => $this->getRevenueChartData($dateRange['current'])
        ];
    }

    public function getOrderStats($dateRange)
    {
        $current = Order::whereBetween('created_at', $dateRange['current'])->count();
        $previous = Order::whereBetween('created_at', $dateRange['previous'])->count();
        $growth = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
        
        return [
            'current' => $current,
            'previous' => $previous,
            'growth' => round($growth, 2),
            'average_value' => $current > 0 ? Order::whereBetween('created_at', $dateRange['current'])
                ->where('status', 'completed')
                ->avg('total_amount') : 0
        ];
    }

    public function getProductStats($dateRange)
    {
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', $dateRange['current'])
            ->where('orders.status', 'completed')
            ->select('products.name', 'products.id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        $mostViewed = Analytics::productViews()
            ->whereBetween('created_at', $dateRange['current'])
            ->select('data->product_id as product_id', DB::raw('COUNT(*) as views'))
            ->groupBy('data->product_id')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get();

        return [
            'top_selling' => $topProducts,
            'most_viewed' => $mostViewed,
            'total_views' => Analytics::productViews()
                ->whereBetween('created_at', $dateRange['current'])
                ->count()
        ];
    }

    public function getUserStats($dateRange)
    {
        $newUsers = User::whereBetween('created_at', $dateRange['current'])->count();
        $activeUsers = Analytics::whereBetween('created_at', $dateRange['current'])
            ->distinct('user_id')
            ->whereNotNull('user_id')
            ->count();
            
        $returningUsers = Analytics::whereBetween('created_at', $dateRange['current'])
            ->whereNotNull('user_id')
            ->whereIn('user_id', function($query) use ($dateRange) {
                $query->select('user_id')
                    ->from('analytics')
                    ->where('created_at', '<', $dateRange['current'][0])
                    ->whereNotNull('user_id');
            })
            ->distinct('user_id')
            ->count();

        return [
            'new_users' => $newUsers,
            'active_users' => $activeUsers,
            'returning_users' => $returningUsers,
            'retention_rate' => $activeUsers > 0 ? round(($returningUsers / $activeUsers) * 100, 2) : 0
        ];
    }

    public function getTrafficStats($dateRange)
    {
        $pageViews = Analytics::pageViews()
            ->whereBetween('created_at', $dateRange['current'])
            ->count();
            
        $uniqueVisitors = Analytics::pageViews()
            ->whereBetween('created_at', $dateRange['current'])
            ->distinct('session_id')
            ->count();
            
        $bounceRate = $this->calculateBounceRate($dateRange['current']);
        
        $topPages = Analytics::pageViews()
            ->whereBetween('created_at', $dateRange['current'])
            ->select('url', DB::raw('COUNT(*) as views'))
            ->groupBy('url')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get();

        return [
            'page_views' => $pageViews,
            'unique_visitors' => $uniqueVisitors,
            'bounce_rate' => $bounceRate,
            'top_pages' => $topPages,
            'avg_session_duration' => $this->getAverageSessionDuration($dateRange['current'])
        ];
    }

    public function getConversionStats($dateRange)
    {
        $visitors = Analytics::whereBetween('created_at', $dateRange['current'])
            ->distinct('session_id')
            ->count();
            
        $purchases = Analytics::purchases()
            ->whereBetween('created_at', $dateRange['current'])
            ->distinct('session_id')
            ->count();
            
        $addToCarts = Analytics::addToCart()
            ->whereBetween('created_at', $dateRange['current'])
            ->distinct('session_id')
            ->count();

        $conversionRate = $visitors > 0 ? round(($purchases / $visitors) * 100, 2) : 0;
        $cartConversionRate = $addToCarts > 0 ? round(($purchases / $addToCarts) * 100, 2) : 0;

        return [
            'conversion_rate' => $conversionRate,
            'cart_conversion_rate' => $cartConversionRate,
            'abandonment_rate' => 100 - $cartConversionRate,
            'funnel_data' => [
                'visitors' => $visitors,
                'product_views' => Analytics::productViews()->whereBetween('created_at', $dateRange['current'])->distinct('session_id')->count(),
                'add_to_cart' => $addToCarts,
                'purchases' => $purchases
            ]
        ];
    }

    public function getSearchAnalytics($dateRange)
    {
        $searches = Analytics::searches()
            ->whereBetween('created_at', $dateRange['current'])
            ->get();

        $topSearches = $searches->groupBy('data.query')
            ->map(function ($group) {
                return [
                    'query' => $group->first()->data['query'] ?? '',
                    'count' => $group->count(),
                    'results' => $group->avg('data.results_count') ?? 0
                ];
            })
            ->sortByDesc('count')
            ->take(20)
            ->values();

        $noResultsSearches = $searches->where('data.results_count', 0)
            ->groupBy('data.query')
            ->map(function ($group) {
                return [
                    'query' => $group->first()->data['query'] ?? '',
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('count')
            ->take(10)
            ->values();

        return [
            'total_searches' => $searches->count(),
            'unique_searches' => $searches->unique('data.query')->count(),
            'top_searches' => $topSearches,
            'no_results_searches' => $noResultsSearches,
            'average_results' => $searches->avg('data.results_count') ?? 0
        ];
    }

    private function getDateRange($period)
    {
        switch ($period) {
            case 'today':
                return [
                    'current' => [today(), today()->endOfDay()],
                    'previous' => [yesterday(), yesterday()->endOfDay()]
                ];
            case 'week':
                return [
                    'current' => [now()->startOfWeek(), now()->endOfWeek()],
                    'previous' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]
                ];
            case 'month':
                return [
                    'current' => [now()->startOfMonth(), now()->endOfMonth()],
                    'previous' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]
                ];
            case 'year':
                return [
                    'current' => [now()->startOfYear(), now()->endOfYear()],
                    'previous' => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()]
                ];
            default:
                return [
                    'current' => [now()->startOfMonth(), now()->endOfMonth()],
                    'previous' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]
                ];
        }
    }

    private function getRevenueChartData($dateRange)
    {
        return Order::whereBetween('created_at', $dateRange)
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'revenue' => (float) $item->revenue
                ];
            });
    }

    private function calculateBounceRate($dateRange)
    {
        $singlePageSessions = Analytics::pageViews()
            ->whereBetween('created_at', $dateRange)
            ->select('session_id', DB::raw('COUNT(*) as page_count'))
            ->groupBy('session_id')
            ->having('page_count', '=', 1)
            ->count();

        $totalSessions = Analytics::pageViews()
            ->whereBetween('created_at', $dateRange)
            ->distinct('session_id')
            ->count();

        return $totalSessions > 0 ? round(($singlePageSessions / $totalSessions) * 100, 2) : 0;
    }

    private function getAverageSessionDuration($dateRange)
    {
        $sessions = Analytics::whereBetween('created_at', $dateRange)
            ->select('session_id', DB::raw('MIN(created_at) as start_time'), DB::raw('MAX(created_at) as end_time'))
            ->groupBy('session_id')
            ->get();

        if ($sessions->isEmpty()) {
            return 0;
        }

        $totalDuration = $sessions->sum(function ($session) {
            return Carbon::parse($session->end_time)->diffInSeconds(Carbon::parse($session->start_time));
        });

        return round($totalDuration / $sessions->count());
    }
}
