<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@estore.com',
            'password' => Hash::make('admin123'),
            'phone' => '+1-555-0001',
            'status' => 'active',
            'permissions' => ['manage_all', 'view_analytics', 'manage_users', 'manage_products'],
            'email_verified_at' => now(),
        ]);

        // Create Regular Admin
        Admin::create([
            'name' => 'Store Manager',
            'email' => 'manager@estore.com',
            'password' => Hash::make('manager123'),
            'phone' => '+1-555-0002',
            'status' => 'active',
            'permissions' => ['manage_products', 'view_orders', 'manage_inventory'],
            'email_verified_at' => now(),
        ]);

        // Create additional admins
        Admin::factory(3)->create();
    }
}
