<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'rate',
        'type',
        'country',
        'state',
        'city',
        'zip_code',
        'minimum_amount',
        'maximum_amount',
        'is_compound',
        'priority',
        'status'
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'minimum_amount' => 'decimal:2',
        'maximum_amount' => 'decimal:2',
        'is_compound' => 'boolean',
        'priority' => 'integer',
        'status' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByLocation($query, $country = null, $state = null, $city = null)
    {
        if ($country) {
            $query->where('country', $country);
        }
        if ($state) {
            $query->where('state', $state);
        }
        if ($city) {
            $query->where('city', $city);
        }
        return $query;
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status;
    }

    public function getFormattedRateAttribute()
    {
        return number_format($this->rate, 2) . '%';
    }

    // Methods
    public function calculateTax($amount)
    {
        if ($amount < $this->minimum_amount || ($this->maximum_amount && $amount > $this->maximum_amount)) {
            return 0;
        }
        
        return ($amount * $this->rate) / 100;
    }
}
