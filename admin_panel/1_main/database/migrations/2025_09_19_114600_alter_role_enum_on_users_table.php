<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure the role column accepts all required values
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('guest','customer','admin','superadmin') NOT NULL DEFAULT 'customer'");
    }

    public function down(): void
    {
        // Revert to original enum if needed
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('customer','admin') NOT NULL DEFAULT 'customer'");
    }
};
