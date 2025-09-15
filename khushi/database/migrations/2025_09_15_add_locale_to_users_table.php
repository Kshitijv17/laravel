<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'locale')) {
                $table->string('locale', 5)->default('en')->after('email');
            }
            if (!Schema::hasColumn('users', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('locale');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'locale')) {
                $table->dropColumn('locale');
            }
            if (Schema::hasColumn('users', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
