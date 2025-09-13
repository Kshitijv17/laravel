<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('variant_type'); // color, size, material, etc.
            $table->string('variant_value'); // red, XL, cotton, etc.
            $table->string('sku')->unique()->nullable();
            $table->decimal('price_adjustment', 8, 2)->default(0); // +/- from base price
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('attributes')->nullable(); // JSON for additional attributes
            $table->boolean('is_default')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            $table->index(['product_id', 'variant_type']);
            $table->unique(['product_id', 'variant_type', 'variant_value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
