<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title',
        'description',
        'features',
        'specifications',
        'price',
        'selling_price',
        'discount_tag',
        'discount_color',
        'quantity',
        'stock_status',
        'is_active',
        'is_featured',
        'category_id',
        'shop_id',
        'image'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, OrderItem::class);
    }

    /**
     * Scope products by shop
     */
    public function scopeByShop($query, $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    /**
     * Scope products by admin (through shop)
     */
    public function scopeByAdmin($query, $adminId)
    {
        return $query->whereHas('shop', function($q) use ($adminId) {
            $q->where('admin_id', $adminId);
        });
    }
}
