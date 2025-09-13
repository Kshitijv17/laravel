<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        // Create orders for each user
        foreach ($users->take(20) as $user) {
            $orderCount = fake()->numberBetween(1, 5);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $order = Order::factory()->create([
                    'user_id' => $user->id,
                ]);

                // Add random products to order
                $orderProducts = $products->random(fake()->numberBetween(1, 4));
                
                foreach ($orderProducts as $product) {
                    $quantity = fake()->numberBetween(1, 3);
                    $price = $product->sale_price ?? $product->price;
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $price * $quantity,
                    ]);
                }

                // Update order total
                $orderItems = $order->items;
                $totalAmount = $orderItems->sum('total');

                $order->update([
                    'total_amount' => $totalAmount,
                ]);
            }
        }
    }
}
