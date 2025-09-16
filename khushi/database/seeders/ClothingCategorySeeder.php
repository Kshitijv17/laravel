<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class ClothingCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // Men's Clothing
            [
                'name' => 'Men',
                'slug' => 'men',
                'description' => 'Men\'s Fashion & Clothing',
                'image' => 'categories/men.jpg',
                'status' => true,
                'children' => [
                    ['name' => 'Shirts', 'slug' => 'men-shirts', 'description' => 'Formal & Casual Shirts'],
                    ['name' => 'T-Shirts', 'slug' => 'men-tshirts', 'description' => 'Casual T-Shirts & Polos'],
                    ['name' => 'Jeans', 'slug' => 'men-jeans', 'description' => 'Denim Jeans & Pants'],
                    ['name' => 'Formal Wear', 'slug' => 'men-formal', 'description' => 'Suits, Blazers & Formal Pants'],
                    ['name' => 'Ethnic Wear', 'slug' => 'men-ethnic', 'description' => 'Kurtas, Sherwanis & Traditional Wear'],
                    ['name' => 'Sportswear', 'slug' => 'men-sports', 'description' => 'Gym & Sports Clothing'],
                    ['name' => 'Innerwear', 'slug' => 'men-innerwear', 'description' => 'Underwear & Sleepwear'],
                ]
            ],
            // Women's Clothing
            [
                'name' => 'Women',
                'slug' => 'women',
                'description' => 'Women\'s Fashion & Clothing',
                'image' => 'categories/women.jpg',
                'status' => true,
                'children' => [
                    ['name' => 'Kurtis', 'slug' => 'women-kurtis', 'description' => 'Designer Kurtis & Tunics'],
                    ['name' => 'Sarees', 'slug' => 'women-sarees', 'description' => 'Traditional & Designer Sarees'],
                    ['name' => 'Dresses', 'slug' => 'women-dresses', 'description' => 'Western Dresses & Gowns'],
                    ['name' => 'Tops & Shirts', 'slug' => 'women-tops', 'description' => 'Casual & Formal Tops'],
                    ['name' => 'Jeans & Pants', 'slug' => 'women-bottoms', 'description' => 'Jeans, Trousers & Leggings'],
                    ['name' => 'Ethnic Wear', 'slug' => 'women-ethnic', 'description' => 'Salwar Suits & Lehengas'],
                    ['name' => 'Lingerie', 'slug' => 'women-lingerie', 'description' => 'Bras, Panties & Sleepwear'],
                    ['name' => 'Sportswear', 'slug' => 'women-sports', 'description' => 'Activewear & Yoga Clothing'],
                ]
            ],
            // Kids Clothing
            [
                'name' => 'Kids',
                'slug' => 'kids',
                'description' => 'Kids Fashion & Clothing',
                'image' => 'categories/kids.jpg',
                'status' => true,
                'children' => [
                    ['name' => 'Boys Clothing', 'slug' => 'boys-clothing', 'description' => 'Boys T-Shirts, Shirts & Pants'],
                    ['name' => 'Girls Clothing', 'slug' => 'girls-clothing', 'description' => 'Girls Dresses, Tops & Bottoms'],
                    ['name' => 'Baby Clothing', 'slug' => 'baby-clothing', 'description' => 'Infant & Toddler Clothing'],
                    ['name' => 'School Uniforms', 'slug' => 'school-uniforms', 'description' => 'School Shirts, Pants & Accessories'],
                    ['name' => 'Party Wear', 'slug' => 'kids-party-wear', 'description' => 'Festive & Party Clothing'],
                ]
            ],
            // Footwear
            [
                'name' => 'Footwear',
                'slug' => 'footwear',
                'description' => 'Shoes, Sandals & Footwear',
                'image' => 'categories/footwear.jpg',
                'status' => true,
                'children' => [
                    ['name' => 'Men\'s Shoes', 'slug' => 'men-shoes', 'description' => 'Formal & Casual Shoes'],
                    ['name' => 'Women\'s Shoes', 'slug' => 'women-shoes', 'description' => 'Heels, Flats & Sandals'],
                    ['name' => 'Sports Shoes', 'slug' => 'sports-shoes', 'description' => 'Running & Training Shoes'],
                    ['name' => 'Kids Footwear', 'slug' => 'kids-footwear', 'description' => 'Children\'s Shoes & Sandals'],
                ]
            ],
            // Accessories
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Fashion Accessories',
                'image' => 'categories/accessories.jpg',
                'status' => true,
                'children' => [
                    ['name' => 'Bags', 'slug' => 'bags', 'description' => 'Handbags, Backpacks & Wallets'],
                    ['name' => 'Jewelry', 'slug' => 'jewelry', 'description' => 'Fashion Jewelry & Accessories'],
                    ['name' => 'Watches', 'slug' => 'watches', 'description' => 'Men\'s & Women\'s Watches'],
                    ['name' => 'Belts', 'slug' => 'belts', 'description' => 'Leather & Fashion Belts'],
                    ['name' => 'Sunglasses', 'slug' => 'sunglasses', 'description' => 'Designer & Sports Sunglasses'],
                    ['name' => 'Scarves & Stoles', 'slug' => 'scarves', 'description' => 'Fashion Scarves & Stoles'],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            $category = Category::create($categoryData);
            
            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                $childData['status'] = true;
                Category::create($childData);
            }
        }
    }
}
