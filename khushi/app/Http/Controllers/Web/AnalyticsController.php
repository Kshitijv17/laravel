<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function dashboard(Request $request)
    {
        $period = $request->get('period', 'month');
        $stats = $this->analyticsService->getDashboardStats($period);
        
        return view('admin.analytics.dashboard', compact('stats', 'period'));
    }

    public function revenue(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);
        
        $revenueStats = $this->analyticsService->getRevenueStats($dateRange);
        
        return view('admin.analytics.revenue', compact('revenueStats', 'period'));
    }

    public function products(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);
        
        $productStats = $this->analyticsService->getProductStats($dateRange);
        
        return view('admin.analytics.products', compact('productStats', 'period'));
    }

    public function users(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);
        
        $userStats = $this->analyticsService->getUserStats($dateRange);
        
        return view('admin.analytics.users', compact('userStats', 'period'));
    }

    public function traffic(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);
        
        $trafficStats = $this->analyticsService->getTrafficStats($dateRange);
        
        return view('admin.analytics.traffic', compact('trafficStats', 'period'));
    }

    public function conversion(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);
        
        $conversionStats = $this->analyticsService->getConversionStats($dateRange);
        
        return view('admin.analytics.conversion', compact('conversionStats', 'period'));
    }

    public function search(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);
        
        $searchStats = $this->analyticsService->getSearchAnalytics($dateRange);
        
        return view('admin.analytics.search', compact('searchStats', 'period'));
    }

    public function track(Request $request)
    {
        $request->validate([
            'event_type' => 'required|string|in:page_view,product_view,add_to_cart,purchase,search',
            'data' => 'array'
        ]);

        $this->analyticsService->trackEvent(
            $request->event_type,
            $request->data ?? []
        );

        return response()->json(['success' => true]);
    }

    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'revenue');
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        switch ($type) {
            case 'revenue':
                $data = $this->analyticsService->getRevenueStats($dateRange)['chart_data'];
                break;
            case 'orders':
                $data = $this->getOrdersChartData($dateRange);
                break;
            case 'traffic':
                $data = $this->getTrafficChartData($dateRange);
                break;
            case 'conversion':
                $data = $this->getConversionChartData($dateRange);
                break;
            default:
                $data = [];
        }

        return response()->json($data);
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'dashboard');
        $period = $request->get('period', 'month');
        $format = $request->get('format', 'csv');

        // Generate export data based on type
        $data = $this->getExportData($type, $period);

        if ($format === 'pdf') {
            return $this->exportToPdf($data, $type, $period);
        } else {
            return $this->exportToCsv($data, $type, $period);
        }
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

    private function getOrdersChartData($dateRange)
    {
        return \App\Models\Order::whereBetween('created_at', $dateRange['current'])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'orders' => (int) $item->orders
                ];
            });
    }

    private function getTrafficChartData($dateRange)
    {
        return \App\Models\Analytics::pageViews()
            ->whereBetween('created_at', $dateRange['current'])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as views, COUNT(DISTINCT session_id) as visitors')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'views' => (int) $item->views,
                    'visitors' => (int) $item->visitors
                ];
            });
    }

    private function getConversionChartData($dateRange)
    {
        $data = [];
        $current = Carbon::parse($dateRange['current'][0]);
        $end = Carbon::parse($dateRange['current'][1]);

        while ($current <= $end) {
            $dayStart = $current->copy()->startOfDay();
            $dayEnd = $current->copy()->endOfDay();

            $visitors = \App\Models\Analytics::whereBetween('created_at', [$dayStart, $dayEnd])
                ->distinct('session_id')->count();
            
            $purchases = \App\Models\Analytics::purchases()
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->distinct('session_id')->count();

            $conversionRate = $visitors > 0 ? round(($purchases / $visitors) * 100, 2) : 0;

            $data[] = [
                'date' => $current->format('M d'),
                'conversion_rate' => $conversionRate,
                'visitors' => $visitors,
                'purchases' => $purchases
            ];

            $current->addDay();
        }

        return $data;
    }

    private function getExportData($type, $period)
    {
        $dateRange = $this->getDateRange($period);
        
        switch ($type) {
            case 'revenue':
                return $this->analyticsService->getRevenueStats($dateRange);
            case 'products':
                return $this->analyticsService->getProductStats($dateRange);
            case 'users':
                return $this->analyticsService->getUserStats($dateRange);
            case 'traffic':
                return $this->analyticsService->getTrafficStats($dateRange);
            case 'conversion':
                return $this->analyticsService->getConversionStats($dateRange);
            default:
                return $this->analyticsService->getDashboardStats($period);
        }
    }

    private function exportToCsv($data, $type, $period)
    {
        $filename = "analytics_{$type}_{$period}_" . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers based on data structure
            if (isset($data['revenue'])) {
                fputcsv($file, ['Metric', 'Current', 'Previous', 'Growth %']);
                fputcsv($file, ['Revenue', $data['revenue']['current'], $data['revenue']['previous'], $data['revenue']['growth']]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportToPdf($data, $type, $period)
    {
        // This would require a PDF library like DomPDF or TCPDF
        // For now, return JSON response
        return response()->json($data);
    }
}
