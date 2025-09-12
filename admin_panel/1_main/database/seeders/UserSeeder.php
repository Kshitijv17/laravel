<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Cool Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'is_guest' => false,
        ]);
    }
}

