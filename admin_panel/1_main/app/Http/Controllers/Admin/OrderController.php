<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])
                     ->orderBy('created_at', 'desc');

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

        $orders = $query->paginate(20);

        // Statistics for dashboard
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::pending()->count(),
            'processing_orders' => Order::processing()->count(),
            'shipped_orders' => Order::shipped()->count(),
            'delivered_orders' => Order::delivered()->count(),
            'cancelled_orders' => Order::cancelled()->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'pending_revenue' => Order::where('payment_status', 'pending')->sum('total_amount'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'items.product']);
        $users = User::whereIn('role', ['customer', 'guest'])->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        return view('admin.orders.edit', compact('order', 'users', 'products'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatuses())),
            'payment_status' => 'required|in:' . implode(',', array_keys(Order::getPaymentStatuses())),
            'payment_method' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'shipping_address' => 'nullable|array',
            'billing_address' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $order->status;
            
            $order->update([
                'status' => $request->status,
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_method,
                'tracking_number' => $request->tracking_number,
                'notes' => $request->notes,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address,
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

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                           ->with('success', 'Order updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }

    /**
     * Update order status via AJAX
     */
    public function updateStatus(Request $request, Order $order)
    {
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
     * Cancel an order
     */
    public function cancel(Order $order)
    {
        if (!$order->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be cancelled'
            ], 400);
        }

        $order->update(['status' => Order::STATUS_CANCELLED]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'status_display' => $order->status_display,
                'status_badge_color' => $order->status_badge_color,
            ]
        ]);
    }

    /**
     * Refund an order
     */
    public function refund(Request $request, Order $order)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . $order->total_amount,
            'refund_reason' => 'nullable|string|max:500',
        ]);

        if (!$order->canBeRefunded()) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be refunded'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $refundAmount = $request->refund_amount;
            $isPartialRefund = $refundAmount < $order->total_amount;

            $order->update([
                'status' => Order::STATUS_REFUNDED,
                'payment_status' => $isPartialRefund ? Order::PAYMENT_PARTIALLY_REFUNDED : Order::PAYMENT_REFUNDED,
                'notes' => ($order->notes ? $order->notes . "\n\n" : '') . 
                          "Refund: $" . number_format($refundAmount, 2) . 
                          ($request->refund_reason ? " - Reason: " . $request->refund_reason : "")
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order refunded successfully',
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'status_display' => $order->status_display,
                    'payment_status_display' => $order->payment_status_display,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process refund: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified order
     */
    public function destroy(Order $order)
    {
        try {
            $order->delete();

            return redirect()->route('admin.orders.index')
                           ->with('success', 'Order deleted successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete order: ' . $e->getMessage()]);
        }
    }

    /**
     * Restore a soft-deleted order
     */
    public function restore($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->restore();

        return redirect()->route('admin.orders.show', $order)
                       ->with('success', 'Order restored successfully!');
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $query = Order::with(['user', 'items']);

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

        $filename = 'orders_' . date('Y-m-d_H-i-s') . '.csv';
        
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
