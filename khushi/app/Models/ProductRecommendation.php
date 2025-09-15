<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'recommended_product_id',
        'recommendation_type',
        'score',
        'reason',
        'metadata'
    ];

    protected $casts = [
        'score' => 'float',
        'metadata' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function recommendedProduct()
    {
        return $this->belongsTo(Product::class, 'recommended_product_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('recommendation_type', $type);
    }

    public function scopeHighScore($query, $minScore = 0.7)
    {
        return $query->where('score', '>=', $minScore);
    }
}
