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
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('name', 'title');
            $table->string('image')->nullable()->after('title');
            $table->enum('active', ['active', 'inactive'])->default('active')->after('image');
            $table->enum('show_on_home', ['show', 'hide'])->default('show')->after('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['show_on_home', 'active', 'image']);
            $table->renameColumn('title', 'name');
        });
    }
};
