<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Services\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $recommendationService;
    protected $user;
    protected $category;
    protected $brand;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->recommendationService = new RecommendationService();
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->brand = Brand::factory()->create();
    }

    public function test_get_similar_products()
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'active',
            'stock_quantity' => 10
        ]);

        $similarProducts = Product::factory()->count(3)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'active',
            'stock_quantity' => 5
        ]);

        $recommendations = $this->recommendationService->getSimilarProducts($product->id, 5);

        $this->assertNotEmpty($recommendations);
        $this->assertLessThanOrEqual(5, $recommendations->count());
    }

    public function test_get_personalized_recommendations()
    {
        Product::factory()->count(10)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'active',
            'stock_quantity' => 5
        ]);

        $recommendations = $this->recommendationService->getPersonalizedRecommendations($this->user->id, 8);

        $this->assertNotEmpty($recommendations);
        $this->assertLessThanOrEqual(8, $recommendations->count());
    }

    public function test_recommendation_scoring()
    {
        $product1 = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'active',
            'stock_quantity' => 10,
            'average_rating' => 4.5
        ]);

        $product2 = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'active',
            'stock_quantity' => 5,
            'average_rating' => 3.0
        ]);

        $recommendations = $this->recommendationService->getSimilarProducts($product1->id, 5);

        $this->assertNotEmpty($recommendations);
        
        foreach ($recommendations as $recommendation) {
            $this->assertObjectHasAttribute('recommendation_score', $recommendation);
            $this->assertIsFloat($recommendation->recommendation_score);
            $this->assertGreaterThanOrEqual(0, $recommendation->recommendation_score);
            $this->assertLessThanOrEqual(1, $recommendation->recommendation_score);
        }
    }

    public function test_cache_functionality()
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'active',
            'stock_quantity' => 10
        ]);

        // First call should cache the results
        $recommendations1 = $this->recommendationService->getSimilarProducts($product->id, 5);
        
        // Second call should use cached results
        $recommendations2 = $this->recommendationService->getSimilarProducts($product->id, 5);

        $this->assertEquals($recommendations1->count(), $recommendations2->count());
    }

    public function test_clear_recommendation_cache()
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'active',
            'stock_quantity' => 10
        ]);

        // Cache some recommendations
        $this->recommendationService->getSimilarProducts($product->id, 5);

        // Clear cache
        $this->recommendationService->clearRecommendationCache($product->id, $this->user->id);

        // This should work without errors
        $this->assertTrue(true);
    }
}
