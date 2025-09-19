<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Products Module
            [
                'name' => 'products.view',
                'display_name' => 'View Products',
                'description' => 'Can view product listings',
                'module' => 'products'
            ],
            [
                'name' => 'products.create',
                'display_name' => 'Create Products',
                'description' => 'Can create new products',
                'module' => 'products'
            ],
            [
                'name' => 'products.edit',
                'display_name' => 'Edit Products',
                'description' => 'Can edit existing products',
                'module' => 'products'
            ],
            [
                'name' => 'products.delete',
                'display_name' => 'Delete Products',
                'description' => 'Can delete products',
                'module' => 'products'
            ],
            [
                'name' => 'products.bulk_upload',
                'display_name' => 'Bulk Upload Products',
                'description' => 'Can upload products via CSV',
                'module' => 'products'
            ],

            // Categories Module
            [
                'name' => 'categories.view',
                'display_name' => 'View Categories',
                'description' => 'Can view category listings',
                'module' => 'categories'
            ],
            [
                'name' => 'categories.create',
                'display_name' => 'Create Categories',
                'description' => 'Can create new categories',
                'module' => 'categories'
            ],
            [
                'name' => 'categories.edit',
                'display_name' => 'Edit Categories',
                'description' => 'Can edit existing categories',
                'module' => 'categories'
            ],
            [
                'name' => 'categories.delete',
                'display_name' => 'Delete Categories',
                'description' => 'Can delete categories',
                'module' => 'categories'
            ],

            // Orders Module
            [
                'name' => 'orders.view',
                'display_name' => 'View Orders',
                'description' => 'Can view order listings',
                'module' => 'orders'
            ],
            [
                'name' => 'orders.view_details',
                'display_name' => 'View Order Details',
                'description' => 'Can view detailed order information',
                'module' => 'orders'
            ],
            [
                'name' => 'orders.update_status',
                'display_name' => 'Update Order Status',
                'description' => 'Can update order status',
                'module' => 'orders'
            ],
            [
                'name' => 'orders.process_payments',
                'display_name' => 'Process Payments',
                'description' => 'Can process and manage payments',
                'module' => 'orders'
            ],

            // Users Module
            [
                'name' => 'users.view',
                'display_name' => 'View Users',
                'description' => 'Can view user listings',
                'module' => 'users'
            ],
            [
                'name' => 'users.view_details',
                'display_name' => 'View User Details',
                'description' => 'Can view detailed user information',
                'module' => 'users'
            ],
            [
                'name' => 'users.create',
                'display_name' => 'Create Users',
                'description' => 'Can create new user accounts',
                'module' => 'users'
            ],
            [
                'name' => 'users.edit',
                'display_name' => 'Edit Users',
                'description' => 'Can edit user information',
                'module' => 'users'
            ],
            [
                'name' => 'users.delete',
                'display_name' => 'Delete Users',
                'description' => 'Can delete user accounts',
                'module' => 'users'
            ],

            // Analytics Module
            [
                'name' => 'analytics.view',
                'display_name' => 'View Analytics',
                'description' => 'Can view analytics and reports',
                'module' => 'analytics'
            ],
            [
                'name' => 'analytics.export',
                'display_name' => 'Export Analytics',
                'description' => 'Can export analytics data',
                'module' => 'analytics'
            ],

            // Settings Module
            [
                'name' => 'settings.view',
                'display_name' => 'View Settings',
                'description' => 'Can view system settings',
                'module' => 'settings'
            ],
            [
                'name' => 'settings.edit',
                'display_name' => 'Edit Settings',
                'description' => 'Can modify system settings',
                'module' => 'settings'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
