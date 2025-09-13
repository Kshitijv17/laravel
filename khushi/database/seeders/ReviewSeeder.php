<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        // Create reviews for random products
        foreach ($products->take(50) as $product) {
            $reviewCount = fake()->numberBetween(0, 8);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                $user = $users->random();
                
                // Avoid duplicate reviews from same user for same product
                if (!Review::where('user_id', $user->id)->where('product_id', $product->id)->exists()) {
                    Review::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'rating' => fake()->numberBetween(3, 5), // Mostly positive reviews
                        'comment' => fake()->paragraph(2),
                        'is_approved' => true,
                    ]);
                }
            }
        }

        // Create some specific good reviews for featured products
        $featuredProducts = Product::where('is_featured', true)->get();
        
        foreach ($featuredProducts->take(10) as $product) {
            $goodReviews = [
                'Excellent product! Highly recommended.',
                'Great quality and fast shipping. Very satisfied!',
                'Perfect! Exactly what I was looking for.',
                'Amazing value for money. Will buy again.',
                'Outstanding quality and great customer service.',
            ];

            foreach ($goodReviews as $comment) {
                $user = $users->random();
                
                if (!Review::where('user_id', $user->id)->where('product_id', $product->id)->exists()) {
                    Review::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'rating' => 5,
                        'comment' => $comment,
                        'is_approved' => true,
                    ]);
                    break; // Only one review per product
                }
            }
        }
    }
}
