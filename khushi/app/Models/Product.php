<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock',
        'sku',
        'image',
        'is_featured',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'status' => 'boolean',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    // Virtual images relationship - returns collection with main image or default
    public function getImagesAttribute()
    {
        $imageUrl = $this->image_url;
        
        return collect([
            (object) [
                'id' => 1,
                'url' => $imageUrl,
                'alt_text' => $this->name ?? 'Product Image',
                'is_primary' => true
            ]
        ]);
    }

    // Primary image accessor with fallback
    public function getPrimaryImageAttribute()
    {
        return $this->image_url;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return 'https://via.placeholder.com/500x500/e5e7eb/9ca3af?text=No+Image';
        }

        // If it's already an absolute URL, return as-is
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        // For local storage paths, use asset() with storage/ prefix
        return asset('storage/' . $this->image);
    }
    public function getFinalPriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->discount_price && $this->price > 0) {
            return round((($this->price - $this->discount_price) / $this->price) * 100);
        }
        return 0;
    }

    public function getIsOnSaleAttribute()
    {
        return !is_null($this->discount_price) && $this->discount_price < $this->price;
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    public function getIsInStockAttribute()
    {
        return $this->stock > 0;
    }

    // Backward-compatible accessor for views using stock_quantity
    public function getStockQuantityAttribute()
    {
        return (int) ($this->stock ?? 0);
    }
}
