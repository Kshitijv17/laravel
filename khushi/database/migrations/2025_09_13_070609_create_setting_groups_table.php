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
        Schema::create('setting_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name');
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, boolean, image, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_groups');
    }
};
