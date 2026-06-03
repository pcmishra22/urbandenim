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
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('short_description')->nullable()->after('slug');
            $table->decimal('sale_price', 10, 2)->nullable()->after('price');
            $table->string('gender')->nullable()->after('sale_price');
            $table->string('age_group')->nullable()->after('gender');
            $table->string('color_family')->nullable()->after('age_group');
            $table->boolean('is_featured')->default(false)->after('color_family');
            $table->boolean('is_active')->default(true)->after('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['slug', 'short_description', 'sale_price', 'gender', 'age_group', 'color_family', 'is_featured', 'is_active']);
        });
    }
};
