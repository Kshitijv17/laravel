<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::insert([
            [
                'title' => 'Electronics',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Books',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Fashion',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
