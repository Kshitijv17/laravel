<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourierTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tracking_number',
        'courier_company',
        'courier_service',
        'status',
        'current_location',
        'shipped_at',
        'delivered_at',
        'tracking_history',
        'delivery_notes',
        'recipient_name',
        'recipient_phone'
    ];

    protected $casts = [
        'tracking_history' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }

    // Accessors
    public function getIsShippedAttribute()
    {
        return $this->status === 'shipped';
    }

    public function getIsDeliveredAttribute()
    {
        return $this->status === 'delivered';
    }

    public function getIsInTransitAttribute()
    {
        return $this->status === 'in_transit';
    }

    // Methods
    public function addTrackingUpdate($status, $location, $notes = null)
    {
        $history = $this->tracking_history ?? [];
        $history[] = [
            'status' => $status,
            'location' => $location,
            'notes' => $notes,
            'timestamp' => now()->toISOString()
        ];
        
        $this->update([
            'tracking_history' => $history,
            'status' => $status,
            'current_location' => $location
        ]);
    }
}
