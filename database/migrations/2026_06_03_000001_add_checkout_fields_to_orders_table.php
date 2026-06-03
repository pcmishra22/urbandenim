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
        Schema::table('orders', function (Blueprint $table) {
            // Shipping / billing info (stored as snapshot at time of order)
            $table->string('shipping_full_name')->nullable()->after('status');
            $table->string('shipping_phone')->nullable()->after('shipping_full_name');
            $table->string('shipping_street')->nullable()->after('shipping_phone');
            $table->string('shipping_city')->nullable()->after('shipping_street');
            $table->string('shipping_state')->nullable()->after('shipping_city');
            $table->string('shipping_postal_code')->nullable()->after('shipping_state');
            $table->string('shipping_country')->nullable()->after('shipping_postal_code');

            // Pricing breakdown
            $table->decimal('subtotal', 10, 2)->default(0)->after('total_price');
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('subtotal');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('shipping_cost');
            $table->string('coupon_code')->nullable()->after('discount_amount');

            // Payment
            $table->string('payment_method')->default('cod')->after('coupon_code');
            $table->string('payment_status')->default('pending')->after('payment_method');

            // Notes
            $table->text('notes')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_full_name', 'shipping_phone', 'shipping_street',
                'shipping_city', 'shipping_state', 'shipping_postal_code', 'shipping_country',
                'subtotal', 'shipping_cost', 'discount_amount', 'coupon_code',
                'payment_method', 'payment_status', 'notes',
            ]);
        });
    }
};
