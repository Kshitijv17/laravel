<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'phone' => fake()->phoneNumber(),
            'avatar' => fake()->imageUrl(200, 200, 'people'),
            'status' => fake()->randomElement(['active', 'inactive']),
            'permissions' => fake()->randomElements([
                'manage_products', 'view_orders', 'manage_inventory', 
                'view_analytics', 'manage_users', 'manage_coupons'
            ], fake()->numberBetween(2, 4)),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the admin is a super admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'permissions' => ['manage_all', 'view_analytics', 'manage_users', 'manage_products'],
        ]);
    }
}
