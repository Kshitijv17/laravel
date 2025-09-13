<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_default',
        'status'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_default' => 'boolean',
        'status' => 'boolean',
    ];

    // Relationships
    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status;
    }

    // Methods
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []) ||
               $this->permissions()->where('name', $permission)->exists();
    }

    public function givePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }
        
        if ($permission) {
            $this->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }
}
