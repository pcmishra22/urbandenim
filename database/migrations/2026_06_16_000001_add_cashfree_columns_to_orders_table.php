<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add Cashfree-specific columns to orders table.
 * cf_order_id  — the order ID sent to Cashfree (e.g. "order_77_1718500000")
 * cf_payment_id — the Cashfree payment reference ID returned on success
 *
 * The legacy payu_txnid / payu_payment_id columns are kept for backward
 * compatibility with existing orders and are reused by the Cashfree controller
 * as a fallback. New orders will use the cf_* columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'cf_order_id')) {
                $table->string('cf_order_id')->nullable()->after('payu_payment_id')
                      ->comment('Cashfree order_id used when creating the payment session');
            }
            if (!Schema::hasColumn('orders', 'cf_payment_id')) {
                $table->string('cf_payment_id')->nullable()->after('cf_order_id')
                      ->comment('Cashfree cf_payment_id returned on successful payment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'cf_order_id'))  $table->dropColumn('cf_order_id');
            if (Schema::hasColumn('orders', 'cf_payment_id')) $table->dropColumn('cf_payment_id');
        });
    }
};
