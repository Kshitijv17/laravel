<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingGroup extends Model
{
    protected $fillable = [
        'group_name', 'key', 'value',
        'type', 'is_active'
    ];
}
