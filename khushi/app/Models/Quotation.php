<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quote_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'items',
        'subtotal',
        'tax_amount',
        'total_amount',
        'valid_until',
        'status',
        'terms',
        'notes'
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'valid_until' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeValid($query)
    {
        return $query->where('valid_until', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now());
    }

    // Accessors
    public function getIsValidAttribute()
    {
        return $this->valid_until >= now();
    }

    public function getIsExpiredAttribute()
    {
        return $this->valid_until < now();
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getTotalItemsAttribute()
    {
        return count($this->items ?? []);
    }
}
