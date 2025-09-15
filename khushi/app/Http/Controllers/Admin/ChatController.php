<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\Admin;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'waiting_chats' => ChatRoom::waiting()->count(),
            'active_chats' => ChatRoom::active()->count(),
            'my_active_chats' => ChatRoom::active()->where('agent_id', Auth::guard('admin')->id())->count(),
            'closed_today' => ChatRoom::closed()->whereDate('closed_at', today())->count(),
            'avg_response_time' => $this->getAverageResponseTime(),
            'satisfaction_rating' => $this->getAverageSatisfactionRating()
        ];

        $waitingChats = ChatRoom::waiting()
            ->with(['user', 'lastMessage'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at')
            ->take(10)
            ->get();

        $myActiveChats = ChatRoom::active()
            ->where('agent_id', Auth::guard('admin')->id())
            ->with(['user', 'lastMessage'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('admin.chat.dashboard', compact('stats', 'waitingChats', 'myActiveChats'));
    }

    public function index(Request $request)
    {
        $query = ChatRoom::with(['user', 'agent', 'lastMessage']);

        // Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        if ($request->department) {
            $query->where('department', $request->department);
        }

        if ($request->agent_id) {
            $query->where('agent_id', $request->agent_id);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                               ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $chatRooms = $query->orderBy('created_at', 'desc')->paginate(20);
        $agents = Admin::where('status', 'active')->get();

        return view('admin.chat.index', compact('chatRooms', 'agents'));
    }

    public function show($roomId)
    {
        $chatRoom = ChatRoom::with(['messages.sender', 'user', 'agent'])
            ->findOrFail($roomId);

        // Mark messages as read by agent
        $chatRoom->messages()
            ->where('sender_type', '!=', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('admin.chat.show', compact('chatRoom'));
    }

    public function assign(Request $request, $roomId)
    {
        $request->validate([
            'agent_id' => 'required|exists:admins,id'
        ]);

        $chatRoom = ChatRoom::findOrFail($roomId);
        $chatRoom->assignAgent($request->agent_id);

        // Send system message
        $agent = Admin::find($request->agent_id);
        ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'sender_id' => Auth::guard('admin')->id(),
            'sender_type' => ChatMessage::SENDER_SYSTEM,
            'message' => "Chat assigned to {$agent->name}",
            'message_type' => ChatMessage::TYPE_SYSTEM
        ]);

        return response()->json(['success' => true]);
    }

    public function takeChat($roomId)
    {
        $chatRoom = ChatRoom::findOrFail($roomId);
        
        if ($chatRoom->status !== ChatRoom::STATUS_WAITING) {
            return response()->json(['error' => 'Chat is not available'], 400);
        }

        $chatRoom->assignAgent(Auth::guard('admin')->id());

        // Send system message
        ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'sender_id' => Auth::guard('admin')->id(),
            'sender_type' => ChatMessage::SENDER_SYSTEM,
            'message' => Auth::guard('admin')->user()->name . " joined the chat",
            'message_type' => ChatMessage::TYPE_SYSTEM
        ]);

        return response()->json(['success' => true]);
    }

    public function sendMessage(Request $request, $roomId)
    {
        $request->validate([
            'message' => 'required_without:attachment|string',
            'attachment' => 'nullable|file|max:10240'
        ]);

        $chatRoom = ChatRoom::findOrFail($roomId);

        // Ensure agent is assigned to this chat
        if ($chatRoom->agent_id !== Auth::guard('admin')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messageData = [
            'chat_room_id' => $chatRoom->id,
            'sender_id' => Auth::guard('admin')->id(),
            'sender_type' => ChatMessage::SENDER_ADMIN,
            'message' => $request->message ?? '',
            'message_type' => ChatMessage::TYPE_TEXT
        ];

        // Handle file attachment
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('chat-attachments', 'public');
            
            $messageData['attachment_url'] = $path;
            $messageData['attachment_type'] = $file->getClientOriginalExtension();
            $messageData['message_type'] = in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif']) 
                ? ChatMessage::TYPE_IMAGE 
                : ChatMessage::TYPE_FILE;
            
            if (empty($messageData['message'])) {
                $messageData['message'] = 'Sent an attachment';
            }
        }

        $message = ChatMessage::create($messageData);

        // Update chat room
        $chatRoom->update(['last_message_at' => now()]);

        // Broadcast to user
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message->load('sender')
        ]);
    }

    public function closeChat(Request $request, $roomId)
    {
        $chatRoom = ChatRoom::findOrFail($roomId);

        $chatRoom->closeChat(Auth::guard('admin')->id());

        // Send system message
        ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'sender_id' => Auth::guard('admin')->id(),
            'sender_type' => ChatMessage::SENDER_SYSTEM,
            'message' => 'Chat closed by ' . Auth::guard('admin')->user()->name,
            'message_type' => ChatMessage::TYPE_SYSTEM
        ]);

        return response()->json(['success' => true]);
    }

    public function transferChat(Request $request, $roomId)
    {
        $request->validate([
            'agent_id' => 'required|exists:admins,id',
            'reason' => 'nullable|string|max:255'
        ]);

        $chatRoom = ChatRoom::findOrFail($roomId);
        $newAgent = Admin::find($request->agent_id);
        
        $chatRoom->update(['agent_id' => $request->agent_id]);

        // Send system message
        $reason = $request->reason ? " (Reason: {$request->reason})" : '';
        ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'sender_id' => Auth::guard('admin')->id(),
            'sender_type' => ChatMessage::SENDER_SYSTEM,
            'message' => "Chat transferred to {$newAgent->name}{$reason}",
            'message_type' => ChatMessage::TYPE_SYSTEM
        ]);

        return response()->json(['success' => true]);
    }

    public function updatePriority(Request $request, $roomId)
    {
        $request->validate([
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        $chatRoom = ChatRoom::findOrFail($roomId);
        $chatRoom->update(['priority' => $request->priority]);

        return response()->json(['success' => true]);
    }

    public function getStats()
    {
        $stats = [
            'waiting' => ChatRoom::waiting()->count(),
            'active' => ChatRoom::active()->count(),
            'closed_today' => ChatRoom::closed()->whereDate('closed_at', today())->count(),
            'my_active' => ChatRoom::active()->where('agent_id', Auth::guard('admin')->id())->count()
        ];

        return response()->json($stats);
    }

    public function typing(Request $request, $roomId)
    {
        broadcast(new \App\Events\AgentTyping([
            'agent_id' => Auth::guard('admin')->id(),
            'agent_name' => Auth::guard('admin')->user()->name,
            'chat_room_id' => $roomId,
            'is_typing' => $request->boolean('is_typing')
        ]))->toOthers();

        return response()->json(['success' => true]);
    }

    private function getAverageResponseTime()
    {
        return ChatRoom::whereNotNull('agent_id')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->get()
            ->avg('response_time') ?? 0;
    }

    private function getAverageSatisfactionRating()
    {
        return ChatRoom::whereNotNull('rating')
            ->whereDate('closed_at', '>=', now()->subDays(30))
            ->avg('rating') ?? 0;
    }
}
