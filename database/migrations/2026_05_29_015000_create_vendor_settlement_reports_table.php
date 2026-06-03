<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_settlement_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();

            $table->date('period_start');
            $table->date('period_end');

            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->decimal('commission_amount', 18, 2)->default(0);
            $table->decimal('net_payout_amount', 18, 2)->default(0);

            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'paid'])->default('draft');
            $table->timestamp('paid_at')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            $table->unique(['vendor_id', 'period_start', 'period_end'], 'vendor_settlement_unique');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_settlement_reports');
    }
};

