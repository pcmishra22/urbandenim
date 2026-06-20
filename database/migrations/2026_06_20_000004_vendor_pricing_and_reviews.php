<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Task 9b: Add pricing fields to products ──
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->nullable()->after('sale_price')
                  ->comment('Vendor cost / base price');
            $table->decimal('courier_charge', 10, 2)->nullable()->default(0)->after('cost_price')
                  ->comment('Courier cost added to sale price');
            $table->decimal('profit_margin', 5, 2)->nullable()->default(0)->after('courier_charge')
                  ->comment('Jeanzo profit % added on top of cost+courier');
        });

        // ── Task 9c/9d: vendor_reviews table ──
        Schema::create('vendor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->tinyInteger('rating')->unsigned()->comment('1–5 stars');
            $table->text('review')->nullable();
            $table->text('vendor_reply')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
            // One review per user per order per vendor
            $table->unique(['vendor_id', 'user_id', 'order_id'], 'vendor_user_order_review_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_reviews');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'courier_charge', 'profit_margin']);
        });
    }
};
