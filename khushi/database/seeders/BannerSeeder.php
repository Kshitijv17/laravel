<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Summer Sale - Up to 50% Off',
                'description' => 'Don\'t miss our biggest summer sale! Get amazing discounts on all categories.',
                'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=400&fit=crop',
                'link' => '/products',
                'position' => 'top',
                'is_active' => true,
            ],
            [
                'title' => 'New Electronics Collection',
                'description' => 'Discover the latest gadgets and electronics at unbeatable prices.',
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=800&h=400&fit=crop',
                'link' => '/categories/electronics',
                'position' => 'middle',
                'is_active' => true,
            ],
            [
                'title' => 'Fashion Week Special',
                'description' => 'Trendy outfits and accessories for the fashion-forward you.',
                'image' => 'https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=800&h=400&fit=crop',
                'link' => '/categories/fashion',
                'position' => 'middle',
                'is_active' => true,
            ],
            [
                'title' => 'Home & Garden Essentials',
                'description' => 'Transform your living space with our home and garden collection.',
                'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&h=400&fit=crop',
                'link' => '/categories/home-garden',
                'position' => 'bottom',
                'is_active' => true,
            ],
        ];

        foreach ($banners as $bannerData) {
            Banner::create($bannerData);
        }

        // Create additional random banners
        Banner::factory(6)->create();
    }
}
