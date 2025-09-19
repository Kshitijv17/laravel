<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = Product::with(['category']);

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'in_stock':
                    $query->where('stock', '>', 10); // Consider > 10 as in stock
                    break;
                case 'low_stock':
                    $query->whereBetween('stock', [1, 10]); // 1-10 is low stock
                    break;
                case 'out_of_stock':
                    $query->where('stock', '<=', 0);
                    break;
            }
        }

        $products = $query->orderBy('name')->paginate(20);
        $categories = Category::where('status', 'active')->get();

        // Calculate stats
        $stats = [
            'total_products' => Product::count(),
            'in_stock' => Product::where('stock', '>', 10)->count(),
            'low_stock' => Product::whereBetween('stock', [1, 10])->count(),
            'out_of_stock' => Product::where('stock', '<=', 0)->count(),
        ];

        return view('admin.inventory.index', compact('products', 'categories', 'stats'));
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:adjustment_increase,adjustment_decrease,damage,return',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            
            $this->inventoryService->recordMovement(
                $product,
                $request->type,
                $request->quantity,
                $request->reason
            );

            return response()->json([
                'success' => true,
                'message' => 'Stock adjustment applied successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function movements($productId)
    {
        $product = Product::findOrFail($productId);
        $movements = InventoryMovement::where('product_id', $productId)
            ->with('reference')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.movements', compact('product', 'movements'));
    }

    public function forecast($productId)
    {
        $product = Product::findOrFail($productId);
        $forecast = $this->inventoryService->getForecast($product, 30);
        
        return view('admin.inventory.forecast', compact('product', 'forecast'));
    }

    public function report(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        
        $report = $this->inventoryService->generateStockReport($startDate, $endDate);
        
        if ($request->get('format') === 'pdf') {
            return $this->generatePdfReport($report);
        }
        
        if ($request->get('format') === 'csv') {
            return $this->generateCsvReport($report);
        }
        
        return view('admin.inventory.report', compact('report', 'startDate', 'endDate'));
    }

    public function lowStockAlert()
    {
        $lowStockProducts = $this->inventoryService->getLowStockProducts();
        $reorderSuggestions = $this->inventoryService->getReorderSuggestions();
        
        return view('admin.inventory.low-stock', compact('lowStockProducts', 'reorderSuggestions'));
    }

    public function bulkAdjustment(Request $request)
    {
        $request->validate([
            'adjustments' => 'required|array',
            'adjustments.*.product_id' => 'required|exists:products,id',
            'adjustments.*.type' => 'required|in:adjustment_increase,adjustment_decrease,damage,return',
            'adjustments.*.quantity' => 'required|integer|min:1',
            'adjustments.*.reason' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->adjustments as $adjustment) {
                $product = Product::findOrFail($adjustment['product_id']);
                
                $this->inventoryService->recordMovement(
                    $product,
                    $adjustment['type'],
                    $adjustment['quantity'],
                    $adjustment['reason'] ?? null
                );
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Bulk adjustments applied successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function exportMovements(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        $format = $request->get('format', 'csv');
        
        $movements = InventoryMovement::with(['product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($format === 'csv') {
            return $this->exportMovementsCsv($movements);
        }
        
        return $this->exportMovementsPdf($movements);
    }

    private function generatePdfReport($report)
    {
        // PDF generation logic here
        // You can use libraries like DomPDF or TCPDF
        return response()->json(['message' => 'PDF generation not implemented yet']);
    }

    private function generateCsvReport($report)
    {
        $filename = 'inventory-report-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($report) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Product', 'SKU', 'Current Stock', 'Reserved', 'Available', 
                'Reorder Level', 'Status', 'Last Movement'
            ]);
            
            foreach ($report['products'] as $product) {
                fputcsv($file, [
                    $product['name'],
                    $product['sku'],
                    $product['current_stock'],
                    $product['reserved'],
                    $product['available'],
                    $product['reorder_level'],
                    $product['status'],
                    $product['last_movement']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportMovementsCsv($movements)
    {
        $filename = 'inventory-movements-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($movements) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Date', 'Product', 'Type', 'Quantity', 'Reason', 'Reference'
            ]);
            
            foreach ($movements as $movement) {
                fputcsv($file, [
                    $movement->created_at->format('Y-m-d H:i:s'),
                    $movement->product->name,
                    $movement->type,
                    $movement->quantity,
                    $movement->reason,
                    $movement->reference_type . '#' . $movement->reference_id
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportMovementsPdf($movements)
    {
        // PDF export logic here
        return response()->json(['message' => 'PDF export not implemented yet']);
    }
}
