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
        Schema::table('products', function (Blueprint $table) {
            // Only rename if the source exists and target does not
            if (Schema::hasColumn('products', 'name') && ! Schema::hasColumn('products', 'title')) {
                $table->renameColumn('name', 'title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Only revert if the current column exists and original does not
            if (Schema::hasColumn('products', 'title') && ! Schema::hasColumn('products', 'name')) {
                $table->renameColumn('title', 'name');
            }
        });
    }
};
