<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'event_name',
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'url',
        'referrer',
        'data',
        'created_at'
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePageViews($query)
    {
        return $query->where('event_type', 'page_view');
    }

    public function scopeProductViews($query)
    {
        return $query->where('event_type', 'product_view');
    }

    public function scopeAddToCart($query)
    {
        return $query->where('event_type', 'add_to_cart');
    }

    public function scopePurchases($query)
    {
        return $query->where('event_type', 'purchase');
    }

    public function scopeSearches($query)
    {
        return $query->where('event_type', 'search');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', now()->year);
    }
}
