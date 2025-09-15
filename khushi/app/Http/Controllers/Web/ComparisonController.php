<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ComparisonService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComparisonController extends Controller
{
    protected $comparisonService;
    protected $recommendationService;

    public function __construct(ComparisonService $comparisonService, RecommendationService $recommendationService)
    {
        $this->comparisonService = $comparisonService;
        $this->recommendationService = $recommendationService;
    }

    public function index()
    {
        $userId = Auth::id();
        $comparisonData = $this->comparisonService->getComparisonData($userId);
        
        return view('web.comparison.index', compact('comparisonData'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = Auth::id();
        $result = $this->comparisonService->addToComparison($request->product_id, $userId);

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = Auth::id();
        $result = $this->comparisonService->removeFromComparison($request->product_id, $userId);

        if ($request->ajax()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    public function clear()
    {
        $userId = Auth::id();
        $result = $this->comparisonService->clearComparison($userId);

        if (request()->ajax()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    public function count()
    {
        $userId = Auth::id();
        $count = $this->comparisonService->getComparisonCount($userId);

        return response()->json(['count' => $count]);
    }

    public function widget()
    {
        $userId = Auth::id();
        $comparisonData = $this->comparisonService->getComparisonData($userId);
        
        return view('web.comparison.widget', compact('comparisonData'));
    }

    public function compare(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:2|max:4',
            'products.*' => 'exists:products,id'
        ]);

        $userId = Auth::id();
        
        // Clear current comparison and add selected products
        $this->comparisonService->clearComparison($userId);
        
        foreach ($request->products as $productId) {
            $this->comparisonService->addToComparison($productId, $userId);
        }

        return redirect()->route('comparison.index');
    }

    public function export(Request $request)
    {
        $userId = Auth::id();
        $comparisonData = $this->comparisonService->getComparisonData($userId);
        
        if (empty($comparisonData['products'])) {
            return redirect()->back()->with('error', 'No products to export');
        }

        $format = $request->get('format', 'pdf');
        
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($comparisonData);
            case 'excel':
                return $this->exportToExcel($comparisonData);
            case 'csv':
                return $this->exportToCsv($comparisonData);
            default:
                return redirect()->back()->with('error', 'Invalid export format');
        }
    }

    public function share(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:2|max:4',
            'products.*' => 'exists:products,id'
        ]);

        // Generate a shareable link or code
        $shareCode = uniqid('comp_');
        
        // Store comparison data in cache for sharing
        cache()->put("shared_comparison:{$shareCode}", $request->products, now()->addDays(7));
        
        $shareUrl = route('comparison.shared', $shareCode);
        
        return response()->json([
            'success' => true,
            'share_url' => $shareUrl,
            'share_code' => $shareCode
        ]);
    }

    public function shared($shareCode)
    {
        $productIds = cache()->get("shared_comparison:{$shareCode}");
        
        if (!$productIds) {
            abort(404, 'Comparison not found or expired');
        }

        // Create temporary comparison data
        $comparisonData = $this->comparisonService->getComparisonData(null, $shareCode);
        
        return view('web.comparison.shared', compact('comparisonData', 'shareCode'));
    }

    private function exportToPdf($comparisonData)
    {
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('web.comparison.export.pdf', compact('comparisonData'));
        
        return $pdf->download('product-comparison.pdf');
    }

    private function exportToExcel($comparisonData)
    {
        // Implementation would depend on your Excel export library
        // This is a placeholder for the actual implementation
        return response()->json(['message' => 'Excel export not implemented yet']);
    }

    private function exportToCsv($comparisonData)
    {
        $filename = 'product-comparison-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($comparisonData) {
            $file = fopen('php://output', 'w');
            
            // Write headers
            $headers = ['Feature'];
            foreach ($comparisonData['products'] as $product) {
                $headers[] = $product['name'];
            }
            fputcsv($file, $headers);
            
            // Write comparison data
            foreach ($comparisonData['comparison_table'] as $section => $features) {
                fputcsv($file, [$section]); // Section header
                
                foreach ($features as $feature => $values) {
                    $row = [$feature];
                    $row = array_merge($row, $values);
                    fputcsv($file, $row);
                }
                
                fputcsv($file, []); // Empty row between sections
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
