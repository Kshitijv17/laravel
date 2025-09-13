<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundPolicy extends Model
{
    protected $fillable = [
        'title', 'description', 'days_limit', 'is_active'
    ];
}
