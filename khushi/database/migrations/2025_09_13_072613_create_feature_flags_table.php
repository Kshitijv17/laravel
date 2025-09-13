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
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('key')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->json('conditions')->nullable(); // User segments, dates, etc.
            $table->json('rollout_percentage')->nullable(); // Gradual rollout
            $table->string('environment')->default('production'); // dev, staging, production
            $table->string('category')->nullable(); // ui, payment, shipping, etc.
            $table->datetime('starts_at')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['key', 'is_enabled']);
            $table->index(['environment', 'is_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_flags');
    }
};
