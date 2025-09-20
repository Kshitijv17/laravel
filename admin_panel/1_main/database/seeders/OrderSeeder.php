<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users (customers)
        $customers = User::where('role', 'customer')->limit(5)->get();
        
        // If no customers exist, create some
        if ($customers->isEmpty()) {
            $customers = collect([
                User::create([
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                    'password' => bcrypt('password'),
                    'role' => 'customer',
                    'email_verified_at' => now(),
                ]),
                User::create([
                    'name' => 'Jane Smith',
                    'email' => 'jane.smith@example.com',
                    'password' => bcrypt('password'),
                    'role' => 'customer',
                    'email_verified_at' => now(),
                ]),
                User::create([
                    'name' => 'Mike Johnson',
                    'email' => 'mike.johnson@example.com',
                    'password' => bcrypt('password'),
                    'role' => 'customer',
                    'email_verified_at' => now(),
                ]),
            ]);
        }

        // Sample products data (in case Product model doesn't exist)
        $sampleProducts = [
            ['name' => 'Laptop Computer', 'sku' => 'LAP001', 'price' => 999.99],
            ['name' => 'Wireless Mouse', 'sku' => 'MOU001', 'price' => 29.99],
            ['name' => 'Keyboard', 'sku' => 'KEY001', 'price' => 79.99],
            ['name' => 'Monitor', 'sku' => 'MON001', 'price' => 299.99],
            ['name' => 'Headphones', 'sku' => 'HEAD001', 'price' => 149.99],
            ['name' => 'Smartphone', 'sku' => 'PHONE001', 'price' => 699.99],
            ['name' => 'Tablet', 'sku' => 'TAB001', 'price' => 399.99],
            ['name' => 'Smartwatch', 'sku' => 'WATCH001', 'price' => 249.99],
        ];

        $orders = [
            [
                'user_id' => $customers->first()->id,
                'status' => Order::STATUS_DELIVERED,
                'payment_status' => Order::PAYMENT_PAID,
                'payment_method' => 'Credit Card',
                'subtotal' => 1029.98,
                'tax_amount' => 82.40,
                'shipping_amount' => 15.00,
                'discount_amount' => 50.00,
                'total_amount' => 1077.38,
                'currency' => 'USD',
                'notes' => 'Customer requested express delivery',
                'shipping_address' => [
                    'name' => 'John Doe',
                    'address_line_1' => '123 Main Street',
                    'address_line_2' => 'Apt 4B',
                    'city' => 'New York',
                    'state' => 'NY',
                    'postal_code' => '10001',
                    'country' => 'United States',
                    'phone' => '+1-555-0123'
                ],
                'billing_address' => [
                    'name' => 'John Doe',
                    'address_line_1' => '123 Main Street',
                    'address_line_2' => 'Apt 4B',
                    'city' => 'New York',
                    'state' => 'NY',
                    'postal_code' => '10001',
                    'country' => 'United States',
                    'phone' => '+1-555-0123'
                ],
                'tracking_number' => 'TRK123456789',
                'shipped_at' => now()->subDays(3),
                'delivered_at' => now()->subDays(1),
                'created_at' => now()->subDays(5),
                'items' => [
                    ['product' => $sampleProducts[0], 'quantity' => 1],
                    ['product' => $sampleProducts[1], 'quantity' => 1],
                ]
            ],
            [
                'user_id' => $customers->skip(1)->first()->id,
                'status' => Order::STATUS_SHIPPED,
                'payment_status' => Order::PAYMENT_PAID,
                'payment_method' => 'PayPal',
                'subtotal' => 449.98,
                'tax_amount' => 36.00,
                'shipping_amount' => 10.00,
                'discount_amount' => 0.00,
                'total_amount' => 495.98,
                'currency' => 'USD',
                'notes' => null,
                'shipping_address' => [
                    'name' => 'Jane Smith',
                    'address_line_1' => '456 Oak Avenue',
                    'city' => 'Los Angeles',
                    'state' => 'CA',
                    'postal_code' => '90210',
                    'country' => 'United States',
                    'phone' => '+1-555-0456'
                ],
                'billing_address' => [
                    'name' => 'Jane Smith',
                    'address_line_1' => '456 Oak Avenue',
                    'city' => 'Los Angeles',
                    'state' => 'CA',
                    'postal_code' => '90210',
                    'country' => 'United States',
                    'phone' => '+1-555-0456'
                ],
                'tracking_number' => 'TRK987654321',
                'shipped_at' => now()->subDays(1),
                'delivered_at' => null,
                'created_at' => now()->subDays(3),
                'items' => [
                    ['product' => $sampleProducts[6], 'quantity' => 1],
                    ['product' => $sampleProducts[1], 'quantity' => 2],
                ]
            ],
            [
                'user_id' => $customers->skip(2)->first()->id,
                'status' => Order::STATUS_PROCESSING,
                'payment_status' => Order::PAYMENT_PAID,
                'payment_method' => 'Credit Card',
                'subtotal' => 949.97,
                'tax_amount' => 76.00,
                'shipping_amount' => 12.00,
                'discount_amount' => 100.00,
                'total_amount' => 937.97,
                'currency' => 'USD',
                'notes' => 'Gift wrapping requested',
                'shipping_address' => [
                    'name' => 'Mike Johnson',
                    'address_line_1' => '789 Pine Street',
                    'city' => 'Chicago',
                    'state' => 'IL',
                    'postal_code' => '60601',
                    'country' => 'United States',
                    'phone' => '+1-555-0789'
                ],
                'billing_address' => [
                    'name' => 'Mike Johnson',
                    'address_line_1' => '789 Pine Street',
                    'city' => 'Chicago',
                    'state' => 'IL',
                    'postal_code' => '60601',
                    'country' => 'United States',
                    'phone' => '+1-555-0789'
                ],
                'tracking_number' => null,
                'shipped_at' => null,
                'delivered_at' => null,
                'created_at' => now()->subDays(2),
                'items' => [
                    ['product' => $sampleProducts[5], 'quantity' => 1],
                    ['product' => $sampleProducts[7], 'quantity' => 1],
                ]
            ],
            [
                'user_id' => $customers->first()->id,
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
                'payment_method' => 'Bank Transfer',
                'subtotal' => 379.98,
                'tax_amount' => 30.40,
                'shipping_amount' => 8.00,
                'discount_amount' => 0.00,
                'total_amount' => 418.38,
                'currency' => 'USD',
                'notes' => 'Customer will pay via bank transfer',
                'shipping_address' => [
                    'name' => 'John Doe',
                    'address_line_1' => '123 Main Street',
                    'address_line_2' => 'Apt 4B',
                    'city' => 'New York',
                    'state' => 'NY',
                    'postal_code' => '10001',
                    'country' => 'United States',
                    'phone' => '+1-555-0123'
                ],
                'billing_address' => [
                    'name' => 'John Doe',
                    'address_line_1' => '123 Main Street',
                    'address_line_2' => 'Apt 4B',
                    'city' => 'New York',
                    'state' => 'NY',
                    'postal_code' => '10001',
                    'country' => 'United States',
                    'phone' => '+1-555-0123'
                ],
                'tracking_number' => null,
                'shipped_at' => null,
                'delivered_at' => null,
                'created_at' => now()->subDays(1),
                'items' => [
                    ['product' => $sampleProducts[3], 'quantity' => 1],
                    ['product' => $sampleProducts[2], 'quantity' => 1],
                ]
            ],
            [
                'user_id' => $customers->skip(1)->first()->id,
                'status' => Order::STATUS_CANCELLED,
                'payment_status' => Order::PAYMENT_REFUNDED,
                'payment_method' => 'Credit Card',
                'subtotal' => 149.99,
                'tax_amount' => 12.00,
                'shipping_amount' => 5.00,
                'discount_amount' => 0.00,
                'total_amount' => 166.99,
                'currency' => 'USD',
                'notes' => 'Order cancelled by customer - full refund processed',
                'shipping_address' => [
                    'name' => 'Jane Smith',
                    'address_line_1' => '456 Oak Avenue',
                    'city' => 'Los Angeles',
                    'state' => 'CA',
                    'postal_code' => '90210',
                    'country' => 'United States',
                    'phone' => '+1-555-0456'
                ],
                'billing_address' => [
                    'name' => 'Jane Smith',
                    'address_line_1' => '456 Oak Avenue',
                    'city' => 'Los Angeles',
                    'state' => 'CA',
                    'postal_code' => '90210',
                    'country' => 'United States',
                    'phone' => '+1-555-0456'
                ],
                'tracking_number' => null,
                'shipped_at' => null,
                'delivered_at' => null,
                'created_at' => now()->subDays(4),
                'items' => [
                    ['product' => $sampleProducts[4], 'quantity' => 1],
                ]
            ],
        ];

        foreach ($orders as $orderData) {
            $items = $orderData['items'];
            unset($orderData['items']);

            $order = Order::create($orderData);

            foreach ($items as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => null, // No actual product relationship for demo
                    'product_name' => $itemData['product']['name'],
                    'product_sku' => $itemData['product']['sku'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['product']['price'],
                    'total_price' => $itemData['product']['price'] * $itemData['quantity'],
                    'product_options' => null,
                ]);
            }
        }

        $this->command->info('Created ' . count($orders) . ' sample orders with items.');
    }
}
