<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'title',
        'image',
        'active',
        'show_on_home'
    ];

    protected $casts = [
        'active' => 'string',
        'show_on_home' => 'string',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
