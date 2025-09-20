<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;
use App\Models\User;
use App\Models\Product;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create admin users (shopkeepers)
        $admins = User::where('role', 'admin')->get();
        
        if ($admins->count() < 3) {
            // Create sample admin users if we don't have enough
            $existingEmails = $admins->pluck('email')->toArray();
            $newAdmins = [];
            
            $sampleAdmins = [
                ['name' => 'John Smith', 'email' => 'john.shop@example.com'],
                ['name' => 'Sarah Johnson', 'email' => 'sarah.shop@example.com'],
                ['name' => 'Mike Wilson', 'email' => 'mike.shop@example.com'],
            ];
            
            foreach ($sampleAdmins as $adminData) {
                if (!in_array($adminData['email'], $existingEmails)) {
                    $newAdmins[] = User::create([
                        'name' => $adminData['name'],
                        'email' => $adminData['email'],
                        'password' => bcrypt('password'),
                        'role' => 'admin',
                        'email_verified_at' => now(),
                    ]);
                }
            }
            
            $admins = $admins->merge($newAdmins);
        }

        $shops = [
            [
                'name' => 'TechGear Pro',
                'description' => 'Your one-stop shop for the latest technology and gadgets. We specialize in computers, smartphones, and electronic accessories.',
                'address' => '123 Tech Street, Silicon Valley, CA 94000',
                'phone' => '+1-555-0123',
                'email' => 'info@techgearpro.com',
                'website' => 'https://techgearpro.com',
                'admin_id' => $admins->first()->id,
                'commission_rate' => 8.5,
                'is_active' => true,
            ],
            [
                'name' => 'Fashion Forward',
                'description' => 'Trendy clothing and accessories for the modern lifestyle. Discover the latest fashion trends and express your unique style.',
                'address' => '456 Fashion Ave, New York, NY 10001',
                'phone' => '+1-555-0456',
                'email' => 'hello@fashionforward.com',
                'website' => 'https://fashionforward.com',
                'admin_id' => $admins->skip(1)->first()->id,
                'commission_rate' => 12.0,
                'is_active' => true,
            ],
            [
                'name' => 'Home & Garden Paradise',
                'description' => 'Everything you need to make your house a home. From furniture to garden supplies, we have it all.',
                'address' => '789 Garden Lane, Austin, TX 78701',
                'phone' => '+1-555-0789',
                'email' => 'support@homegardenparadise.com',
                'website' => 'https://homegardenparadise.com',
                'admin_id' => $admins->skip(2)->first()->id,
                'commission_rate' => 10.0,
                'is_active' => true,
            ],
        ];

        foreach ($shops as $shopData) {
            $shop = Shop::create($shopData);
            
            // Create sample products for each shop
            $this->createSampleProducts($shop);
        }

        $this->command->info('Created ' . count($shops) . ' sample shops with products.');
    }

    /**
     * Create sample products for a shop
     */
    private function createSampleProducts(Shop $shop)
    {
        $products = [];

        if ($shop->name === 'TechGear Pro') {
            $products = [
                [
                    'title' => 'MacBook Pro 16-inch',
                    'description' => 'Powerful laptop with M2 Pro chip, perfect for professionals and creatives.',
                    'price' => 2499.00,
                    'selling_price' => 2299.00,
                    'quantity' => 15,
                    'category_id' => 1, // Assuming Electronics category exists
                ],
                [
                    'title' => 'iPhone 15 Pro',
                    'description' => 'Latest iPhone with advanced camera system and titanium design.',
                    'price' => 999.00,
                    'selling_price' => 949.00,
                    'quantity' => 25,
                    'category_id' => 1,
                ],
                [
                    'title' => 'Wireless Gaming Mouse',
                    'description' => 'High-precision wireless mouse designed for gaming enthusiasts.',
                    'price' => 79.99,
                    'selling_price' => 69.99,
                    'quantity' => 50,
                    'category_id' => 1,
                ],
            ];
        } elseif ($shop->name === 'Fashion Forward') {
            $products = [
                [
                    'title' => 'Designer Leather Jacket',
                    'description' => 'Premium leather jacket with modern cut and stylish design.',
                    'price' => 299.00,
                    'selling_price' => 249.00,
                    'quantity' => 20,
                    'category_id' => 2, // Assuming Fashion category exists
                ],
                [
                    'title' => 'Casual Summer Dress',
                    'description' => 'Comfortable and stylish dress perfect for summer occasions.',
                    'price' => 89.99,
                    'selling_price' => 79.99,
                    'quantity' => 35,
                    'category_id' => 2,
                ],
                [
                    'title' => 'Designer Sunglasses',
                    'description' => 'UV protection sunglasses with premium frame and lenses.',
                    'price' => 159.00,
                    'selling_price' => 139.00,
                    'quantity' => 40,
                    'category_id' => 2,
                ],
            ];
        } else { // Home & Garden Paradise
            $products = [
                [
                    'title' => 'Modern Coffee Table',
                    'description' => 'Sleek wooden coffee table perfect for modern living rooms.',
                    'price' => 399.00,
                    'selling_price' => 349.00,
                    'quantity' => 12,
                    'category_id' => 3, // Assuming Home category exists
                ],
                [
                    'title' => 'Garden Tool Set',
                    'description' => 'Complete set of essential gardening tools for your outdoor space.',
                    'price' => 129.99,
                    'selling_price' => 109.99,
                    'quantity' => 30,
                    'category_id' => 3,
                ],
                [
                    'title' => 'Decorative Plant Pot',
                    'description' => 'Beautiful ceramic plant pot to enhance your home decor.',
                    'price' => 49.99,
                    'selling_price' => 39.99,
                    'quantity' => 60,
                    'category_id' => 3,
                ],
            ];
        }

        foreach ($products as $productData) {
            $productData['shop_id'] = $shop->id;
            $productData['is_active'] = true;
            $productData['stock_status'] = $productData['quantity'] > 0 ? 'in_stock' : 'out_of_stock';
            
            Product::create($productData);
        }
    }
}
