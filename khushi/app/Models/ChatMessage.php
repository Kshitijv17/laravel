<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room_id',
        'sender_id',
        'sender_type',
        'message',
        'message_type',
        'attachment_url',
        'attachment_type',
        'is_read',
        'read_at',
        'metadata'
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'is_read' => 'boolean',
        'metadata' => 'array'
    ];

    // Message types
    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';
    const TYPE_SYSTEM = 'system';
    const TYPE_EMOJI = 'emoji';

    // Sender types
    const SENDER_USER = 'user';
    const SENDER_ADMIN = 'admin';
    const SENDER_SYSTEM = 'system';

    // Relationships
    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function sender()
    {
        if ($this->sender_type === self::SENDER_USER) {
            return $this->belongsTo(User::class, 'sender_id');
        } elseif ($this->sender_type === self::SENDER_ADMIN) {
            return $this->belongsTo(Admin::class, 'sender_id');
        }
        return null;
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('message_type', $type);
    }

    public function scopeBySender($query, $senderType)
    {
        return $query->where('sender_type', $senderType);
    }

    // Methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function getSenderNameAttribute()
    {
        if ($this->sender_type === self::SENDER_USER && $this->sender) {
            return $this->sender->name;
        } elseif ($this->sender_type === self::SENDER_ADMIN && $this->sender) {
            return $this->sender->name;
        } elseif ($this->sender_type === self::SENDER_SYSTEM) {
            return 'System';
        }
        return 'Unknown';
    }

    public function getFormattedMessageAttribute()
    {
        if ($this->message_type === self::TYPE_SYSTEM) {
            return $this->message;
        }
        
        // Format URLs as clickable links
        $message = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" class="text-blue-500 underline">$1</a>',
            $this->message
        );
        
        return $message;
    }
}
