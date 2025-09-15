<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to modify the enum column
        DB::statement("ALTER TABLE banners MODIFY COLUMN position ENUM('hero', 'sidebar', 'footer', 'popup') DEFAULT 'hero'");
        
        // Also need to update the column names to match our controller
        Schema::table('banners', function (Blueprint $table) {
            // Rename 'link' to 'link_url' if it exists
            if (Schema::hasColumn('banners', 'link')) {
                $table->renameColumn('link', 'link_url');
            }
            
            // Rename 'is_active' to 'status' if it exists
            if (Schema::hasColumn('banners', 'is_active')) {
                $table->renameColumn('is_active', 'status');
            }
            
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('banners', 'button_text')) {
                $table->string('button_text')->nullable()->after('link_url');
            }
            
            if (!Schema::hasColumn('banners', 'start_date')) {
                $table->date('start_date')->nullable()->after('button_text');
            }
            
            if (!Schema::hasColumn('banners', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum values
        DB::statement("ALTER TABLE banners MODIFY COLUMN position ENUM('top', 'middle', 'bottom') DEFAULT 'top'");
        
        Schema::table('banners', function (Blueprint $table) {
            // Revert column names
            if (Schema::hasColumn('banners', 'link_url')) {
                $table->renameColumn('link_url', 'link');
            }
            
            if (Schema::hasColumn('banners', 'status')) {
                $table->renameColumn('status', 'is_active');
            }
            
            // Drop added columns
            if (Schema::hasColumn('banners', 'button_text')) {
                $table->dropColumn('button_text');
            }
            
            if (Schema::hasColumn('banners', 'start_date')) {
                $table->dropColumn('start_date');
            }
            
            if (Schema::hasColumn('banners', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });
    }
};
