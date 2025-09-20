<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // First, let's make sure we have shops and categories
        $shops = Shop::all();
        $categories = Category::all();
        
        if ($shops->isEmpty() || $categories->isEmpty()) {
            $this->command->info('Please run ShopSeeder and CategorySeeder first!');
            return;
        }

        // Clear existing products (using delete to avoid foreign key constraint issues)
        Product::query()->delete();

        $products = [
            // TechGear Pro Products (Shop 1)
            [
                'title' => 'iPhone 15 Pro Max',
                'description' => 'Latest Apple iPhone with A17 Pro chip, 256GB storage, and ProRAW camera system. Experience the ultimate in mobile technology.',
                'features' => "• A17 Pro chip with 6-core GPU\n• 48MP Main camera with 5x Telephoto\n• 6.7-inch Super Retina XDR display\n• All-day battery life\n• iOS 17 with new features",
                'specifications' => "Display: 6.7-inch Super Retina XDR\nProcessor: A17 Pro chip\nStorage: 256GB\nCamera: 48MP Main, 12MP Ultra Wide, 12MP Telephoto\nBattery: Up to 29 hours video playback\nOS: iOS 17",
                'price' => 129999.00,
                'selling_price' => 119999.00,
                'discount_tag' => '8% OFF',
                'discount_color' => '#FF4444',
                'quantity' => 25,
                'stock_status' => 'in_stock',
                'is_active' => true,
                'is_featured' => true,
                'category_id' => $categories->where('title', 'Electronics')->first()?->id ?? 1,
                'shop_id' => $shops->where('name', 'TechGear Pro')->first()?->id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'MacBook Air M2',
                'description' => 'Supercharged by M2 chip. 13.6-inch Liquid Retina display. Up to 18 hours of battery life. Perfect for work and creativity.',
                'features' => "• M2 chip with 8-core CPU and 10-core GPU\n• 13.6-inch Liquid Retina display\n• 1080p FaceTime HD camera\n• Four-speaker sound system\n• Up to 18 hours battery life",
                'specifications' => "Display: 13.6-inch Liquid Retina\nProcessor: Apple M2 chip\nMemory: 8GB unified memory\nStorage: 256GB SSD\nPorts: 2x Thunderbolt, MagSafe 3\nWeight: 1.24 kg",
                'price' => 114900.00,
                'selling_price' => 109900.00,
                'discount_tag' => '4% OFF',
                'discount_color' => '#007BFF',
                'quantity' => 15,
                'stock_status' => 'in_stock',
                'is_active' => true,
                'is_featured' => true,
                'category_id' => $categories->where('title', 'Electronics')->first()?->id ?? 1,
                'shop_id' => $shops->where('name', 'TechGear Pro')->first()?->id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Sony WH-1000XM5 Headphones',
                'description' => 'Industry-leading noise canceling with Dual Noise Sensor technology. Up to 30-hour battery life with quick charge.',
                'features' => "• Industry-leading noise canceling\n• 30mm driver units\n• Up to 30 hours battery life\n• Quick charge (3 min = 3 hours)\n• Multipoint connection",
                'specifications' => "Driver: 30mm\nFrequency Response: 4Hz-40kHz\nBattery Life: 30 hours (ANC ON)\nCharging: USB-C\nWeight: 250g\nConnectivity: Bluetooth 5.2",
                'price' => 29990.00,
                'selling_price' => 24990.00,
                'discount_tag' => '17% OFF',
                'discount_color' => '#28A745',
                'quantity' => 40,
                'stock_status' => 'in_stock',
                'is_active' => true,
                'is_featured' => false,
                'category_id' => $categories->where('title', 'Electronics')->first()?->id ?? 1,
                'shop_id' => $shops->where('name', 'TechGear Pro')->first()?->id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Fashion Forward Products (Shop 2)
            [
                'title' => 'Premium Cotton T-Shirt',
                'description' => 'Ultra-soft 100% organic cotton t-shirt with modern fit. Perfect for casual wear and layering.',
                'features' => "• 100% organic cotton\n• Pre-shrunk fabric\n• Reinforced seams\n• Tagless design\n• Available in multiple colors",
                'specifications' => "Material: 100% Organic Cotton\nFit: Modern Regular Fit\nSizes: XS to XXL\nCare: Machine washable\nOrigin: Made in India\nGSM: 180",
                'price' => 1299.00,
                'selling_price' => 999.00,
                'discount_tag' => '23% OFF',
                'discount_color' => '#FF6B35',
                'quantity' => 100,
                'stock_status' => 'in_stock',
                'is_active' => true,
                'is_featured' => true,
                'category_id' => $categories->where('title', 'Fashion')->first()?->id ?? 2,
                'shop_id' => $shops->where('name', 'Fashion Forward')->first()?->id ?? 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Designer Denim Jeans',
                'description' => 'Premium stretch denim with contemporary styling. Comfortable fit with excellent durability.',
                'features' => "• Premium stretch denim\n• 5-pocket styling\n• Button fly closure\n• Contrast stitching\n• Fade-resistant wash",
                'specifications' => "Material: 98% Cotton, 2% Elastane\nFit: Slim Fit\nRise: Mid Rise\nSizes: 28-38 waist\nLength: 32-34 inseam\nWash: Dark Blue",
                'price' => 3999.00,
                'selling_price' => 2999.00,
                'discount_tag' => '25% OFF',
                'discount_color' => '#DC3545',
                'quantity' => 60,
                'stock_status' => 'in_stock',
                'is_active' => true,
                'is_featured' => true,
                'category_id' => $categories->where('title', 'Fashion')->first()?->id ?? 2,
                'shop_id' => $shops->where('name', 'Fashion Forward')->first()?->id ?? 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Leather Crossbody Bag',
                'description' => 'Handcrafted genuine leather crossbody bag with adjustable strap. Perfect for daily use and travel.',
                'features' => "• Genuine leather construction\n• Adjustable shoulder strap\n• Multiple compartments\n• Secure zip closure\n• Compact yet spacious",
                'specifications' => "Material: Genuine Leather\nDimensions: 25cm x 18cm x 8cm\nStrap: Adjustable 120-140cm\nCompartments: 3 main, 2 inner pockets\nClosure: YKK zipper\nColor: Brown",
                'price' => 4999.00,
                'selling_price' => 3999.00,
                'discount_tag' => '20% OFF',
                'discount_color' => '#6F42C1',
                'quantity' => 30,
                'stock_status' => 'in_stock',
                'is_active' => true,
                'is_featured' => false,
                'category_id' => $categories->where('title', 'Fashion')->first()?->id ?? 2,
                'shop_id' => $shops->where('name', 'Fashion Forward')->first()?->id ?? 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Home & Garden Paradise Products (Shop 3)
            [
                'title' => 'Smart LED Ceiling Light',
                'description' => 'WiFi-enabled smart ceiling light with adjustable brightness and color temperature. Control via smartphone app.',
                'features' => "• WiFi connectivity\n• Adjustable brightness (1-100%)\n• Color temperature control\n• Voice control compatible\n• Energy efficient LED",
                'specifications' => "Power: 24W LED\nLumens: 2400lm\nColor Temperature: 2700K-6500K\nConnectivity: WiFi 2.4GHz\nDimensions: 40cm diameter\nLifespan: 25,000 hours",
                'price' => 5999.00,
                'selling_price' => 4499.00,
                'discount_tag' => '25% OFF',
                'discount_color' => '#FFC107',
                'quantity' => 35,
                'stock_status' => 'in_stock',
                'is_active' => true,
                'is_featured' => true,
                'category_id' => $categories->where('title', 'Home & Garden')->first()?->id ?? 3,
                'shop_id' => $shops->where('name', 'Home & Garden Paradise')->first()?->id ?? 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Bamboo Kitchen Utensil Set',
                'description' => 'Eco-friendly bamboo kitchen utensil set with 6 essential tools. Sustainable and durable for everyday cooking.',
                'features' => "• 100% natural bamboo\n• 6-piece utensil set\n• Heat resistant up to 200°C\n• Lightweight and durable\n• Easy to clean",
                'specifications' => "Material: 100% Bamboo\nSet Includes: Spatula, Spoon, Fork, Tongs, Ladle, Slotted Spoon\nLength: 30cm each\nFinish: Natural oil finish\nCare: Hand wash only",
                'price' => 1999.00,
                'selling_price' => 1499.00,
                'discount_tag' => '25% OFF',
                'discount_color' => '#20C997',
                'quantity' => 80,
                'stock_status' => 'in_stock',
                'is_active' => true,
                'is_featured' => false,
                'category_id' => $categories->where('title', 'Home & Garden')->first()?->id ?? 3,
                'shop_id' => $shops->where('name', 'Home & Garden Paradise')->first()?->id ?? 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Indoor Plant Care Kit',
                'description' => 'Complete plant care kit with organic fertilizer, spray bottle, and care guide. Perfect for indoor gardening enthusiasts.',
                'features' => "• Organic liquid fertilizer 250ml\n• Fine mist spray bottle\n• Plant care guide book\n• Measuring cup included\n• Suitable for all indoor plants",
                'specifications' => "Fertilizer: 250ml organic liquid\nSpray Bottle: 500ml capacity\nGuide: 50-page care manual\nMeasuring Cup: 50ml graduated\nPackaging: Eco-friendly box",
                'price' => 899.00,
                'selling_price' => 699.00,
                'discount_tag' => '22% OFF',
                'discount_color' => '#17A2B8',
                'quantity' => 50,
                'stock_status' => 'in_stock',
                'is_active' => true,
                'is_featured' => true,
                'category_id' => $categories->where('title', 'Home & Garden')->first()?->id ?? 3,
                'shop_id' => $shops->where('name', 'Home & Garden Paradise')->first()?->id ?? 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Additional Products for variety
            [
                'title' => 'Wireless Gaming Mouse',
                'description' => 'High-precision wireless gaming mouse with RGB lighting and programmable buttons. Perfect for gaming and productivity.',
                'features' => "• 16,000 DPI sensor\n• 11 programmable buttons\n• RGB lighting with effects\n• 70-hour battery life\n• Wireless 2.4GHz connection",
                'specifications' => "DPI: 100-16,000 adjustable\nButtons: 11 programmable\nBattery: 70 hours continuous use\nConnectivity: 2.4GHz wireless\nWeight: 120g\nCompatibility: Windows, Mac",
                'price' => 7999.00,
                'selling_price' => 5999.00,
                'discount_tag' => '25% OFF',
                'discount_color' => '#E83E8C',
                'quantity' => 45,
                'stock_status' => 'in_stock',
                'is_active' => true,
                'is_featured' => false,
                'category_id' => $categories->where('title', 'Electronics')->first()?->id ?? 1,
                'shop_id' => $shops->where('name', 'TechGear Pro')->first()?->id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Vintage Leather Jacket',
                'description' => 'Classic vintage-style leather jacket with premium finish. Timeless design that never goes out of style.',
                'features' => "• Premium genuine leather\n• Vintage distressed finish\n• YKK zippers throughout\n• Quilted lining\n• Multiple pockets",
                'specifications' => "Material: 100% Genuine Leather\nLining: Quilted polyester\nSizes: S to XXL\nColor: Black, Brown\nCare: Professional leather cleaning\nOrigin: Handcrafted",
                'price' => 12999.00,
                'selling_price' => 9999.00,
                'discount_tag' => '23% OFF',
                'discount_color' => '#6C757D',
                'quantity' => 20,
                'stock_status' => 'in_stock',
                'is_active' => true,
                'is_featured' => true,
                'category_id' => $categories->where('title', 'Fashion')->first()?->id ?? 2,
                'shop_id' => $shops->where('name', 'Fashion Forward')->first()?->id ?? 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Successfully seeded ' . count($products) . ' products!');
    }
}
