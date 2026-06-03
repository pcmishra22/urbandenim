<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete();

            $table->string('tracking_id')->nullable()->index();
            $table->string('status')->default('created');

            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Optional: store history snapshots
            $table->text('tracking_history')->nullable();

            $table->timestamps();

            $table->unique(['order_id', 'courier_id', 'tracking_id'], 'order_courier_tracking_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_shipments');
    }
};

