<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'module',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions');
    }

    /**
     * Get permissions by module
     */
    public static function getByModule($module)
    {
        return self::where('module', $module)->get();
    }

    /**
     * Get all available modules
     */
    public static function getModules()
    {
        return self::distinct()->pluck('module')->toArray();
    }
}
