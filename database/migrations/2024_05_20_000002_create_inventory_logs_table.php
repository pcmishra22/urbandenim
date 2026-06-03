<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();

            // product_variants might not exist in some migration orders.
            if (Schema::hasTable('product_variants')) {
                $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
            } else {
                $table->foreignId('product_variant_id')->nullable();
            }

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('old_stock');
            $table->integer('new_stock');
            $table->integer('adjustment');
            $table->string('reason');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};