<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Latest gadgets, smartphones, laptops and electronic devices',
                'icon' => 'laptop',
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400&h=300&fit=crop',
            ],
            [
                'name' => 'Fashion',
                'description' => 'Trendy clothing, shoes, and accessories for men and women',
                'icon' => 'tshirt',
                'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=300&fit=crop',
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Furniture, decor, and garden essentials for your home',
                'icon' => 'home',
                'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=300&fit=crop',
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Sports equipment, outdoor gear, and fitness accessories',
                'icon' => 'football',
                'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop',
            ],
            [
                'name' => 'Health & Beauty',
                'description' => 'Skincare, cosmetics, and health products',
                'icon' => 'heart',
                'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=400&h=300&fit=crop',
            ],
            [
                'name' => 'Books & Media',
                'description' => 'Books, movies, music, and educational materials',
                'icon' => 'book',
                'image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400&h=300&fit=crop',
            ],
            [
                'name' => 'Toys & Games',
                'description' => 'Fun toys, board games, and entertainment for all ages',
                'icon' => 'gamepad',
                'image' => 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&h=300&fit=crop',
            ],
            [
                'name' => 'Automotive',
                'description' => 'Car accessories, parts, and automotive tools',
                'icon' => 'car',
                'image' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=400&h=300&fit=crop',
            ],
        ];

        foreach ($categories as $index => $categoryData) {
            $category = Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'image' => $categoryData['image'],
                'status' => true,
            ]);

            // Create subcategories for some main categories
            if ($categoryData['name'] === 'Electronics') {
                $subcategories = ['Smartphones', 'Laptops', 'Tablets', 'Headphones'];
                foreach ($subcategories as $subIndex => $subName) {
                    Category::create([
                        'name' => $subName,
                        'slug' => Str::slug($subName),
                        'description' => 'Best ' . strtolower($subName) . ' collection',
                        'status' => true,
                    ]);
                }
            }

            if ($categoryData['name'] === 'Fashion') {
                $subcategories = ['Men\'s Clothing', 'Women\'s Clothing', 'Shoes', 'Accessories'];
                foreach ($subcategories as $subIndex => $subName) {
                    Category::create([
                        'name' => $subName,
                        'slug' => Str::slug($subName),
                        'description' => 'Trendy ' . strtolower($subName),
                        'status' => true,
                    ]);
                }
            }
        }
    }
}
