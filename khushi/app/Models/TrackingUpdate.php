<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingUpdate extends Model
{
    protected $fillable = [
        'courier_tracking_id',
        'status',
        'location',
        'description',
        'occurred_at'
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function courierTracking()
    {
        return $this->belongsTo(CourierTracking::class);
    }
}
