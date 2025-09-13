<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReferralReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_id',
        'reward_type',
        'reward_value',
        'trigger_event',
        'minimum_purchase',
        'is_one_time',
        'status'
    ];

    protected $casts = [
        'reward_value' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'is_one_time' => 'boolean',
        'status' => 'boolean',
    ];

    // Relationships
    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('reward_type', $type);
    }

    public function scopeOneTime($query)
    {
        return $query->where('is_one_time', true);
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status;
    }

    public function getFormattedRewardAttribute()
    {
        if ($this->reward_type === 'percentage') {
            return $this->reward_value . '%';
        }
        return '$' . number_format($this->reward_value, 2);
    }
}
