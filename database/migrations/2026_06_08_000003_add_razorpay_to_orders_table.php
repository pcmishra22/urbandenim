<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Originally created for Razorpay; repurposed for PayU.
 * Columns renamed to payu_* to avoid confusion.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Rename if old razorpay columns already exist (safe guard).
            if (Schema::hasColumn('orders', 'razorpay_order_id') && !Schema::hasColumn('orders', 'payu_txnid')) {
                $table->renameColumn('razorpay_order_id', 'payu_txnid');
            } elseif (!Schema::hasColumn('orders', 'payu_txnid')) {
                $table->string('payu_txnid')->nullable()->after('payment_status');
            }

            if (Schema::hasColumn('orders', 'razorpay_payment_id') && !Schema::hasColumn('orders', 'payu_payment_id')) {
                $table->renameColumn('razorpay_payment_id', 'payu_payment_id');
            } elseif (!Schema::hasColumn('orders', 'payu_payment_id')) {
                $table->string('payu_payment_id')->nullable()->after('payu_txnid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payu_txnid')) {
                $table->renameColumn('payu_txnid', 'razorpay_order_id');
            }
            if (Schema::hasColumn('orders', 'payu_payment_id')) {
                $table->renameColumn('payu_payment_id', 'razorpay_payment_id');
            }
        });
    }
};
