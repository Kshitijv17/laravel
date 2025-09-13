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
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referral_id');
            $table->enum('reward_type', ['percentage', 'fixed_amount', 'points']);
            $table->decimal('reward_value', 10, 2);
            $table->enum('trigger_event', ['signup', 'first_purchase', 'purchase_amount']);
            $table->decimal('minimum_purchase', 10, 2)->nullable();
            $table->boolean('is_one_time')->default(true);
            $table->enum('status', ['pending', 'awarded', 'expired'])->default('pending');
            $table->timestamp('awarded_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['referral_id', 'status']);
            $table->index(['trigger_event', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_rewards');
    }
};
