<?php

namespace App\Http\Controllers\Api;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of support tickets
     */
    public function index(Request $request): JsonResponse
    {
        $query = SupportTicket::with(['user', 'assignedTo']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->has('search')) {
            $query->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $tickets = $query->orderBy('created_at', 'desc')
                        ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $tickets,
            'message' => 'Support tickets retrieved successfully'
        ]);
    }

    /**
     * Store a newly created support ticket
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,billing,general,complaint,feature_request',
            'priority' => 'required|in:low,medium,high,urgent',
            'attachments' => 'nullable|array'
        ]);

        // Generate ticket number
        $validated['ticket_number'] = 'TKT-' . strtoupper(uniqid());
        $validated['status'] = 'open';

        $ticket = SupportTicket::create($validated);
        $ticket->load(['user']);

        return response()->json([
            'success' => true,
            'data' => $ticket,
            'message' => 'Support ticket created successfully'
        ], 201);
    }

    /**
     * Display the specified support ticket
     */
    public function show(SupportTicket $supportTicket): JsonResponse
    {
        $supportTicket->load(['user', 'assignedTo']);

        return response()->json([
            'success' => true,
            'data' => $supportTicket,
            'message' => 'Support ticket retrieved successfully'
        ]);
    }

    /**
     * Update the specified support ticket
     */
    public function update(Request $request, SupportTicket $supportTicket): JsonResponse
    {
        $validated = $request->validate([
            'subject' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'sometimes|in:technical,billing,general,complaint,feature_request',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'status' => 'sometimes|in:open,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:admins,id',
            'resolution' => 'nullable|string',
            'attachments' => 'nullable|array'
        ]);

        // Set resolved/closed timestamps
        if (isset($validated['status'])) {
            if ($validated['status'] === 'resolved' && !$supportTicket->resolved_at) {
                $validated['resolved_at'] = now();
            }
            if ($validated['status'] === 'closed' && !$supportTicket->closed_at) {
                $validated['closed_at'] = now();
            }
        }

        $supportTicket->update($validated);
        $supportTicket->load(['user', 'assignedTo']);

        return response()->json([
            'success' => true,
            'data' => $supportTicket,
            'message' => 'Support ticket updated successfully'
        ]);
    }

    /**
     * Assign ticket to admin
     */
    public function assign(Request $request, SupportTicket $supportTicket): JsonResponse
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:admins,id'
        ]);

        $supportTicket->update([
            'assigned_to' => $validated['assigned_to'],
            'status' => 'in_progress'
        ]);

        $supportTicket->load(['assignedTo']);

        return response()->json([
            'success' => true,
            'data' => $supportTicket,
            'message' => 'Ticket assigned successfully'
        ]);
    }

    /**
     * Close support ticket
     */
    public function close(SupportTicket $supportTicket): JsonResponse
    {
        $supportTicket->update([
            'status' => 'closed',
            'closed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $supportTicket,
            'message' => 'Support ticket closed successfully'
        ]);
    }

    /**
     * Get ticket statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_tickets' => SupportTicket::count(),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
            'in_progress_tickets' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved_tickets' => SupportTicket::where('status', 'resolved')->count(),
            'closed_tickets' => SupportTicket::where('status', 'closed')->count(),
            'high_priority_tickets' => SupportTicket::where('priority', 'high')->count(),
            'urgent_tickets' => SupportTicket::where('priority', 'urgent')->count(),
            'avg_resolution_time' => SupportTicket::whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
                ->value('avg_hours') ?? 0
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Support ticket statistics retrieved successfully'
        ]);
    }
}
