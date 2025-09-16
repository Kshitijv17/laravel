<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ClothingProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            // Women's Kurtis
            [
                'name' => 'Women\'s Rayon Printed Kurti',
                'slug' => 'womens-rayon-printed-kurti',
                'description' => 'Elegant rayon kurti with floral prints, perfect for daily wear and festive occasions. Soft fabric, breathable design, and vibrant colors.',
                'sku' => 'KRTY-STYLAURA-RED-M',
                'price' => 499.00,
                'discount_price' => 424.15,
                'stock' => 35,
                'status' => true,
                'is_featured' => true,
                'category_slug' => 'women-kurtis',
                'brand' => 'StyleAura',
                'attributes' => [
                    'fabric' => 'Rayon',
                    'pattern' => 'Floral Print',
                    'fit' => 'Regular Fit',
                    'sleeve_type' => '3/4 Sleeve',
                    'neck_type' => 'Round Neck',
                    'length' => 'Calf Length',
                    'occasion' => 'Casual, Festive',
                    'wash_care' => 'Machine Wash',
                    'country_of_origin' => 'India'
                ]
            ],
            // Men's Shirts
            [
                'name' => 'Men\'s Cotton Formal Shirt',
                'slug' => 'mens-cotton-formal-shirt',
                'description' => 'Premium cotton formal shirt with regular fit. Perfect for office wear and formal occasions. Wrinkle-free fabric with comfortable fit.',
                'sku' => 'SHIRT-FORMAL-WHITE-L',
                'price' => 899.00,
                'discount_price' => 719.20,
                'stock' => 50,
                'status' => true,
                'is_featured' => true,
                'category_slug' => 'men-shirts',
                'brand' => 'Arrow',
                'attributes' => [
                    'fabric' => 'Cotton',
                    'pattern' => 'Solid',
                    'fit' => 'Regular Fit',
                    'sleeve_type' => 'Full Sleeve',
                    'collar_type' => 'Spread Collar',
                    'occasion' => 'Formal, Office',
                    'wash_care' => 'Machine Wash',
                    'country_of_origin' => 'India'
                ],
                'variants' => [
                    ['size' => 'M', 'color' => 'White', 'stock' => 15],
                    ['size' => 'L', 'color' => 'White', 'stock' => 20],
                    ['size' => 'XL', 'color' => 'Blue', 'stock' => 10],
                    ['size' => 'XXL', 'color' => 'Blue', 'stock' => 5]
                ]
            ],
            // Women's Dresses
            [
                'name' => 'Women\'s Floral Maxi Dress',
                'slug' => 'womens-floral-maxi-dress',
                'description' => 'Beautiful floral maxi dress perfect for summer outings and casual wear. Lightweight fabric with comfortable fit and elegant design.',
                'short_description' => 'Beautiful floral maxi dress',
                'sku' => 'DRESS-MAXI-FLORAL-M',
                'price' => 1299.00,
                'sale_price' => 999.00,
                'stock_quantity' => 25,
                'status' => 'active',
                'is_featured' => true,
                'category_slug' => 'women-dresses',
                'brand' => 'Vero Moda',
                'attributes' => [
                    'fabric' => 'Polyester',
                    'pattern' => 'Floral Print',
                    'fit' => 'A-Line',
                    'sleeve_type' => 'Sleeveless',
                    'length' => 'Maxi',
                    'occasion' => 'Casual, Party',
                    'wash_care' => 'Hand Wash',
                    'country_of_origin' => 'India'
                ],
                'variants' => [
                    ['size' => 'S', 'color' => 'Pink', 'stock' => 8],
                    ['size' => 'M', 'color' => 'Pink', 'stock' => 10],
                    ['size' => 'L', 'color' => 'Yellow', 'stock' => 7]
                ]
            ],
            // Men's Jeans
            [
                'name' => 'Men\'s Slim Fit Jeans',
                'slug' => 'mens-slim-fit-jeans',
                'description' => 'Premium denim jeans with slim fit design. Comfortable stretch fabric with modern styling. Perfect for casual and semi-formal occasions.',
                'short_description' => 'Premium slim fit denim jeans',
                'sku' => 'JEANS-SLIM-BLUE-32',
                'price' => 1599.00,
                'sale_price' => 1199.00,
                'stock_quantity' => 40,
                'status' => 'active',
                'is_featured' => true,
                'category_slug' => 'men-jeans',
                'brand' => 'Levi\'s',
                'attributes' => [
                    'fabric' => 'Denim (Cotton Blend)',
                    'fit' => 'Slim Fit',
                    'rise' => 'Mid Rise',
                    'stretch' => 'Stretchable',
                    'wash' => 'Dark Blue',
                    'occasion' => 'Casual',
                    'wash_care' => 'Machine Wash',
                    'country_of_origin' => 'India'
                ],
                'variants' => [
                    ['size' => '30', 'color' => 'Dark Blue', 'stock' => 12],
                    ['size' => '32', 'color' => 'Dark Blue', 'stock' => 15],
                    ['size' => '34', 'color' => 'Light Blue', 'stock' => 8],
                    ['size' => '36', 'color' => 'Black', 'stock' => 5]
                ]
            ],
            // Kids Clothing
            [
                'name' => 'Kids Cotton T-Shirt Set',
                'slug' => 'kids-cotton-tshirt-set',
                Product::create([
                'name' => 'Kids Cotton T-Shirt Set',
                'slug' => 'kids-cotton-tshirt-set',
                'description' => 'Comfortable cotton t-shirt set for kids. Soft fabric, vibrant colors, and fun prints. Perfect for daily wear and play.',
                'sku' => 'KIDS-TSHIRT-SET-6Y',
                'price' => 699.00,
                'discount_price' => 549.00,
                'stock' => 30,
                'status' => 'active',
                'is_featured' => true,
                'category_id' => 1,
                'image' => 'https://images.unsplash.com/photo-' . rand(1500000000000, 1700000000000) . '?w=400&h=400&fit=crop',
                'sizes' => json_encode(['XS', 'S', 'M', 'L', 'XL']),
                'colors' => json_encode(['Black', 'White', 'Navy', 'Gray']),
                'fabric' => 'Cotton Blend',
                'pattern' => 'Solid',
                'fit_type' => 'Regular',
                'sleeve_type' => 'Short Sleeve',
                'neck_type' => 'Round Neck',
                'occasion' => 'Casual',
                'care_instructions' => 'Machine wash cold',
                'country_of_origin' => 'India',
                'meta_data' => json_encode([
                    'brand' => 'FirstCry',
                    'material' => 'Premium Cotton',
                    'weight' => '200g'
                ])
            ]);    'brand' => 'FirstCry',
                'attributes' => [
                    'fabric' => '100% Cotton',
                    'pattern' => 'Cartoon Print',
                    'fit' => 'Regular Fit',
                    'sleeve_type' => 'Short Sleeve',
                    'wash_care' => 'Machine Wash',
                    'age_group' => '3-8 Years'
                ],
                'variants' => [
                    ['size' => '3-4Y', 'color' => 'Red', 'stock' => 8],
                    ['size' => '5-6Y', 'color' => 'Blue', 'stock' => 12],
                    ['size' => '7-8Y', 'color' => 'Green', 'stock' => 10]
                ]
            ],
            // Footwear
            [
                'name' => 'Men\'s Leather Formal Shoes',
                'slug' => 'mens-leather-formal-shoes',
                'description' => 'Genuine leather formal shoes with classic design. Perfect for office wear and formal occasions. Comfortable sole with premium finish.',
                'short_description' => 'Genuine leather formal shoes',
                'sku' => 'SHOES-FORMAL-BLACK-9',
                'price' => 2499.00,
                'sale_price' => 1999.00,
                'stock_quantity' => 20,
                'status' => 'active',
                'is_featured' => true,
                'category_slug' => 'men-shoes',
                'brand' => 'Bata',
                'attributes' => [
                    'material' => 'Genuine Leather',
                    'sole_material' => 'Rubber',
                    'closure' => 'Lace-up',
                    'toe_style' => 'Round Toe',
                    'occasion' => 'Formal, Office',
                    'care_instructions' => 'Polish Regularly'
                ],
                'variants' => [
                    ['size' => '7', 'color' => 'Black', 'stock' => 5],
                    ['size' => '8', 'color' => 'Black', 'stock' => 8],
                    ['size' => '9', 'color' => 'Brown', 'stock' => 4],
                    ['size' => '10', 'color' => 'Brown', 'stock' => 3]
                ]
            ]
        ];

        foreach ($products as $productData) {
            $variants = $productData['variants'] ?? [];
            $attributes = $productData['attributes'] ?? [];
            $categorySlug = $productData['category_slug'];
            $brand = $productData['brand'] ?? null;
            
            unset($productData['variants'], $productData['attributes'], $productData['category_slug'], $productData['brand']);
            
            // Find category
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $productData['category_id'] = $category->id;
            }
            
            $product = Product::create($productData);
            
            // Store attributes and brand as JSON in meta_data field
            $metaData = [];
            if (!empty($attributes)) {
                $metaData = array_merge($metaData, $attributes);
            }
            if ($brand) {
                $metaData['brand'] = $brand;
            }
            
            if (!empty($metaData)) {
                $product->update(['meta_data' => json_encode($metaData)]);
            }
        }
    }
}
