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
        Schema::create('tax_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('rate', 5, 2); // Tax rate percentage
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->decimal('min_amount', 10, 2)->nullable(); // Minimum order amount
            $table->decimal('max_amount', 10, 2)->nullable(); // Maximum order amount
            $table->boolean('is_compound')->default(false); // Compound tax
            $table->integer('priority')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            $table->index(['country', 'state', 'status']);
            $table->index(['code', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rules');
    }
};
