<?php

namespace App\Http\Controllers\Api;

use App\Models\SupportTicketMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class SupportTicketMessageController extends Controller
{
    /**
     * Display a listing of support ticket messages
     */
    public function index(Request $request): JsonResponse
    {
        $query = SupportTicketMessage::with(['supportTicket', 'user', 'admin']);

        if ($request->has('support_ticket_id')) {
            $query->where('support_ticket_id', $request->support_ticket_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        $messages = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $messages,
            'message' => 'Support ticket messages retrieved successfully'
        ]);
    }

    /**
     * Store a newly created support ticket message
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'support_ticket_id' => 'required|exists:support_tickets,id',
            'user_id' => 'nullable|exists:users,id',
            'admin_id' => 'nullable|exists:admins,id',
            'message' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'string|max:255'
        ]);

        // Ensure either user_id or admin_id is provided
        if (!isset($validated['user_id']) && !isset($validated['admin_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Either user_id or admin_id must be provided'
            ], 400);
        }

        $message = SupportTicketMessage::create($validated);

        // Update ticket's last activity
        $message->supportTicket->touch();

        return response()->json([
            'success' => true,
            'data' => $message->load(['supportTicket', 'user', 'admin']),
            'message' => 'Support ticket message created successfully'
        ], 201);
    }

    /**
     * Display the specified support ticket message
     */
    public function show(SupportTicketMessage $supportTicketMessage): JsonResponse
    {
        $supportTicketMessage->load(['supportTicket', 'user', 'admin']);

        return response()->json([
            'success' => true,
            'data' => $supportTicketMessage,
            'message' => 'Support ticket message retrieved successfully'
        ]);
    }

    /**
     * Update the specified support ticket message
     */
    public function update(Request $request, SupportTicketMessage $supportTicketMessage): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'sometimes|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'string|max:255'
        ]);

        $supportTicketMessage->update($validated);

        return response()->json([
            'success' => true,
            'data' => $supportTicketMessage->load(['supportTicket', 'user', 'admin']),
            'message' => 'Support ticket message updated successfully'
        ]);
    }

    /**
     * Remove the specified support ticket message
     */
    public function destroy(SupportTicketMessage $supportTicketMessage): JsonResponse
    {
        $supportTicketMessage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Support ticket message deleted successfully'
        ]);
    }

    /**
     * Get messages by support ticket
     */
    public function ticketMessages(Request $request, SupportTicket $supportTicket): JsonResponse
    {
        $messages = $supportTicket->messages()
                                 ->with(['user', 'admin'])
                                 ->orderBy('created_at', 'asc')
                                 ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'ticket' => $supportTicket,
                'messages' => $messages,
                'total_messages' => $messages->count()
            ],
            'message' => 'Support ticket messages retrieved successfully'
        ]);
    }
}
