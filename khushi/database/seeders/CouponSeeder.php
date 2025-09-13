<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'type' => 'percent',
                'value' => 10,
                'min_cart_value' => 50.00,
                'usage_limit' => 1000,
                'is_active' => true,
                'start_date' => now()->subDays(30)->format('Y-m-d'),
                'end_date' => now()->addDays(90)->format('Y-m-d'),
            ],
            [
                'code' => 'SUMMER25',
                'type' => 'percent',
                'value' => 25,
                'min_cart_value' => 100.00,
                'usage_limit' => 500,
                'is_active' => true,
                'start_date' => now()->subDays(15)->format('Y-m-d'),
                'end_date' => now()->addDays(45)->format('Y-m-d'),
            ],
            [
                'code' => 'SAVE20',
                'type' => 'fixed',
                'value' => 20.00,
                'min_cart_value' => 150.00,
                'usage_limit' => 200,
                'is_active' => true,
                'start_date' => now()->subDays(7)->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
            ],
            [
                'code' => 'FREESHIP',
                'type' => 'fixed',
                'value' => 10.00,
                'min_cart_value' => 75.00,
                'usage_limit' => 1000,
                'is_active' => true,
                'start_date' => now()->subDays(60)->format('Y-m-d'),
                'end_date' => now()->addDays(120)->format('Y-m-d'),
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::create($couponData);
        }

        // Create additional random coupons
        Coupon::factory(10)->create();
    }
}
