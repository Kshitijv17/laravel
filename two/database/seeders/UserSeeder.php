<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@larashop.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567890',
            'address' => '123 Admin Street',
            'city' => 'Admin City',
            'state' => 'Admin State',
            'postal_code' => '12345',
            'country' => 'US',
            'is_admin' => true,
        ]);

        // Create regular users
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1987654321',
            'address' => '456 Customer Lane',
            'city' => 'Customer City',
            'state' => 'Customer State',
            'postal_code' => '54321',
            'country' => 'US',
            'is_admin' => false,
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1122334455',
            'address' => '789 Buyer Boulevard',
            'city' => 'Buyer City',
            'state' => 'Buyer State',
            'postal_code' => '67890',
            'country' => 'US',
            'is_admin' => false,
        ]);
    }
}
