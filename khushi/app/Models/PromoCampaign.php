<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromoCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'discount_value',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'applicable_products',
        'applicable_categories',
        'excluded_products',
        'excluded_categories',
        'is_stackable',
        'status'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'excluded_products' => 'array',
        'excluded_categories' => 'array',
        'is_stackable' => 'boolean',
        'status' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeAvailable($query)
    {
        return $query->active()
                    ->where(function($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                    });
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }

    public function getIsAvailableAttribute()
    {
        return $this->status &&
               ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    // Methods
    public function isApplicableToProduct($productId)
    {
        if (!empty($this->excluded_products) && in_array($productId, $this->excluded_products)) {
            return false;
        }
        
        if (!empty($this->applicable_products)) {
            return in_array($productId, $this->applicable_products);
        }
        
        return true;
    }
}
