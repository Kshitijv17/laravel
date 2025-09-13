<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_type',
        'variant_value',
        'sku',
        'price_adjustment',
        'stock',
        'image',
        'weight',
        'attributes',
        'status'
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:2',
        'stock' => 'integer',
        'weight' => 'decimal:2',
        'attributes' => 'array',
        'status' => 'boolean',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('variant_type', $type);
    }

    // Accessors
    public function getFinalPriceAttribute()
    {
        return $this->product->price + $this->price_adjustment;
    }

    public function getIsInStockAttribute()
    {
        return $this->stock > 0;
    }

    public function getVariantNameAttribute()
    {
        return $this->variant_type . ': ' . $this->variant_value;
    }
}
