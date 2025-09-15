<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add SEO fields to products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('description');
                $table->text('meta_description')->nullable()->after('meta_title');
                $table->text('meta_keywords')->nullable()->after('meta_description');
                $table->string('canonical_url')->nullable()->after('meta_keywords');
                $table->boolean('noindex')->default(false)->after('canonical_url');
                $table->boolean('nofollow')->default(false)->after('noindex');
            }
        });

        // Add SEO fields to categories table
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('description');
                $table->text('meta_description')->nullable()->after('meta_title');
                $table->text('meta_keywords')->nullable()->after('meta_description');
                $table->string('canonical_url')->nullable()->after('meta_keywords');
                $table->boolean('noindex')->default(false)->after('canonical_url');
                $table->boolean('nofollow')->default(false)->after('noindex');
            }
        });

        // Add SEO fields to blog_posts table if it exists
        if (Schema::hasTable('blog_posts')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->string('meta_title')->nullable()->after('excerpt');
                $table->text('meta_description')->nullable()->after('meta_title');
                $table->text('meta_keywords')->nullable()->after('meta_description');
                $table->string('canonical_url')->nullable()->after('meta_keywords');
                $table->boolean('noindex')->default(false)->after('canonical_url');
                $table->boolean('nofollow')->default(false)->after('noindex');
                $table->string('focus_keyword')->nullable()->after('nofollow');
            });
        }

        // Add alt_text to product_images table if it doesn't exist
        if (Schema::hasTable('product_images')) {
            Schema::table('product_images', function (Blueprint $table) {
                if (!Schema::hasColumn('product_images', 'alt_text')) {
                    $table->string('alt_text')->nullable()->after('image_path');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description', 
                'meta_keywords',
                'canonical_url',
                'noindex',
                'nofollow'
            ]);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'meta_keywords', 
                'canonical_url',
                'noindex',
                'nofollow'
            ]);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'meta_keywords',
                'canonical_url', 
                'noindex',
                'nofollow',
                'focus_keyword'
            ]);
        });

        if (Schema::hasTable('product_images') && Schema::hasColumn('product_images', 'alt_text')) {
            Schema::table('product_images', function (Blueprint $table) {
                $table->dropColumn('alt_text');
            });
        }
    }
};
