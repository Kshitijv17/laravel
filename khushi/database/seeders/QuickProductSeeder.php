<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class QuickProductSeeder extends Seeder
{
    public function run()
    {
        // Get categories
        $menCategory = Category::where('name', 'Men')->first();
        $womenCategory = Category::where('name', 'Women')->first();
        $kidsCategory = Category::where('name', 'Kids')->first();
        $footwearCategory = Category::where('name', 'Footwear')->first();
        $accessoriesCategory = Category::where('name', 'Accessories')->first();

        $products = [
            // Men's Products
            [
                'name' => 'Men\'s Classic Cotton T-Shirt',
                'slug' => 'mens-classic-cotton-tshirt',
                'description' => 'Premium quality cotton t-shirt with comfortable fit. Perfect for casual wear and everyday comfort.',
                'price' => 899.00,
                'discount_price' => 699.00,
                'stock' => 50,
                'category_id' => $menCategory?->id ?? 1,
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=400&fit=crop',
            ],
            [
                'name' => 'Men\'s Denim Jeans',
                'slug' => 'mens-denim-jeans',
                'description' => 'Stylish denim jeans with perfect fit and premium quality fabric. Available in multiple sizes.',
                'price' => 2499.00,
                'discount_price' => 1999.00,
                'stock' => 30,
                'category_id' => $menCategory?->id ?? 1,
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=400&h=400&fit=crop',
            ],
            [
                'name' => 'Men\'s Formal Shirt',
                'slug' => 'mens-formal-shirt',
                'description' => 'Professional formal shirt perfect for office wear. Wrinkle-free fabric with elegant design.',
                'price' => 1599.00,
                'discount_price' => 1299.00,
                'stock' => 40,
                'category_id' => $menCategory?->id ?? 1,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=400&h=400&fit=crop',
            ],

            // Women's Products
            [
                'name' => 'Women\'s Floral Maxi Dress',
                'slug' => 'womens-floral-maxi-dress',
                'description' => 'Beautiful floral maxi dress perfect for summer outings. Lightweight fabric with elegant design.',
                'price' => 2299.00,
                'discount_price' => 1799.00,
                'stock' => 25,
                'category_id' => $womenCategory?->id ?? 2,
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&h=400&fit=crop',
            ],
            [
                'name' => 'Women\'s Casual Top',
                'slug' => 'womens-casual-top',
                'description' => 'Comfortable casual top perfect for everyday wear. Soft fabric with modern fit.',
                'price' => 1299.00,
                'discount_price' => 999.00,
                'stock' => 35,
                'category_id' => $womenCategory?->id ?? 2,
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&h=400&fit=crop',
            ],
            [
                'name' => 'Women\'s Skinny Jeans',
                'slug' => 'womens-skinny-jeans',
                'description' => 'Trendy skinny jeans with perfect fit. High-quality denim with stretch for comfort.',
                'price' => 2199.00,
                'discount_price' => 1699.00,
                'stock' => 28,
                'category_id' => $womenCategory?->id ?? 2,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=400&h=400&fit=crop',
            ],

            // Kids Products
            [
                'name' => 'Kids Cotton T-Shirt Set',
                'slug' => 'kids-cotton-tshirt-set',
                'description' => 'Comfortable cotton t-shirt set for kids. Soft fabric with fun prints and vibrant colors.',
                'price' => 799.00,
                'discount_price' => 599.00,
                'stock' => 45,
                'category_id' => $kidsCategory?->id ?? 3,
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?w=400&h=400&fit=crop',
            ],
            [
                'name' => 'Kids Denim Shorts',
                'slug' => 'kids-denim-shorts',
                'description' => 'Stylish denim shorts for kids. Comfortable fit perfect for play and casual outings.',
                'price' => 899.00,
                'discount_price' => 699.00,
                'stock' => 32,
                'category_id' => $kidsCategory?->id ?? 3,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1503944583220-79d8926ad5e2?w=400&h=400&fit=crop',
            ],

            // Footwear
            [
                'name' => 'Men\'s Leather Formal Shoes',
                'slug' => 'mens-leather-formal-shoes',
                'description' => 'Premium leather formal shoes perfect for office and formal occasions. Comfortable and durable.',
                'price' => 3999.00,
                'discount_price' => 2999.00,
                'stock' => 20,
                'category_id' => $footwearCategory?->id ?? 4,
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400&h=400&fit=crop',
            ],
            [
                'name' => 'Women\'s Running Shoes',
                'slug' => 'womens-running-shoes',
                'description' => 'Comfortable running shoes with excellent grip and cushioning. Perfect for workouts and daily wear.',
                'price' => 2799.00,
                'discount_price' => 2199.00,
                'stock' => 25,
                'category_id' => $footwearCategory?->id ?? 4,
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=400&h=400&fit=crop',
            ],

            // Accessories
            [
                'name' => 'Leather Wallet',
                'slug' => 'leather-wallet',
                'description' => 'Premium leather wallet with multiple card slots and compartments. Elegant and functional design.',
                'price' => 1499.00,
                'discount_price' => 1199.00,
                'stock' => 40,
                'category_id' => $accessoriesCategory?->id ?? 5,
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&h=400&fit=crop',
            ],
            [
                'name' => 'Stylish Sunglasses',
                'slug' => 'stylish-sunglasses',
                'description' => 'Trendy sunglasses with UV protection. Perfect accessory for any outfit and outdoor activities.',
                'price' => 999.00,
                'discount_price' => 799.00,
                'stock' => 35,
                'category_id' => $accessoriesCategory?->id ?? 5,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400&h=400&fit=crop',
            ],
        ];

        foreach ($products as $productData) {
            Product::create([
                'name' => $productData['name'],
                'slug' => $productData['slug'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'discount_price' => $productData['discount_price'],
                'stock' => $productData['stock'],
                'category_id' => $productData['category_id'],
                'is_featured' => $productData['is_featured'],
                'status' => true,
                'image' => $productData['image'],
                'sku' => strtoupper(str_replace('-', '_', $productData['slug'])),
                'sizes' => json_encode(['XS', 'S', 'M', 'L', 'XL']),
                'colors' => json_encode(['Black', 'White', 'Navy', 'Gray', 'Blue']),
                'fabric' => 'Premium Cotton',
                'pattern' => 'Solid',
                'fit_type' => 'Regular',
                'sleeve_type' => 'Short Sleeve',
                'neck_type' => 'Round Neck',
                'occasion' => 'Casual',
                'care_instructions' => 'Machine wash cold',
                'country_of_origin' => 'India',
                'meta_data' => json_encode([
                    'brand' => 'Fashion Brand',
                    'material' => 'Premium Quality',
                    'weight' => '250g'
                ])
            ]);
        }
    }
}
