<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id')->nullable();

            $table->unsignedTinyInteger('rating')->default(0);
            $table->text('review_text')->nullable();

            // Review state
            $table->string('status')->default('pending'); // pending|approved|rejected|spam
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_spam')->default(false);
            $table->boolean('is_featured')->default(false);

            // Spam signals / lightweight detection
            $table->unsignedTinyInteger('spam_score')->default(0);
            $table->unsignedInteger('reported_count')->default(0);

            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index(['product_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['is_spam', 'is_approved', 'is_featured']);
        });

        // Keep status values consistent (optional DB-level check)
        // MySQL check constraints support varies; safe to skip if DB doesn't support.
        try {
            DB::statement("ALTER TABLE product_reviews ADD CONSTRAINT chk_product_reviews_status CHECK (status IN ('pending','approved','rejected','spam'))");
        } catch (\Throwable $e) {
            // no-op
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};

