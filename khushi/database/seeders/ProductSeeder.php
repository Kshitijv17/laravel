<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        
        // Electronics Products
        $electronicsCategory = Category::where('name', 'Electronics')->first();
        if ($electronicsCategory) {
            $electronicsProducts = [
                [
                    'name' => 'iPhone 15 Pro Max',
                    'description' => 'Latest Apple iPhone with A17 Pro chip and titanium design',
                    'price' => 1199.99,
                    'sale_price' => 1099.99,
                    'stock_quantity' => 50,
                    'brand' => 'Apple',
                    'images' => [
                        'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=500&h=500&fit=crop',
                        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=500&fit=crop'
                    ]
                ],
                [
                    'name' => 'Samsung Galaxy S24 Ultra',
                    'description' => 'Premium Android smartphone with S Pen and AI features',
                    'price' => 1299.99,
                    'stock_quantity' => 35,
                    'brand' => 'Samsung',
                    'images' => [
                        'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=500&h=500&fit=crop'
                    ]
                ],
                [
                    'name' => 'MacBook Pro 16-inch',
                    'description' => 'Powerful laptop with M3 Pro chip for professionals',
                    'price' => 2499.99,
                    'sale_price' => 2299.99,
                    'stock_quantity' => 25,
                    'brand' => 'Apple',
                    'images' => [
                        'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=500&h=500&fit=crop'
                    ]
                ],
                [
                    'name' => 'Sony WH-1000XM5 Headphones',
                    'description' => 'Premium noise-canceling wireless headphones',
                    'price' => 399.99,
                    'sale_price' => 349.99,
                    'stock_quantity' => 75,
                    'brand' => 'Sony',
                    'images' => [
                        'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&h=500&fit=crop'
                    ]
                ],
            ];

            foreach ($electronicsProducts as $productData) {
                $product = Product::create([
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']),
                    'description' => $productData['description'],
                    'sku' => fake()->unique()->regexify('[A-Z]{3}[0-9]{6}'),
                    'price' => $productData['price'],
                    'discount_price' => $productData['sale_price'] ?? null,
                    'stock' => $productData['stock_quantity'],
                    'image' => $productData['images'][0] ?? fake()->imageUrl(500, 500, 'business'),
                    'status' => true,
                    'is_featured' => fake()->boolean(40),
                    'category_id' => $electronicsCategory->id,
                ]);

                // Product images are stored in the main image field
            }
        }

        // Fashion Products
        $fashionCategory = Category::where('name', 'Fashion')->first();
        if ($fashionCategory) {
            $fashionProducts = [
                [
                    'name' => 'Classic Denim Jacket',
                    'description' => 'Timeless denim jacket perfect for any casual outfit',
                    'price' => 79.99,
                    'sale_price' => 59.99,
                    'stock_quantity' => 100,
                    'brand' => 'Levi\'s',
                    'images' => [
                        'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=500&h=500&fit=crop'
                    ]
                ],
                [
                    'name' => 'Running Sneakers',
                    'description' => 'Comfortable running shoes with excellent cushioning',
                    'price' => 129.99,
                    'stock_quantity' => 80,
                    'brand' => 'Nike',
                    'images' => [
                        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500&h=500&fit=crop'
                    ]
                ],
                [
                    'name' => 'Elegant Evening Dress',
                    'description' => 'Beautiful evening dress for special occasions',
                    'price' => 199.99,
                    'sale_price' => 149.99,
                    'stock_quantity' => 45,
                    'brand' => 'Zara',
                    'images' => [
                        'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=500&h=500&fit=crop'
                    ]
                ],
            ];

            foreach ($fashionProducts as $productData) {
                $product = Product::create([
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']),
                    'description' => $productData['description'],
                    'sku' => fake()->unique()->regexify('[A-Z]{3}[0-9]{6}'),
                    'price' => $productData['price'],
                    'discount_price' => $productData['sale_price'] ?? null,
                    'stock' => $productData['stock_quantity'],
                    'image' => $productData['images'][0] ?? fake()->imageUrl(500, 500, 'business'),
                    'status' => true,
                    'is_featured' => fake()->boolean(30),
                    'category_id' => $fashionCategory->id,
                ]);

                // Product images are stored in the main image field
            }
        }

        // Create additional random products for all categories
        foreach ($categories->take(6) as $category) {
            $products = Product::factory(15)->create([
                'category_id' => $category->id,
            ]);

            // Products already have images from factory
        }
    }
}
