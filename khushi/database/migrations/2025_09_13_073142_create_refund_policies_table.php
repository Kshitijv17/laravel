<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('refund_policies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('days_limit')->default(7);
            $table->json('applicable_categories')->nullable(); // Category IDs
            $table->json('excluded_products')->nullable(); // Product IDs
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->decimal('maximum_refund_amount', 10, 2)->nullable();
            $table->boolean('requires_original_packaging')->default(false);
            $table->boolean('allows_partial_refund')->default(true);
            $table->enum('refund_method', ['original_payment', 'store_credit', 'bank_transfer'])->default('original_payment');
            $table->text('terms_conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['is_active']);
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_policies');
    }
};
