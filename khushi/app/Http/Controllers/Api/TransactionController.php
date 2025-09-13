<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions
     */
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::with(['user', 'order', 'payment']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')
                             ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $transactions,
            'message' => 'Transactions retrieved successfully'
        ]);
    }

    /**
     * Store a newly created transaction
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_id' => 'nullable|exists:orders,id',
            'payment_id' => 'nullable|exists:payments,id',
            'type' => 'required|in:payment,refund,wallet_credit,wallet_debit',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'status' => 'required|in:pending,completed,failed,cancelled',
            'reference_number' => 'nullable|string|max:255',
            'gateway' => 'nullable|string|max:255',
            'gateway_transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        // Generate reference number if not provided
        if (!isset($validated['reference_number'])) {
            $validated['reference_number'] = 'TXN-' . strtoupper(uniqid());
        }

        $transaction = Transaction::create($validated);
        $transaction->load(['user', 'order', 'payment']);

        return response()->json([
            'success' => true,
            'data' => $transaction,
            'message' => 'Transaction created successfully'
        ], 201);
    }

    /**
     * Display the specified transaction
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load(['user', 'order', 'payment']);

        return response()->json([
            'success' => true,
            'data' => $transaction,
            'message' => 'Transaction retrieved successfully'
        ]);
    }

    /**
     * Update the specified transaction
     */
    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,completed,failed,cancelled',
            'gateway_transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $transaction->update($validated);
        $transaction->load(['user', 'order', 'payment']);

        return response()->json([
            'success' => true,
            'data' => $transaction,
            'message' => 'Transaction updated successfully'
        ]);
    }

    /**
     * Get transaction statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = Transaction::query();

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total_transactions' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'completed_transactions' => $query->where('status', 'completed')->count(),
            'completed_amount' => $query->where('status', 'completed')->sum('amount'),
            'pending_transactions' => $query->where('status', 'pending')->count(),
            'pending_amount' => $query->where('status', 'pending')->sum('amount'),
            'failed_transactions' => $query->where('status', 'failed')->count(),
            'failed_amount' => $query->where('status', 'failed')->sum('amount'),
            'payment_transactions' => $query->where('type', 'payment')->count(),
            'refund_transactions' => $query->where('type', 'refund')->count(),
            'wallet_transactions' => $query->whereIn('type', ['wallet_credit', 'wallet_debit'])->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Transaction statistics retrieved successfully'
        ]);
    }

    /**
     * Get user transactions
     */
    public function userTransactions(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        
        $query = Transaction::where('user_id', $userId)
                           ->with(['order', 'payment']);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('created_at', 'desc')
                             ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $transactions,
            'message' => 'User transactions retrieved successfully'
        ]);
    }
}
