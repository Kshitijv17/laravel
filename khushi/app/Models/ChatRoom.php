<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'agent_id',
        'status',
        'priority',
        'subject',
        'department',
        'last_message_at',
        'rating',
        'feedback',
        'closed_at',
        'closed_by',
        'metadata'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'closed_at' => 'datetime',
        'metadata' => 'array'
    ];

    // Status constants
    const STATUS_WAITING = 'waiting';
    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';
    const STATUS_RESOLVED = 'resolved';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(Admin::class, 'agent_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class)->latest();
    }

    public function closedBy()
    {
        return $this->belongsTo(Admin::class, 'closed_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', self::STATUS_WAITING);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    // Methods
    public function assignAgent($agentId)
    {
        $this->update([
            'agent_id' => $agentId,
            'status' => self::STATUS_ACTIVE
        ]);
    }

    public function closeChat($closedBy = null, $feedback = null, $rating = null)
    {
        $this->update([
            'status' => self::STATUS_CLOSED,
            'closed_at' => now(),
            'closed_by' => $closedBy,
            'feedback' => $feedback,
            'rating' => $rating
        ]);
    }

    public function getWaitingTimeAttribute()
    {
        if ($this->status === self::STATUS_WAITING) {
            return $this->created_at->diffInMinutes(now());
        }
        return null;
    }

    public function getResponseTimeAttribute()
    {
        $firstAgentMessage = $this->messages()
            ->where('sender_type', 'admin')
            ->first();

        if ($firstAgentMessage) {
            return $this->created_at->diffInMinutes($firstAgentMessage->created_at);
        }
        return null;
    }
}
