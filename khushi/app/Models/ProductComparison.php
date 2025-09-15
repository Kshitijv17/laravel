<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductComparison extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_ids',
        'comparison_data',
        'created_at'
    ];

    protected $casts = [
        'product_ids' => 'array',
        'comparison_data' => 'array',
        'created_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return Product::whereIn('id', $this->product_ids ?? [])->get();
    }

    public function getProductsAttribute()
    {
        return Product::whereIn('id', $this->product_ids ?? [])->get();
    }
}
