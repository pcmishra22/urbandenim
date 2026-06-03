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
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('sku')->nullable()->unique();
            $table->string('fit_type')->nullable();
            $table->string('color')->nullable();
            $table->string('stretch')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeignKeyConstraints();
            $table->dropColumn(['category_id', 'sku', 'fit_type', 'color', 'stretch']);
        });
    }
};
