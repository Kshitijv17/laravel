<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Clothing specific fields
            $table->json('sizes')->nullable()->after('description');
            $table->json('colors')->nullable()->after('sizes');
            $table->string('fabric')->nullable()->after('colors');
            $table->string('pattern')->nullable()->after('fabric');
            $table->string('fit_type')->nullable()->after('pattern');
            $table->string('sleeve_type')->nullable()->after('fit_type');
            $table->string('neck_type')->nullable()->after('sleeve_type');
            $table->string('occasion')->nullable()->after('neck_type');
            $table->string('care_instructions')->nullable()->after('occasion');
            $table->string('country_of_origin')->default('India')->after('care_instructions');
            $table->json('meta_data')->nullable()->after('country_of_origin');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sizes', 'colors', 'fabric', 'pattern', 'fit_type',
                'sleeve_type', 'neck_type', 'occasion', 'care_instructions',
                'country_of_origin', 'meta_data'
            ]);
        });
    }
};
