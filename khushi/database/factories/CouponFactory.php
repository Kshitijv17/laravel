<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $discountType = fake()->randomElement(['percent', 'fixed']);
        $discountValue = $discountType === 'percent' 
            ? fake()->numberBetween(5, 50) 
            : fake()->randomFloat(2, 5, 100);

        return [
            'code' => fake()->unique()->regexify('[A-Z]{4}[0-9]{2}'),
            'type' => $discountType,
            'value' => $discountValue,
            'min_cart_value' => fake()->randomFloat(2, 10, 200),
            'usage_limit' => fake()->numberBetween(10, 1000),
            'is_active' => fake()->boolean(80),
            'start_date' => fake()->date(),
            'end_date' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
        ];
    }
}
