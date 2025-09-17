<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'status',
        'notes',
        'ordered_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'ordered_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderAddresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    // Alias for convenience in views/controllers
    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    // Primary shipping address alias used in admin views/controllers
    public function address()
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'shipping');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function courierTracking()
    {
        return $this->hasOne(CourierTracking::class);
    }

    // Alias to support eager load: tracking.updates
    public function tracking()
    {
        return $this->hasOne(CourierTracking::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }

    public function getItemsCountAttribute()
    {
        return $this->items()->count();
    }

    public function getTotalItemsQuantityAttribute()
    {
        return $this->items()->sum('quantity');
    }

    public function getCanBeCancelledAttribute()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function getCanBeReturnedAttribute()
    {
        return $this->status === 'delivered';
    }
}
