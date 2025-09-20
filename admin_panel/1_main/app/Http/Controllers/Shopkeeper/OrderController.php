<?php

namespace App\Http\Controllers\Shopkeeper;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of shopkeeper's orders
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop) {
            return redirect()->route('shopkeeper.shop.create');
        }

        $query = $shop->orders()->with(['user', 'items']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics for the shop
        $stats = [
            'total_orders' => $shop->orders()->count(),
            'pending_orders' => $shop->orders()->where('status', 'pending')->count(),
            'processing_orders' => $shop->orders()->where('status', 'processing')->count(),
            'shipped_orders' => $shop->orders()->where('status', 'shipped')->count(),
            'delivered_orders' => $shop->orders()->where('status', 'delivered')->count(),
            'cancelled_orders' => $shop->orders()->where('status', 'cancelled')->count(),
            'total_revenue' => $shop->orders()->where('payment_status', 'paid')->sum('total_amount'),
            'pending_revenue' => $shop->orders()->where('payment_status', 'pending')->sum('total_amount'),
        ];

        return view('shopkeeper.orders.index', compact('orders', 'stats', 'shop'));
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $user = auth()->user();
        $shop = $user->shop;

        // Ensure the order belongs to the current shopkeeper's shop
        if (!$shop || $order->shop_id !== $shop->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['user', 'items.product', 'shop']);
        return view('shopkeeper.orders.show', compact('order', 'shop'));
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        $user = auth()->user();
        $shop = $user->shop;

        // Ensure the order belongs to the current shopkeeper's shop
        if (!$shop || $order->shop_id !== $shop->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['user', 'items.product', 'shop']);
        return view('shopkeeper.orders.edit', compact('order', 'shop'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $user = auth()->user();
        $shop = $user->shop;

        // Ensure the order belongs to the current shopkeeper's shop
        if (!$shop || $order->shop_id !== $shop->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatuses())),
            'payment_status' => 'required|in:' . implode(',', array_keys(Order::getPaymentStatuses())),
            'tracking_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $order->status;
        
        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'tracking_number' => $request->tracking_number,
            'notes' => $request->notes,
        ]);

        // Update timestamps based on status changes
        if ($oldStatus !== $request->status) {
            if ($request->status === Order::STATUS_SHIPPED && !$order->shipped_at) {
                $order->update(['shipped_at' => now()]);
            }
            if ($request->status === Order::STATUS_DELIVERED && !$order->delivered_at) {
                $order->update(['delivered_at' => now()]);
            }
        }

        return redirect()->route('shopkeeper.orders.show', $order)
                       ->with('success', 'Order updated successfully!');
    }

    /**
     * Update order status via AJAX
     */
    public function updateStatus(Request $request, Order $order)
    {
        $user = auth()->user();
        $shop = $user->shop;

        // Ensure the order belongs to the current shopkeeper's shop
        if (!$shop || $order->shop_id !== $shop->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatuses())),
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;

        // Update timestamps based on status changes
        if ($request->status === Order::STATUS_SHIPPED && !$order->shipped_at) {
            $order->shipped_at = now();
        }
        if ($request->status === Order::STATUS_DELIVERED && !$order->delivered_at) {
            $order->delivered_at = now();
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'status_display' => $order->status_display,
                'status_badge_color' => $order->status_badge_color,
            ]
        ]);
    }

    /**
     * Update payment status via AJAX
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $user = auth()->user();
        $shop = $user->shop;

        // Ensure the order belongs to the current shopkeeper's shop
        if (!$shop || $order->shop_id !== $shop->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'payment_status' => 'required|in:' . implode(',', array_keys(Order::getPaymentStatuses())),
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully',
            'order' => [
                'id' => $order->id,
                'payment_status' => $order->payment_status,
                'payment_status_display' => $order->payment_status_display,
                'payment_status_badge_color' => $order->payment_status_badge_color,
            ]
        ]);
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop) {
            return redirect()->route('shopkeeper.shop.create');
        }

        $query = $shop->orders()->with(['user', 'items']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $filename = 'shop_orders_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Order Number', 'Customer', 'Email', 'Status', 'Payment Status',
                'Total Amount', 'Items Count', 'Created Date', 'Shipped Date', 'Delivered Date'
            ]);

            // CSV data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->user->name ?? 'N/A',
                    $order->user->email ?? 'N/A',
                    $order->status_display,
                    $order->payment_status_display,
                    '$' . number_format($order->total_amount, 2),
                    $order->items->count(),
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->shipped_at ? $order->shipped_at->format('Y-m-d H:i:s') : '',
                    $order->delivered_at ? $order->delivered_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
