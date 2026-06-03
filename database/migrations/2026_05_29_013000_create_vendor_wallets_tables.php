<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete()->unique();
            $table->decimal('balance', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('vendor_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_wallet_id')->constrained('vendor_wallets')->cascadeOnDelete();

            $table->enum('type', ['credit', 'debit']);
            $table->string('source');
            $table->decimal('amount', 18, 2);
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_wallet_transactions');
        Schema::dropIfExists('vendor_wallets');
    }
};

