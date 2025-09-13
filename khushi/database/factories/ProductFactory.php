<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->randomElement([
                'iPhone 15 Pro', 'Samsung Galaxy S24', 'MacBook Pro', 'Dell XPS 13',
                'Sony WH-1000XM5', 'AirPods Pro', 'iPad Air', 'Surface Pro',
                'Gaming Chair', 'Wireless Mouse', 'Mechanical Keyboard', 'Monitor 4K',
                'Running Shoes', 'Denim Jacket', 'Cotton T-Shirt', 'Leather Wallet',
                'Coffee Maker', 'Blender', 'Air Fryer', 'Vacuum Cleaner'
            ]),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraph(),
            'sku' => fake()->unique()->regexify('[A-Z]{3}[0-9]{6}'),
            'price' => fake()->randomFloat(2, 10, 2000),
            'discount_price' => fake()->optional(0.3)->randomFloat(2, 5, 1500),
            'stock' => fake()->numberBetween(0, 200),
            'image' => fake()->imageUrl(500, 500, 'business'),
            'status' => fake()->boolean(85),
            'is_featured' => fake()->boolean(20),
        ];
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }

    /**
     * Indicate that the product is on sale.
     */
    public function onSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'sale_price' => fake()->randomFloat(2, 5, $attributes['price'] * 0.8),
        ]);
    }
}
