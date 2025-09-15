<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->category = Category::factory()->create();
        $this->brand = Brand::factory()->create();
    }

    public function test_products_can_be_listed()
    {
        Product::factory()->count(5)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'active'
        ]);

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    public function test_product_can_be_viewed()
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'active'
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertStatus(200);
        $response->assertViewHas('product');
        $response->assertSee($product->name);
    }

    public function test_admin_can_create_product()
    {
        $admin = Admin::factory()->create();

        $productData = [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'short_description' => $this->faker->sentence,
            'sku' => $this->faker->unique()->bothify('SKU-####'),
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'original_price' => 100.00,
            'selling_price' => 80.00,
            'stock_quantity' => 50,
            'status' => 'active'
        ];

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), $productData);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'name' => $productData['name'],
            'sku' => $productData['sku']
        ]);
    }

    public function test_admin_can_update_product()
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id
        ]);

        $updateData = [
            'name' => 'Updated Product Name',
            'selling_price' => 90.00
        ];

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.products.update', $product), array_merge($product->toArray(), $updateData));

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'selling_price' => 90.00
        ]);
    }

    public function test_admin_can_delete_product()
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_products_can_be_searched()
    {
        $product = Product::factory()->create([
            'name' => 'Searchable Product',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'active'
        ]);

        $response = $this->get(route('products.index', ['search' => 'Searchable']));

        $response->assertStatus(200);
        $response->assertSee('Searchable Product');
    }

    public function test_products_can_be_filtered_by_category()
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'active'
        ]);

        $response = $this->get(route('products.index', ['category' => $this->category->id]));

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    public function test_products_can_be_filtered_by_price_range()
    {
        Product::factory()->create([
            'selling_price' => 50.00,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'active'
        ]);

        $response = $this->get(route('products.index', [
            'min_price' => 40,
            'max_price' => 60
        ]));

        $response->assertStatus(200);
    }

    public function test_inactive_products_are_not_visible_to_users()
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'inactive'
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertStatus(404);
    }

    public function test_out_of_stock_products_show_correct_status()
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'stock_quantity' => 0,
            'status' => 'active'
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Out of Stock');
    }
}
