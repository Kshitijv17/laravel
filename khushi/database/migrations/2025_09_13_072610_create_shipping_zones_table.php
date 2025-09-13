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
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('countries'); // Array of country codes
            $table->json('states')->nullable(); // Array of state codes
            $table->json('cities')->nullable(); // Array of city names
            $table->json('zip_codes')->nullable(); // Array of zip code ranges
            $table->decimal('base_rate', 8, 2)->default(0);
            $table->decimal('per_kg_rate', 8, 2)->default(0);
            $table->decimal('free_shipping_threshold', 10, 2)->nullable();
            $table->integer('delivery_days_min')->default(1);
            $table->integer('delivery_days_max')->default(7);
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_zones');
    }
};
