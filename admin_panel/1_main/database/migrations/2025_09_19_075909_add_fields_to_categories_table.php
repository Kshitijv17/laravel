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
            // Only rename if source exists and target does not
            if (Schema::hasColumn('categories', 'name') && ! Schema::hasColumn('categories', 'title')) {
                $table->renameColumn('name', 'title');
            }
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
            if (Schema::hasColumn('categories', 'title') && ! Schema::hasColumn('categories', 'name')) {
                $table->renameColumn('title', 'name');
            }
        });
    }
};
