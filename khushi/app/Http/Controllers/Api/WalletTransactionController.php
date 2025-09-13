<?php

namespace App\Http\Controllers\Api;

use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class WalletTransactionController extends Controller
{
    /**
     * Display a listing of wallet transactions
     */
    public function index(Request $request): JsonResponse
    {
        $query = WalletTransaction::with(['user']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
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
            'message' => 'Wallet transactions retrieved successfully'
        ]);
    }

    /**
     * Store a newly created wallet transaction
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'reference_id' => 'nullable|string|max:255',
            'reference_type' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($validated['user_id']);

            // Check if user has sufficient balance for debit
            if ($validated['type'] === 'debit' && $user->wallet_balance < $validated['amount']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient wallet balance'
                ], 400);
            }

            // Generate transaction number
            $validated['transaction_number'] = 'WT-' . strtoupper(uniqid());
            $validated['status'] = 'completed';
            $validated['completed_at'] = now();

            // Create transaction
            $transaction = WalletTransaction::create($validated);

            // Update user wallet balance
            if ($validated['type'] === 'credit') {
                $user->increment('wallet_balance', $validated['amount']);
            } else {
                $user->decrement('wallet_balance', $validated['amount']);
            }

            DB::commit();

            $transaction->load(['user']);

            return response()->json([
                'success' => true,
                'data' => $transaction,
                'message' => 'Wallet transaction created successfully'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified wallet transaction
     */
    public function show(WalletTransaction $walletTransaction): JsonResponse
    {
        $walletTransaction->load(['user']);

        return response()->json([
            'success' => true,
            'data' => $walletTransaction,
            'message' => 'Wallet transaction retrieved successfully'
        ]);
    }

    /**
     * Get user wallet transactions
     */
    public function userTransactions(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        
        $query = WalletTransaction::where('user_id', $userId);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('created_at', 'desc')
                             ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $transactions,
            'message' => 'User wallet transactions retrieved successfully'
        ]);
    }

    /**
     * Get wallet balance
     */
    public function balance(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        $user = User::findOrFail($userId);

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'balance' => $user->wallet_balance,
                'formatted_balance' => number_format($user->wallet_balance, 2)
            ],
            'message' => 'Wallet balance retrieved successfully'
        ]);
    }

    /**
     * Get wallet statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = WalletTransaction::query();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total_transactions' => $query->count(),
            'total_credits' => $query->where('type', 'credit')->sum('amount'),
            'total_debits' => $query->where('type', 'debit')->sum('amount'),
            'credit_transactions' => $query->where('type', 'credit')->count(),
            'debit_transactions' => $query->where('type', 'debit')->count(),
            'net_amount' => $query->where('type', 'credit')->sum('amount') - 
                          $query->where('type', 'debit')->sum('amount')
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Wallet statistics retrieved successfully'
        ]);
    }
}
