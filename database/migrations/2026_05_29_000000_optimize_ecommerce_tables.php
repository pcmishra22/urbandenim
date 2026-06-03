<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('parent_id');
            $table->index('is_active');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('slug');
            $table->index('is_active');
            $table->index('is_featured');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('sku');
            $table->index('color');
            $table->index('waist_size');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};