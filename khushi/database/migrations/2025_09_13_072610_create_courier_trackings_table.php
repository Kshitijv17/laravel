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
        Schema::create('courier_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('tracking_number')->unique();
            $table->string('courier_company'); // FedEx, UPS, DHL, etc.
            $table->string('courier_service')->nullable(); // Express, Standard, etc.
            $table->enum('status', ['pending', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned'])->default('pending');
            $table->text('current_location')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('expected_delivery')->nullable();
            $table->json('tracking_history')->nullable(); // Array of tracking events
            $table->text('delivery_notes')->nullable();
            $table->string('recipient_name')->nullable();
            $table->text('delivery_address')->nullable();
            $table->timestamps();
            
            $table->index(['tracking_number', 'status']);
            $table->index(['order_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_trackings');
    }
};
