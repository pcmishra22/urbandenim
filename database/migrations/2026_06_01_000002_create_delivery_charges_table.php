<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_rule_id')->constrained('shipping_rules')->cascadeOnDelete();

            // Tiered ranges
            $table->decimal('weight_from', 10, 2)->default(0);
            $table->decimal('weight_to', 10, 2);

            $table->decimal('charge_amount', 10, 2);
            $table->string('currency', 3)->default('USD');

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['shipping_rule_id', 'weight_from', 'weight_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_charges');
    }
};

