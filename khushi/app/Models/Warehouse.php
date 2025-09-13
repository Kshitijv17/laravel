<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'email',
        'manager',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, Inventory::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLocation($query, $city = null, $state = null)
    {
        if ($city) {
            $query->where('city', $city);
        }
        if ($state) {
            $query->where('state', $state);
        }
        return $query;
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->city . ', ' . $this->state . ' ' . $this->postal_code;
    }

    public function getTotalInventoryValueAttribute()
    {
        return $this->inventories()->sum('total_value');
    }

    public function getTotalStockAttribute()
    {
        return $this->inventories()->sum('available_quantity');
    }
}
