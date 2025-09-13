<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Test User
        User::create([
            'name' => 'John Doe',
            'email' => 'user@estore.com',
            'password' => Hash::make('user123'),
            'phone' => '+1-555-1001',
            'date_of_birth' => '1990-01-15',
            'gender' => 'male',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Female User
        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@estore.com',
            'password' => Hash::make('user123'),
            'phone' => '+1-555-1002',
            'date_of_birth' => '1992-05-20',
            'gender' => 'female',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create additional random users
        User::factory(48)->create();
    }
}
