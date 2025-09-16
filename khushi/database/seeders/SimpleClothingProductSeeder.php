<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class SimpleClothingProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Women\'s Rayon Printed Kurti',
                'slug' => 'womens-rayon-printed-kurti',
                'description' => 'Elegant rayon kurti with floral prints, perfect for daily wear and festive occasions.',
                'sku' => 'KRTY-001',
                'price' => 499.00,
                'discount_price' => 424.15,
                'stock' => 35,
                'status' => true,
                'is_featured' => true,
                'category_slug' => 'women-kurtis',
                'image' => 'products/kurti-1.jpg'
            ],
            [
                'name' => 'Men\'s Cotton Formal Shirt',
                'slug' => 'mens-cotton-formal-shirt',
                'description' => 'Premium cotton formal shirt with regular fit. Perfect for office wear.',
                'sku' => 'SHIRT-001',
                'price' => 899.00,
                'discount_price' => 719.20,
                'stock' => 50,
                'status' => true,
                'is_featured' => true,
                'category_slug' => 'men-shirts',
                'image' => 'products/shirt-1.jpg'
            ],
            [
                'name' => 'Women\'s Floral Maxi Dress',
                'slug' => 'womens-floral-maxi-dress',
                'description' => 'Beautiful floral maxi dress perfect for summer outings and casual wear.',
                'sku' => 'DRESS-001',
                'price' => 1299.00,
                'discount_price' => 999.00,
                'stock' => 25,
                'status' => true,
                'is_featured' => true,
                'category_slug' => 'women-dresses',
                'image' => 'products/dress-1.jpg'
            ],
            [
                'name' => 'Men\'s Denim Jeans',
                'slug' => 'mens-denim-jeans',
                'description' => 'Classic blue denim jeans with regular fit and comfortable stretch.',
                'sku' => 'JEANS-001',
                'price' => 1599.00,
                'discount_price' => 1199.00,
                'stock' => 40,
                'status' => true,
                'is_featured' => false,
                'category_slug' => 'men-jeans',
                'image' => 'products/jeans-1.jpg'
            ],
            [
                'name' => 'Kids Cotton T-Shirt',
                'slug' => 'kids-cotton-tshirt',
                'description' => 'Soft cotton t-shirt for kids with fun cartoon prints.',
                'sku' => 'KIDS-001',
                'price' => 299.00,
                'discount_price' => 249.00,
                'stock' => 60,
                'status' => true,
                'is_featured' => false,
                'category_slug' => 'boys-clothing',
                'image' => 'products/kids-tshirt-1.jpg'
            ],
            [
                'name' => 'Women\'s Running Shoes',
                'slug' => 'womens-running-shoes',
                'description' => 'Lightweight running shoes with excellent cushioning and support.',
                'sku' => 'SHOES-001',
                'price' => 2499.00,
                'discount_price' => 1999.00,
                'stock' => 30,
                'status' => true,
                'is_featured' => true,
                'category_slug' => 'women-shoes',
                'image' => 'products/shoes-1.jpg'
            ]
        ];

        foreach ($products as $productData) {
            $categorySlug = $productData['category_slug'];
            unset($productData['category_slug']);
            
            // Find category
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $productData['category_id'] = $category->id;
                Product::create($productData);
            }
        }
    }
}
