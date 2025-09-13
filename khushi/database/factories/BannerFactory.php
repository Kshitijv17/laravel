<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(8),
            'image' => fake()->imageUrl(800, 400, 'business'),
            'link' => fake()->url(),
            'position' => fake()->randomElement(['top', 'middle', 'bottom']),
            'is_active' => fake()->boolean(80),
        ];
    }
}
