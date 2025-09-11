<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        $products = [
            // Electronics
            [
                'name' => 'Wireless Bluetooth Headphones',
                'slug' => 'wireless-bluetooth-headphones',
                'description' => 'High-quality wireless headphones with noise cancellation and premium sound quality. Perfect for music lovers and professionals.',
                'short_description' => 'Premium wireless headphones with noise cancellation',
                'price' => 199.99,
                'sale_price' => 149.99,
                'sku' => 'WBH001',
                'stock_quantity' => 50,
                'category_id' => $categories->where('slug', 'electronics')->first()->id,
                'brand' => 'AudioTech',
                'weight' => 0.5,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Smartphone 128GB',
                'slug' => 'smartphone-128gb',
                'description' => 'Latest smartphone with advanced camera system, fast processor, and long-lasting battery life.',
                'short_description' => 'Advanced smartphone with 128GB storage',
                'price' => 799.99,
                'sale_price' => null,
                'sku' => 'SP128001',
                'stock_quantity' => 25,
                'category_id' => $categories->where('slug', 'electronics')->first()->id,
                'brand' => 'TechCorp',
                'weight' => 0.2,
                'is_active' => true,
                'is_featured' => true,
            ],
            
            // Clothing
            [
                'name' => 'Cotton T-Shirt',
                'slug' => 'cotton-t-shirt',
                'description' => 'Comfortable 100% cotton t-shirt available in multiple colors. Perfect for casual wear.',
                'short_description' => '100% cotton comfortable t-shirt',
                'price' => 29.99,
                'sale_price' => 19.99,
                'sku' => 'CT001',
                'stock_quantity' => 100,
                'category_id' => $categories->where('slug', 'clothing')->first()->id,
                'brand' => 'ComfortWear',
                'weight' => 0.2,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Denim Jeans',
                'slug' => 'denim-jeans',
                'description' => 'Classic denim jeans with modern fit. Durable and stylish for everyday wear.',
                'short_description' => 'Classic denim jeans with modern fit',
                'price' => 89.99,
                'sale_price' => null,
                'sku' => 'DJ001',
                'stock_quantity' => 75,
                'category_id' => $categories->where('slug', 'clothing')->first()->id,
                'brand' => 'DenimCo',
                'weight' => 0.7,
                'is_active' => true,
                'is_featured' => true,
            ],
            
            // Home & Garden
            [
                'name' => 'Coffee Maker',
                'slug' => 'coffee-maker',
                'description' => 'Programmable coffee maker with 12-cup capacity. Perfect for home and office use.',
                'short_description' => 'Programmable 12-cup coffee maker',
                'price' => 129.99,
                'sale_price' => 99.99,
                'sku' => 'CM001',
                'stock_quantity' => 30,
                'category_id' => $categories->where('slug', 'home-garden')->first()->id,
                'brand' => 'BrewMaster',
                'weight' => 3.5,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Garden Tools Set',
                'slug' => 'garden-tools-set',
                'description' => 'Complete set of essential garden tools including spade, rake, and pruning shears.',
                'short_description' => 'Complete set of essential garden tools',
                'price' => 79.99,
                'sale_price' => null,
                'sku' => 'GTS001',
                'stock_quantity' => 40,
                'category_id' => $categories->where('slug', 'home-garden')->first()->id,
                'brand' => 'GardenPro',
                'weight' => 2.0,
                'is_active' => true,
                'is_featured' => false,
            ],
            
            // Sports & Outdoors
            [
                'name' => 'Yoga Mat',
                'slug' => 'yoga-mat',
                'description' => 'Non-slip yoga mat perfect for yoga, pilates, and fitness exercises. Eco-friendly material.',
                'short_description' => 'Non-slip eco-friendly yoga mat',
                'price' => 49.99,
                'sale_price' => 34.99,
                'sku' => 'YM001',
                'stock_quantity' => 60,
                'category_id' => $categories->where('slug', 'sports-outdoors')->first()->id,
                'brand' => 'FitLife',
                'weight' => 1.2,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Running Shoes',
                'slug' => 'running-shoes',
                'description' => 'Lightweight running shoes with superior cushioning and breathable design.',
                'short_description' => 'Lightweight running shoes with cushioning',
                'price' => 159.99,
                'sale_price' => null,
                'sku' => 'RS001',
                'stock_quantity' => 45,
                'category_id' => $categories->where('slug', 'sports-outdoors')->first()->id,
                'brand' => 'RunFast',
                'weight' => 0.8,
                'is_active' => true,
                'is_featured' => true,
            ],
            
            // Books
            [
                'name' => 'Programming Guide',
                'slug' => 'programming-guide',
                'description' => 'Comprehensive guide to modern programming languages and best practices.',
                'short_description' => 'Comprehensive programming guide',
                'price' => 59.99,
                'sale_price' => 44.99,
                'sku' => 'PG001',
                'stock_quantity' => 80,
                'category_id' => $categories->where('slug', 'books')->first()->id,
                'brand' => 'TechBooks',
                'weight' => 0.6,
                'is_active' => true,
                'is_featured' => false,
            ],
            
            // Beauty & Health
            [
                'name' => 'Skincare Set',
                'slug' => 'skincare-set',
                'description' => 'Complete skincare routine set with cleanser, toner, and moisturizer.',
                'short_description' => 'Complete skincare routine set',
                'price' => 89.99,
                'sale_price' => 69.99,
                'sku' => 'SS001',
                'stock_quantity' => 35,
                'category_id' => $categories->where('slug', 'beauty-health')->first()->id,
                'brand' => 'GlowSkin',
                'weight' => 0.4,
                'is_active' => true,
                'is_featured' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
