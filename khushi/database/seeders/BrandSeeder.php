<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'description' => 'Just Do It - Leading sportswear brand',
                'website' => 'https://www.nike.com',
                'status' => 'active',
                'sort_order' => 1,
                'meta_title' => 'Nike - Just Do It',
                'meta_description' => 'Shop Nike shoes, clothing and accessories'
            ],
            [
                'name' => 'Adidas',
                'slug' => 'adidas',
                'description' => 'Impossible is Nothing - German sportswear brand',
                'website' => 'https://www.adidas.com',
                'status' => 'active',
                'sort_order' => 2,
                'meta_title' => 'Adidas - Impossible is Nothing',
                'meta_description' => 'Shop Adidas shoes, clothing and accessories'
            ],
            [
                'name' => 'Puma',
                'slug' => 'puma',
                'description' => 'Forever Faster - Athletic and casual footwear',
                'website' => 'https://www.puma.com',
                'status' => 'active',
                'sort_order' => 3,
                'meta_title' => 'Puma - Forever Faster',
                'meta_description' => 'Shop Puma shoes, clothing and accessories'
            ],
            [
                'name' => 'Levis',
                'slug' => 'levis',
                'description' => 'Original jeans brand since 1853',
                'website' => 'https://www.levi.com',
                'status' => 'active',
                'sort_order' => 4,
                'meta_title' => 'Levis - Original Jeans',
                'meta_description' => 'Shop Levis jeans, jackets and denim clothing'
            ],
            [
                'name' => 'H&M',
                'slug' => 'hm',
                'description' => 'Fashion and quality at the best price',
                'website' => 'https://www.hm.com',
                'status' => 'active',
                'sort_order' => 5,
                'meta_title' => 'H&M - Fashion and Quality',
                'meta_description' => 'Shop H&M fashion for men, women and kids'
            ],
            [
                'name' => 'Zara',
                'slug' => 'zara',
                'description' => 'Spanish fast fashion retailer',
                'website' => 'https://www.zara.com',
                'status' => 'active',
                'sort_order' => 6,
                'meta_title' => 'Zara - Fast Fashion',
                'meta_description' => 'Shop Zara fashion for men, women and kids'
            ],
            [
                'name' => 'Roadster',
                'slug' => 'roadster',
                'description' => 'Trendy casual wear for young India',
                'website' => 'https://www.myntra.com/roadster',
                'status' => 'active',
                'sort_order' => 7,
                'meta_title' => 'Roadster - Trendy Casual Wear',
                'meta_description' => 'Shop Roadster casual clothing and accessories'
            ],
            [
                'name' => 'U.S. Polo Assn.',
                'slug' => 'us-polo-assn',
                'description' => 'Official brand of the United States Polo Association',
                'website' => 'https://www.uspoloassn.com',
                'status' => 'active',
                'sort_order' => 8,
                'meta_title' => 'U.S. Polo Assn. - Official Brand',
                'meta_description' => 'Shop U.S. Polo Assn. clothing and accessories'
            ]
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
