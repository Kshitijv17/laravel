<?php

namespace App\Services;

use App\Models\SupportTicket;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Admin;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ChatService
{
    public function createTicket($userId, $subject, $message, $priority = 'medium')
    {
        $ticket = SupportTicket::create([
            'user_id' => $userId,
            'subject' => $subject,
            'message' => $message,
            'priority' => $priority,
            'status' => 'open'
        ]);

        // Create initial message
        $this->sendMessage($ticket->id, $userId, $message, 'user');

        // Auto-assign to available admin
        $this->autoAssignTicket($ticket);

        return $ticket;
    }

    public function sendMessage($ticketId, $senderId, $message, $senderType = 'user')
    {
        $chatMessage = ChatMessage::create([
            'support_ticket_id' => $ticketId,
            'sender_id' => $senderId,
            'sender_type' => $senderType,
            'message' => $message,
            'is_read' => false
        ]);

        // Update ticket status
        $ticket = SupportTicket::find($ticketId);
        if ($ticket && $senderType === 'user' && $ticket->status === 'resolved') {
            $ticket->update(['status' => 'open']);
        }

        // Broadcast message
        broadcast(new MessageSent($chatMessage))->toOthers();

        // Mark user as typing stopped
        $this->stopTyping($ticketId, $senderId, $senderType);

        return $chatMessage;
    }

    public function getMessages($ticketId, $limit = 50)
    {
        return ChatMessage::with(['sender'])
            ->where('support_ticket_id', $ticketId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    public function markAsRead($ticketId, $userId, $userType = 'user')
    {
        ChatMessage::where('support_ticket_id', $ticketId)
            ->where('sender_type', '!=', $userType)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function getUnreadCount($userId, $userType = 'user')
    {
        if ($userType === 'user') {
            return ChatMessage::whereHas('ticket', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->count();
        } else {
            return ChatMessage::whereHas('ticket', function($query) use ($userId) {
                $query->where('assigned_to', $userId);
            })
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->count();
        }
    }

    public function assignTicket($ticketId, $adminId)
    {
        $ticket = SupportTicket::find($ticketId);
        if ($ticket) {
            $ticket->update(['assigned_to' => $adminId]);
            
            // Send system message
            $this->sendSystemMessage($ticketId, "Ticket assigned to admin.");
        }
        
        return $ticket;
    }

    public function resolveTicket($ticketId, $adminId)
    {
        $ticket = SupportTicket::find($ticketId);
        if ($ticket) {
            $ticket->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                'assigned_to' => $adminId
            ]);
            
            $this->sendSystemMessage($ticketId, "Ticket has been resolved.");
        }
        
        return $ticket;
    }

    public function closeTicket($ticketId)
    {
        $ticket = SupportTicket::find($ticketId);
        if ($ticket) {
            $ticket->update([
                'status' => 'closed',
                'closed_at' => now()
            ]);
            
            $this->sendSystemMessage($ticketId, "Ticket has been closed.");
        }
        
        return $ticket;
    }

    public function startTyping($ticketId, $userId, $userType = 'user')
    {
        $key = "typing:{$ticketId}:{$userType}:{$userId}";
        Cache::put($key, true, 30); // 30 seconds
        
        broadcast(new \App\Events\UserTyping($ticketId, $userId, $userType));
    }

    public function stopTyping($ticketId, $userId, $userType = 'user')
    {
        $key = "typing:{$ticketId}:{$userType}:{$userId}";
        Cache::forget($key);
    }

    public function getTypingUsers($ticketId)
    {
        $typingUsers = [];
        
        // Check for typing users and admins
        $cacheKeys = Cache::getRedis()->keys("typing:{$ticketId}:*");
        
        foreach ($cacheKeys as $key) {
            if (Cache::get($key)) {
                $parts = explode(':', $key);
                $userType = $parts[2];
                $userId = $parts[3];
                
                $typingUsers[] = [
                    'user_id' => $userId,
                    'user_type' => $userType
                ];
            }
        }
        
        return $typingUsers;
    }

    public function getUserTickets($userId, $status = null)
    {
        $query = SupportTicket::where('user_id', $userId)
            ->with(['assignedTo'])
            ->withCount(['messages as unread_count' => function($q) {
                $q->where('sender_type', 'admin')
                  ->where('is_read', false);
            }])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function getAdminTickets($adminId = null, $status = null)
    {
        $query = SupportTicket::with(['user', 'assignedTo'])
            ->withCount(['messages as unread_count' => function($q) {
                $q->where('sender_type', 'user')
                  ->where('is_read', false);
            }])
            ->orderBy('created_at', 'desc');

        if ($adminId) {
            $query->where('assigned_to', $adminId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function searchTickets($query, $userId = null, $userType = 'user')
    {
        $ticketQuery = SupportTicket::with(['user', 'assignedTo']);

        if ($userType === 'user' && $userId) {
            $ticketQuery->where('user_id', $userId);
        }

        $ticketQuery->where(function($q) use ($query) {
            $q->where('subject', 'like', "%{$query}%")
              ->orWhere('message', 'like', "%{$query}%")
              ->orWhereHas('messages', function($mq) use ($query) {
                  $mq->where('message', 'like', "%{$query}%");
              });
        });

        return $ticketQuery->orderBy('created_at', 'desc')->get();
    }

    public function getTicketStats($adminId = null)
    {
        $query = SupportTicket::query();
        
        if ($adminId) {
            $query->where('assigned_to', $adminId);
        }

        return [
            'total' => $query->count(),
            'open' => $query->where('status', 'open')->count(),
            'resolved' => $query->where('status', 'resolved')->count(),
            'closed' => $query->where('status', 'closed')->count(),
            'high_priority' => $query->where('priority', 'high')->count(),
            'unassigned' => $query->whereNull('assigned_to')->count(),
            'response_time' => $this->getAverageResponseTime($adminId)
        ];
    }

    public function autoAssignTicket($ticket)
    {
        // Find admin with least assigned tickets
        $admin = Admin::withCount(['assignedTickets' => function($query) {
            $query->whereIn('status', ['open', 'resolved']);
        }])
        ->where('is_active', true)
        ->orderBy('assigned_tickets_count')
        ->first();

        if ($admin) {
            $this->assignTicket($ticket->id, $admin->id);
        }
    }

    private function sendSystemMessage($ticketId, $message)
    {
        ChatMessage::create([
            'support_ticket_id' => $ticketId,
            'sender_id' => null,
            'sender_type' => 'system',
            'message' => $message,
            'is_read' => false
        ]);
    }

    private function getAverageResponseTime($adminId = null)
    {
        // Calculate average response time in minutes
        $query = ChatMessage::whereHas('ticket', function($q) use ($adminId) {
            if ($adminId) {
                $q->where('assigned_to', $adminId);
            }
        })
        ->where('sender_type', 'admin')
        ->whereExists(function($q) {
            $q->select(\DB::raw(1))
              ->from('chat_messages as cm2')
              ->whereColumn('cm2.support_ticket_id', 'chat_messages.support_ticket_id')
              ->where('cm2.sender_type', 'user')
              ->whereRaw('cm2.created_at < chat_messages.created_at')
              ->orderBy('cm2.created_at', 'desc')
              ->limit(1);
        });

        $avgMinutes = $query->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, 
            (SELECT MAX(created_at) FROM chat_messages cm2 
             WHERE cm2.support_ticket_id = chat_messages.support_ticket_id 
             AND cm2.sender_type = "user" 
             AND cm2.created_at < chat_messages.created_at), 
            chat_messages.created_at)) as avg_response_time')
        ->first()
        ->avg_response_time ?? 0;

        return round($avgMinutes, 2);
    }

    public function exportTickets($filters = [])
    {
        $query = SupportTicket::with(['user', 'assignedTo', 'messages']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
