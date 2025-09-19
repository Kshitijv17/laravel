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
            // Rename name to title
            $table->renameColumn('name', 'title');

            // Update description to text for rich content
            $table->text('description')->change();

            // Add new fields
            $table->text('features')->nullable()->after('description');
            $table->text('specifications')->nullable()->after('features');
            $table->string('discount_tag')->nullable()->after('specifications');
            $table->string('discount_color', 7)->default('#FF0000')->after('discount_tag'); // Hex color code
            $table->decimal('selling_price', 10, 2)->nullable()->after('price');
            $table->integer('quantity')->default(0)->after('selling_price');
            $table->enum('stock_status', ['in_stock', 'out_of_stock'])->default('in_stock')->after('quantity');
            $table->boolean('is_active')->default(true)->after('stock_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove new fields
            $table->dropColumn([
                'features',
                'specifications',
                'discount_tag',
                'discount_color',
                'selling_price',
                'quantity',
                'stock_status',
                'is_active'
            ]);

            // Revert description back to nullable string
            $table->string('description')->nullable()->change();

            // Rename title back to name
            $table->renameColumn('title', 'name');
        });
    }
};
