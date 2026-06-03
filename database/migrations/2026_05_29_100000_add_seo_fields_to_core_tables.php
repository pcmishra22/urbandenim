<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add SEO fields.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('name');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('canonical_url')->nullable()->after('meta_description');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('name');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('canonical_url')->nullable()->after('meta_description');
        });

        Schema::table('cms_pages', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('title');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('canonical_url')->nullable()->after('meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('products', function ($table) { $table->dropColumn(['meta_title', 'meta_description', 'canonical_url']); });
        Schema::table('categories', function ($table) { $table->dropColumn(['meta_title', 'meta_description', 'canonical_url']); });
        Schema::table('cms_pages', function ($table) { $table->dropColumn(['meta_title', 'meta_description', 'canonical_url']); });
    }
};