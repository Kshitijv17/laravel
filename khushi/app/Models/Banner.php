<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Banner extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'image', 'link',
        'description', 'position', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return 'https://via.placeholder.com/1200x400/e5e7eb/9ca3af?text=No+Image';
        }

        // If it's already an absolute URL, return as-is
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        // For local storage paths, use asset() with storage/ prefix
        return asset('storage/' . $this->image);
    }
}

