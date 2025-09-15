<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
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

    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    public function getActiveProductsCountAttribute()
    {
        return $this->products()->active()->count();
    }
}
