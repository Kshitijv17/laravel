<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsLog extends Model
{
    protected $fillable = [
        'user_id', 'page', 'action',
        'device', 'ip_address', 'timestamp'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
