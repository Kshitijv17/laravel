<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name', 'logo', 'favicon', 'contact_email',
        'contact_phone', 'address', 'currency',
        'timezone', 'maintenance_mode'
    ];
}
