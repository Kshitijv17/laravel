<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->string('ip_address');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('results_count')->default(0);
            $table->json('filters')->nullable();
            $table->timestamps();

            $table->index(['query', 'created_at']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('search_logs');
    }
};
