<?php

namespace App\Http\Controllers\Api;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['order.user', 'user']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        if ($request->has('overdue')) {
            $query->where('due_date', '<', now())
                  ->where('status', '!=', 'paid');
        }

        $invoices = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $invoices,
            'message' => 'Invoices retrieved successfully'
        ]);
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'user_id' => 'required|exists:users,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        // Generate invoice number
        $validated['invoice_number'] = 'INV-' . date('Y') . '-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT);
        $validated['status'] = 'pending';

        $invoice = Invoice::create($validated);
        $invoice->load(['order', 'user']);

        return response()->json([
            'success' => true,
            'data' => $invoice,
            'message' => 'Invoice created successfully'
        ], 201);
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['order.items.product', 'user']);

        return response()->json([
            'success' => true,
            'data' => $invoice,
            'message' => 'Invoice retrieved successfully'
        ]);
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        $validated = $request->validate([
            'due_date' => 'sometimes|date',
            'subtotal' => 'sometimes|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:pending,sent,paid,overdue,cancelled',
            'notes' => 'nullable|string'
        ]);

        // Set paid date if status is paid
        if (isset($validated['status']) && $validated['status'] === 'paid' && !$invoice->paid_at) {
            $validated['paid_at'] = now();
        }

        $invoice->update($validated);
        $invoice->load(['order', 'user']);

        return response()->json([
            'success' => true,
            'data' => $invoice,
            'message' => 'Invoice updated successfully'
        ]);
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Invoice $invoice): JsonResponse
    {
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $invoice,
            'message' => 'Invoice marked as paid successfully'
        ]);
    }

    /**
     * Send invoice to customer
     */
    public function send(Invoice $invoice): JsonResponse
    {
        $invoice->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);

        // Here you would typically send the invoice via email
        // For now, we'll just update the status

        return response()->json([
            'success' => true,
            'data' => $invoice,
            'message' => 'Invoice sent successfully'
        ]);
    }

    /**
     * Get invoice statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = Invoice::query();

        if ($request->has('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $stats = [
            'total_invoices' => $query->count(),
            'total_amount' => $query->sum('total_amount'),
            'pending_invoices' => $query->where('status', 'pending')->count(),
            'pending_amount' => $query->where('status', 'pending')->sum('total_amount'),
            'paid_invoices' => $query->where('status', 'paid')->count(),
            'paid_amount' => $query->where('status', 'paid')->sum('total_amount'),
            'overdue_invoices' => $query->where('due_date', '<', now())
                ->where('status', '!=', 'paid')->count(),
            'overdue_amount' => $query->where('due_date', '<', now())
                ->where('status', '!=', 'paid')->sum('total_amount')
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Invoice statistics retrieved successfully'
        ]);
    }
}
