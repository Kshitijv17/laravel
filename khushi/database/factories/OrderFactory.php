<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => fake()->unique()->regexify('ORD-[0-9]{6}'),
            'total_amount' => fake()->randomFloat(2, 50, 500),
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            'notes' => fake()->optional()->paragraph(),
            'ordered_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
