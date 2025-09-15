<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $chatRooms = $user->chatRooms()->with(['lastMessage', 'agent'])->latest()->get();
        
        return view('web.chat.index', compact('chatRooms'));
    }

    public function show($roomId)
    {
        $chatRoom = ChatRoom::with(['messages.sender', 'agent', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($roomId);

        // Mark messages as read
        $chatRoom->messages()
            ->where('sender_type', '!=', 'user')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('web.chat.show', compact('chatRoom'));
    }

    public function create()
    {
        return view('web.chat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'department' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        $chatRoom = ChatRoom::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'department' => $request->department,
            'priority' => $request->priority,
            'status' => ChatRoom::STATUS_WAITING,
            'last_message_at' => now()
        ]);

        $message = ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'sender_id' => Auth::id(),
            'sender_type' => ChatMessage::SENDER_USER,
            'message' => $request->message,
            'message_type' => ChatMessage::TYPE_TEXT
        ]);

        // Broadcast to agents
        broadcast(new MessageSent($message))->toOthers();

        return redirect()->route('chat.show', $chatRoom->id)
            ->with('success', 'Chat started successfully! An agent will be with you shortly.');
    }

    public function sendMessage(Request $request, $roomId)
    {
        $request->validate([
            'message' => 'required_without:attachment|string',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx'
        ]);

        $chatRoom = ChatRoom::where('user_id', Auth::id())->findOrFail($roomId);

        $messageData = [
            'chat_room_id' => $chatRoom->id,
            'sender_id' => Auth::id(),
            'sender_type' => ChatMessage::SENDER_USER,
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

        // Update chat room last message time
        $chatRoom->update(['last_message_at' => now()]);

        // Broadcast to agents
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message->load('sender')
        ]);
    }

    public function closeChat(Request $request, $roomId)
    {
        $chatRoom = ChatRoom::where('user_id', Auth::id())->findOrFail($roomId);

        $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000'
        ]);

        $chatRoom->closeChat(null, $request->feedback, $request->rating);

        // Send system message
        ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'sender_id' => 0,
            'sender_type' => ChatMessage::SENDER_SYSTEM,
            'message' => 'Chat closed by user',
            'message_type' => ChatMessage::TYPE_SYSTEM
        ]);

        return response()->json(['success' => true]);
    }

    public function widget()
    {
        return view('web.chat.widget');
    }

    public function quickStart(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'email' => 'required_without:user_id|email',
            'name' => 'required_without:user_id|string|max:255'
        ]);

        // For guest users, create a temporary chat
        $userId = Auth::id();
        if (!$userId) {
            // Create guest user or handle differently
            $guestData = [
                'email' => $request->email,
                'name' => $request->name
            ];
        }

        $chatRoom = ChatRoom::create([
            'user_id' => $userId,
            'subject' => 'Quick Support',
            'department' => 'general',
            'priority' => ChatRoom::PRIORITY_MEDIUM,
            'status' => ChatRoom::STATUS_WAITING,
            'last_message_at' => now(),
            'metadata' => !$userId ? $guestData : null
        ]);

        $message = ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'sender_id' => $userId ?? 0,
            'sender_type' => $userId ? ChatMessage::SENDER_USER : ChatMessage::SENDER_SYSTEM,
            'message' => $request->message,
            'message_type' => ChatMessage::TYPE_TEXT
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'chat_room_id' => $chatRoom->id,
            'redirect_url' => $userId ? route('chat.show', $chatRoom->id) : null
        ]);
    }

    public function getMessages($roomId)
    {
        $chatRoom = ChatRoom::where('user_id', Auth::id())->findOrFail($roomId);
        
        $messages = $chatRoom->messages()
            ->with('sender')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json($messages);
    }

    public function typing(Request $request, $roomId)
    {
        $chatRoom = ChatRoom::where('user_id', Auth::id())->findOrFail($roomId);
        
        broadcast(new \App\Events\UserTyping([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'chat_room_id' => $chatRoom->id,
            'is_typing' => $request->boolean('is_typing')
        ]))->toOthers();

        return response()->json(['success' => true]);
    }

    public function sendChatMessage(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:chat_rooms,id',
            'message' => 'required|string|max:1000'
        ]);

        $chatRoom = ChatRoom::findOrFail($request->ticket_id);
        
        $message = ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'sender_id' => Auth::id() ?? 0,
            'sender_type' => Auth::check() ? 'user' : 'guest',
            'message' => $request->message,
            'message_type' => 'text'
        ]);

        $chatRoom->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'ticket_id' => 'required|exists:chat_rooms,id'
        ]);

        $file = $request->file('file');
        $path = $file->store('chat-files', 'public');

        return response()->json([
            'success' => true,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName()
        ]);
    }

    public function checkNewMessages($ticketId)
    {
        $messages = ChatMessage::where('chat_room_id', $ticketId)
            ->where('created_at', '>', now()->subMinutes(1))
            ->where('sender_type', '!=', 'user')
            ->get();

        return response()->json([
            'success' => true,
            'new_messages' => $messages,
            'agent_typing' => false
        ]);
    }
}
