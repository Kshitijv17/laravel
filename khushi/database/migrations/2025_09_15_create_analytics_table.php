<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 50)->index(); // page_view, product_view, add_to_cart, purchase, search
            $table->string('event_name', 100)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id', 100)->index();
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            $table->string('url', 500)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->json('data')->nullable(); // Additional event data
            $table->timestamp('created_at');
            
            $table->index(['event_type', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('analytics');
    }
};
