<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_commission_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete()->unique();

            $table->decimal('commission_rate', 5, 4)->default(0.0000); // e.g. 0.0500 = 5%
            $table->decimal('commission_flat', 18, 2)->nullable();
            $table->string('payout_frequency')->default('on_demand');

            $table->timestamps();
        });

        Schema::create('vendor_performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();

            $table->date('period_start');
            $table->date('period_end');

            $table->integer('orders_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->decimal('on_time_delivery_rate', 5, 4)->nullable();
            $table->decimal('cancel_rate', 5, 4)->nullable();
            $table->integer('returns_count')->nullable();
            $table->decimal('rating_avg', 6, 2)->nullable();

            $table->timestamps();

            $table->unique(['vendor_id', 'period_start', 'period_end'], 'vendor_perf_unique');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_performance_metrics');
        Schema::dropIfExists('vendor_commission_rules');
    }
};

