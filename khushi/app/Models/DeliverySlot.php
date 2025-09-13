<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliverySlot extends Model
{
    protected $fillable = [
        'day', 'start_time', 'end_time', 'is_available'
    ];
}
