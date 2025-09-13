<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'countries',
        'states',
        'cities',
        'zip_codes',
        'shipping_rates',
        'delivery_days',
        'status'
    ];

    protected $casts = [
        'countries' => 'array',
        'states' => 'array',
        'cities' => 'array',
        'zip_codes' => 'array',
        'shipping_rates' => 'array',
        'delivery_days' => 'integer',
        'status' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status;
    }

    // Methods
    public function getShippingRate($weight = 0)
    {
        $rates = $this->shipping_rates ?? [];
        
        // Find appropriate rate based on weight
        foreach ($rates as $rate) {
            if ($weight <= ($rate['max_weight'] ?? PHP_INT_MAX)) {
                return $rate['rate'] ?? 0;
            }
        }
        
        return 0;
    }

    public function coversLocation($country, $state = null, $city = null, $zipCode = null)
    {
        if (!in_array($country, $this->countries ?? [])) {
            return false;
        }
        
        if ($state && !empty($this->states) && !in_array($state, $this->states)) {
            return false;
        }
        
        if ($city && !empty($this->cities) && !in_array($city, $this->cities)) {
            return false;
        }
        
        if ($zipCode && !empty($this->zip_codes) && !in_array($zipCode, $this->zip_codes)) {
            return false;
        }
        
        return true;
    }
}
