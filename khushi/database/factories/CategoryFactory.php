<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Electronics', 'Fashion', 'Home & Garden', 'Sports & Outdoors',
                'Health & Beauty', 'Books & Media', 'Toys & Games', 'Automotive'
            ]),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraph(),
            'image' => fake()->imageUrl(400, 300, 'business'),
            'status' => fake()->boolean(85),
        ];
    }
}
