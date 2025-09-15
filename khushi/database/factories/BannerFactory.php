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
            'link_url' => fake()->url(),
            'button_text' => fake()->words(2, true),
            'position' => fake()->randomElement(['hero', 'sidebar', 'footer', 'popup']),
            'status' => fake()->boolean(80),
            'start_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'end_date' => fake()->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
        ];
    }
}
