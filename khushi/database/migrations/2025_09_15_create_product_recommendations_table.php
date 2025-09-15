<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('recommended_product_id')->constrained('products')->onDelete('cascade');
            $table->enum('recommendation_type', ['collaborative', 'content_based', 'trending', 'cross_sell', 'up_sell', 'similar']);
            $table->decimal('score', 3, 2)->default(0.0);
            $table->string('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'recommendation_type']);
            $table->index(['product_id', 'recommendation_type']);
            $table->index(['score', 'recommendation_type']);
            $table->unique(['user_id', 'product_id', 'recommended_product_id', 'recommendation_type'], 'unique_recommendation');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_recommendations');
    }
};
