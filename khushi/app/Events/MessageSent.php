<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(ChatMessage $message)
    {
        $this->message = $message->load('sender', 'chatRoom');
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('chat-room.' . $this->message->chat_room_id),
            new PrivateChannel('admin-chat-notifications')
        ];
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'chat_room_id' => $this->message->chat_room_id,
            'sender_id' => $this->message->sender_id,
            'sender_type' => $this->message->sender_type,
            'sender_name' => $this->message->sender_name,
            'message' => $this->message->message,
            'message_type' => $this->message->message_type,
            'attachment_url' => $this->message->attachment_url,
            'attachment_type' => $this->message->attachment_type,
            'created_at' => $this->message->created_at->toISOString(),
            'formatted_time' => $this->message->created_at->format('H:i')
        ];
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}
