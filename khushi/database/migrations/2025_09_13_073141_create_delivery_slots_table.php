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
        Schema::create('delivery_slots', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Morning, Afternoon, Evening
            $table->enum('day_type', ['weekday', 'weekend', 'specific'])->default('weekday');
            $table->string('specific_day')->nullable(); // monday, tuesday, etc.
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_orders')->default(50);
            $table->integer('current_orders')->default(0);
            $table->decimal('additional_charge', 8, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->json('available_zones')->nullable(); // Array of shipping zone IDs
            $table->timestamps();
            
            $table->index(['day_type', 'is_available']);
            $table->index(['start_time', 'end_time']);
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_slots');
    }
};
