<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'order_item_id',
        'return_number',
        'reason',
        'quantity',
        'refund_amount',
        'status',
        'return_type',
        'images',
        'admin_notes',
        'approved_at',
        'processed_at'
    ];

    protected $casts = [
        'images' => 'array',
        'refund_amount' => 'decimal:2',
        'quantity' => 'integer',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    // Accessors
    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    public function getIsProcessedAttribute()
    {
        return $this->status === 'processed';
    }

    public function getTotalRefundAttribute()
    {
        return $this->refund_amount * $this->quantity;
    }
}
