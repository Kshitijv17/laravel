<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    public function recordMovement($productId, $type, $quantity, $reason = null, $reference = null, $notes = null, $userId = null)
    {
        return DB::transaction(function () use ($productId, $type, $quantity, $reason, $reference, $notes, $userId) {
            $product = Product::lockForUpdate()->findOrFail($productId);
            $previousQuantity = $product->stock_quantity;
            $newQuantity = $previousQuantity + $quantity;

            // Validate stock levels for outward movements
            if ($quantity < 0 && $newQuantity < 0) {
                throw new \Exception('Insufficient stock. Available: ' . $previousQuantity . ', Required: ' . abs($quantity));
            }

            // Update product stock
            $product->update(['stock_quantity' => $newQuantity]);

            // Record movement
            $movement = InventoryMovement::create([
                'product_id' => $productId,
                'type' => $type,
                'quantity' => $quantity,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $newQuantity,
                'reason' => $reason,
                'reference_id' => $reference ? $reference->id : null,
                'reference_type' => $reference ? get_class($reference) : null,
                'notes' => $notes,
                'user_id' => $userId
            ]);

            // Log the movement
            Log::info('Inventory movement recorded', [
                'product_id' => $productId,
                'type' => $type,
                'quantity' => $quantity,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $newQuantity
            ]);

            // Check for low stock alerts
            $this->checkLowStockAlert($product);

            return $movement;
        });
    }

    public function adjustStock($productId, $newQuantity, $reason = 'Manual adjustment', $notes = null, $userId = null)
    {
        $product = Product::findOrFail($productId);
        $currentQuantity = $product->stock_quantity;
        $adjustmentQuantity = $newQuantity - $currentQuantity;

        return $this->recordMovement(
            $productId,
            InventoryMovement::TYPE_ADJUSTMENT,
            $adjustmentQuantity,
            $reason,
            null,
            $notes,
            $userId
        );
    }

    public function processOrder($order)
    {
        foreach ($order->orderItems as $item) {
            $this->recordMovement(
                $item->product_id,
                InventoryMovement::TYPE_SALE,
                -$item->quantity,
                'Order sale',
                $order,
                "Order #{$order->order_number}",
                $order->user_id
            );
        }
    }

    public function processReturn($order, $returnItems)
    {
        foreach ($returnItems as $item) {
            $this->recordMovement(
                $item['product_id'],
                InventoryMovement::TYPE_RETURN,
                $item['quantity'],
                'Order return',
                $order,
                "Return for Order #{$order->order_number}"
            );
        }
    }

    public function processPurchase($productId, $quantity, $reference = null, $userId = null)
    {
        return $this->recordMovement(
            $productId,
            InventoryMovement::TYPE_PURCHASE,
            $quantity,
            'Stock purchase',
            $reference,
            null,
            $userId
        );
    }

    public function recordDamage($productId, $quantity, $reason = 'Damaged goods', $notes = null, $userId = null)
    {
        return $this->recordMovement(
            $productId,
            InventoryMovement::TYPE_DAMAGE,
            -$quantity,
            $reason,
            null,
            $notes,
            $userId
        );
    }

    public function getMovementHistory($productId, $limit = 50)
    {
        return InventoryMovement::with(['user', 'reference'])
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getLowStockProducts($threshold = 10)
    {
        return Product::with(['category', 'brand'])
            ->where('stock_quantity', '<=', $threshold)
            ->where('status', 'active')
            ->orderBy('stock_quantity')
            ->get();
    }

    public function getOutOfStockProducts()
    {
        return Product::with(['category', 'brand'])
            ->where('stock_quantity', 0)
            ->where('status', 'active')
            ->get();
    }

    public function getInventoryValue()
    {
        return Product::where('status', 'active')
            ->selectRaw('SUM(stock_quantity * selling_price) as total_value')
            ->first()
            ->total_value ?? 0;
    }

    public function getStockMovementSummary($startDate = null, $endDate = null)
    {
        $query = InventoryMovement::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->selectRaw('
            type,
            COUNT(*) as movement_count,
            SUM(CASE WHEN quantity > 0 THEN quantity ELSE 0 END) as total_inward,
            SUM(CASE WHEN quantity < 0 THEN ABS(quantity) ELSE 0 END) as total_outward
        ')
        ->groupBy('type')
        ->get();
    }

    public function bulkUpdateStock($updates, $reason = 'Bulk update', $userId = null)
    {
        $results = [];

        DB::transaction(function () use ($updates, $reason, $userId, &$results) {
            foreach ($updates as $update) {
                try {
                    $movement = $this->adjustStock(
                        $update['product_id'],
                        $update['new_quantity'],
                        $reason,
                        $update['notes'] ?? null,
                        $userId
                    );
                    $results[] = ['success' => true, 'movement' => $movement];
                } catch (\Exception $e) {
                    $results[] = ['success' => false, 'error' => $e->getMessage()];
                }
            }
        });

        return $results;
    }

    public function generateStockReport($categoryId = null, $brandId = null)
    {
        $query = Product::with(['category', 'brand'])
            ->where('status', 'active');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        return $query->selectRaw('
            products.*,
            (stock_quantity * selling_price) as stock_value,
            CASE 
                WHEN stock_quantity = 0 THEN "Out of Stock"
                WHEN stock_quantity <= 10 THEN "Low Stock"
                WHEN stock_quantity <= 50 THEN "Medium Stock"
                ELSE "Good Stock"
            END as stock_status
        ')
        ->orderBy('stock_quantity')
        ->get();
    }

    private function checkLowStockAlert($product)
    {
        $lowStockThreshold = config('inventory.low_stock_threshold', 10);
        
        if ($product->stock_quantity <= $lowStockThreshold && $product->stock_quantity > 0) {
            // Trigger low stock alert
            Log::warning('Low stock alert', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'stock_quantity' => $product->stock_quantity
            ]);

            // You can add email notifications, webhooks, etc. here
        } elseif ($product->stock_quantity == 0) {
            // Trigger out of stock alert
            Log::warning('Out of stock alert', [
                'product_id' => $product->id,
                'product_name' => $product->name
            ]);
        }
    }

    public function forecastDemand($productId, $days = 30)
    {
        $movements = InventoryMovement::where('product_id', $productId)
            ->where('type', InventoryMovement::TYPE_SALE)
            ->where('created_at', '>=', now()->subDays($days * 2))
            ->selectRaw('DATE(created_at) as date, SUM(ABS(quantity)) as daily_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($movements->isEmpty()) {
            return 0;
        }

        $averageDailySales = $movements->avg('daily_sales');
        return ceil($averageDailySales * $days);
    }

    public function getReorderSuggestions()
    {
        $lowStockProducts = $this->getLowStockProducts(20);
        $suggestions = [];

        foreach ($lowStockProducts as $product) {
            $forecastedDemand = $this->forecastDemand($product->id, 30);
            $currentStock = $product->stock_quantity;
            $suggestedOrder = max(0, $forecastedDemand - $currentStock);

            if ($suggestedOrder > 0) {
                $suggestions[] = [
                    'product' => $product,
                    'current_stock' => $currentStock,
                    'forecasted_demand' => $forecastedDemand,
                    'suggested_order_quantity' => $suggestedOrder,
                    'urgency' => $currentStock == 0 ? 'critical' : ($currentStock <= 5 ? 'high' : 'medium')
                ];
            }
        }

        return collect($suggestions)->sortBy(function ($item) {
            return $item['urgency'] == 'critical' ? 0 : ($item['urgency'] == 'high' ? 1 : 2);
        });
    }
}
