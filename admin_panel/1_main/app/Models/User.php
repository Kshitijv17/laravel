<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_guest',
        'expires_at',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_guest' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user is a guest
     */
    public function isGuest(): bool
    {
        return $this->role === 'guest';
    }

    /**
     * Check if the user is a customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Check if the user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Get the user's role display name
     */
    public function getRoleDisplayAttribute(): string
    {
        return match($this->role) {
            'guest' => 'Guest',
            'customer' => 'Customer',
            'admin' => 'Admin',
            'superadmin' => 'Super Admin',
            default => 'Customer'
        };
    }

    /**
     * Get the user's role badge color
     */
    public function getRoleBadgeColorAttribute(): string
    {
        return match($this->role) {
            'guest' => 'secondary',
            'customer' => 'primary',
            'admin' => 'warning',
            'superadmin' => 'danger',
            default => 'secondary'
        };
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permission): bool
    {
        // Super admins have all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Regular admins have admin permissions by default
        if ($this->isAdmin()) {
            return true;
        }

        // Check specific permissions for regular users
        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        // Super admins have all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Regular admins have admin permissions by default
        if ($this->isAdmin()) {
            return true;
        }

        // Check if user has any of the specified permissions
        return $this->permissions()->whereIn('name', $permissions)->exists();
    }

    /**
     * Assign permissions to user
     */
    public function assignPermissions(array $permissions)
    {
        $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
        $this->permissions()->sync($permissionIds);
    }

    /**
     * Get user's permissions as array
     */
    public function getPermissionsArray(): array
    {
        return $this->permissions->pluck('name')->toArray();
    }

    /**
     * Orders relationship
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Shop relationship (for admin users)
     */
    public function shop()
    {
        return $this->hasOne(Shop::class, 'admin_id');
    }

    /**
     * Check if user is a shopkeeper (admin with a shop)
     */
    public function isShopkeeper(): bool
    {
        return $this->isAdmin() && $this->shop()->exists();
    }

    /**
     * Get user's shop products (for admin users)
     */
    public function shopProducts()
    {
        if (!$this->isAdmin()) {
            return collect();
        }
        
        return $this->shop ? $this->shop->products : collect();
    }

    /**
     * Get user's shop orders (for admin users)
     */
    public function shopOrders()
    {
        if (!$this->isAdmin()) {
            return collect();
        }
        
        return $this->shop ? $this->shop->orders : collect();
    }
}
