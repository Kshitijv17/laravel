<?php

namespace App\Http\Controllers\Api;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['order.user', 'user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        $payments = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $payments,
            'message' => 'Payments retrieved successfully'
        ]);
    }

    /**
     * Process a new payment
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'gateway' => 'required|string',
            'gateway_transaction_id' => 'nullable|string',
            'payment_method' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($validated['order_id']);
            
            if ($order->total_amount != $validated['amount']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount does not match order total'
                ], 400);
            }

            $payment = Payment::create([
                'user_id' => $validated['user_id'],
                'order_id' => $validated['order_id'],
                'amount' => $validated['amount'],
                'gateway' => $validated['gateway'],
                'gateway_transaction_id' => $validated['gateway_transaction_id'],
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
                'transaction_id' => 'TXN-' . time() . '-' . rand(1000, 9999)
            ]);

            // Simulate payment processing
            $paymentSuccess = $this->processPayment($payment);

            if ($paymentSuccess) {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now()
                ]);
                
                $order->update(['status' => 'confirmed']);
            } else {
                $payment->update(['status' => 'failed']);
            }

            DB::commit();

            return response()->json([
                'success' => $paymentSuccess,
                'data' => $payment,
                'message' => $paymentSuccess ? 'Payment processed successfully' : 'Payment failed'
            ], $paymentSuccess ? 201 : 400);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment): JsonResponse
    {
        $payment->load(['user', 'order']);

        return response()->json([
            'success' => true,
            'data' => $payment,
            'message' => 'Payment retrieved successfully'
        ]);
    }

    /**
     * Update payment status
     */
    public function update(Request $request, Payment $payment): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
            'gateway_response' => 'nullable|array'
        ]);

        $payment->update($validated);

        if ($validated['status'] === 'completed' && !$payment->paid_at) {
            $payment->update(['paid_at' => now()]);
            $payment->order->update(['status' => 'confirmed']);
        }

        return response()->json([
            'success' => true,
            'data' => $payment,
            'message' => 'Payment updated successfully'
        ]);
    }

    /**
     * Refund a payment
     */
    public function refund(Payment $payment): JsonResponse
    {
        if (!$payment->is_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Only completed payments can be refunded'
            ], 400);
        }

        if ($payment->is_refunded) {
            return response()->json([
                'success' => false,
                'message' => 'Payment already refunded'
            ], 400);
        }

        // Process refund logic here
        $payment->update([
            'status' => 'refunded',
            'refunded_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $payment,
            'message' => 'Payment refunded successfully'
        ]);
    }

    /**
     * Get payment statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_payments' => Payment::count(),
            'completed_payments' => Payment::completed()->count(),
            'failed_payments' => Payment::failed()->count(),
            'total_revenue' => Payment::completed()->sum('amount'),
            'pending_payments' => Payment::pending()->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Payment statistics retrieved successfully'
        ]);
    }

    /**
     * Simulate payment processing
     */
    private function processPayment(Payment $payment): bool
    {
        // Simulate payment gateway processing
        // In real implementation, integrate with actual payment gateways
        return rand(1, 10) > 2; // 80% success rate for demo
    }
}
