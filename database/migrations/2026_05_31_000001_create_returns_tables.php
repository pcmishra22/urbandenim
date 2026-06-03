<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('return_requests')) {
            Schema::create('return_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

                $table->enum('status', ['requested', 'approved', 'rejected', 'pickup_requested', 'pickup_received', 'refund_wallet_queued', 'refund_completed'])->default('requested');

                $table->text('reason')->nullable();

                $table->timestamp('requested_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('rejected_at')->nullable();

                $table->decimal('refund_wallet_amount', 18, 2)->default(0);
                $table->string('refund_wallet_currency', 3)->default('USD');

                $table->foreignId('approved_by_admin_id')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamps();

                $table->index('status');
            });
        }

        if (!Schema::hasTable('return_request_items')) {
            Schema::create('return_request_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('return_request_id')->constrained('return_requests')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('quantity')->default(1);
                $table->text('reason')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('reverse_pickup_requests')) {
            Schema::create('reverse_pickup_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('return_request_id')->constrained('return_requests')->cascadeOnDelete();


                $table->enum('status', ['requested', 'picked_up', 'received', 'cancelled'])->default('requested');

                $table->timestamp('requested_at')->nullable();
                $table->timestamp('picked_up_at')->nullable();
                $table->timestamp('received_at')->nullable();

                if (Schema::hasTable('couriers')) {
                    $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete();
                } else {
                    $table->unsignedBigInteger('courier_id')->nullable();
                }
                $table->string('tracking_id')->nullable();

                $table->timestamps();

                $table->index(['return_request_id', 'status']);
            });
        }

        if (!Schema::hasTable('exchange_requests')) {
            Schema::create('exchange_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('return_request_id')->constrained('return_requests')->cascadeOnDelete();

                $table->enum('status', ['requested', 'approved', 'rejected', 'refund_wallet_queued', 'completed'])->default('requested');

                $table->timestamp('requested_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('rejected_at')->nullable();

                $table->foreignId('approved_by_admin_id')->nullable()->constrained('users')->nullOnDelete();

                $table->decimal('exchange_wallet_amount', 18, 2)->default(0);

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('exchange_request_items')) {
            Schema::create('exchange_request_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exchange_request_id')->constrained('exchange_requests')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('quantity')->default(1);
                $table->text('reason')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wallet_refund_transactions')) {
            Schema::create('wallet_refund_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('return_request_id')->nullable()->constrained('return_requests')->nullOnDelete();

                $table->enum('type', ['credit'])->default('credit');
                $table->decimal('amount', 18, 2);
                $table->string('currency', 3)->default('USD');
                $table->enum('status', ['queued', 'completed', 'failed'])->default('queued');
                $table->json('meta')->nullable();

                $table->foreignId('created_by_admin_id')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamps();

                $table->index(['user_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_refund_transactions');
        Schema::dropIfExists('exchange_request_items');
        Schema::dropIfExists('exchange_requests');
        Schema::dropIfExists('reverse_pickup_requests');
        Schema::dropIfExists('return_request_items');
        Schema::dropIfExists('return_requests');
    }
};

