<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'banner',
        'address',
        'phone',
        'email',
        'website',
        'is_active',
        'admin_id',
        'commission_rate',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'commission_rate' => 'decimal:2',
        'settings' => 'array',
    ];

    /**
     * Get the admin (shopkeeper) who owns this shop
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get all products belonging to this shop
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all orders for this shop
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get shop statistics
     */
    public function getStatsAttribute()
    {
        return [
            'total_products' => $this->products()->count(),
            'active_products' => $this->products()->where('is_active', true)->count(),
            'total_orders' => $this->orders()->count(),
            'pending_orders' => $this->orders()->where('status', 'pending')->count(),
            'total_revenue' => $this->orders()->where('payment_status', 'paid')->sum('total_amount'),
        ];
    }

    /**
     * Generate unique shop slug
     */
    public static function generateSlug($name)
    {
        $slug = \Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shop) {
            if (!$shop->slug) {
                $shop->slug = self::generateSlug($shop->name);
            }
        });
    }
}
