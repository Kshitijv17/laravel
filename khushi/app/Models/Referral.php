<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'referral_code',
        'status',
        'commission_earned',
        'conversion_date'
    ];

    protected $casts = [
        'commission_earned' => 'decimal:2',
        'conversion_date' => 'datetime',
    ];

    // Relationships
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function rewards()
    {
        return $this->hasMany(ReferralReward::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeConverted($query)
    {
        return $query->whereNotNull('conversion_date');
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    public function getIsConvertedAttribute()
    {
        return !is_null($this->conversion_date);
    }

    public function getTotalRewardsAttribute()
    {
        return $this->rewards()->sum('reward_value');
    }
}
