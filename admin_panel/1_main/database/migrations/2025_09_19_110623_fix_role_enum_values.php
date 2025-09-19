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
        // Drop the existing role column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        // Recreate the role column with correct enum values
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['guest', 'customer', 'admin', 'superadmin'])->default('customer')->after('email');
        });
    }

    public function down(): void
    {
        // Drop and recreate with original enum (if needed)
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->enum('role', ['guest', 'customer', 'admin', 'superadmin'])->default('customer')->after('email');
        });
    }
};
