<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('return_requests', 'description')) {
                $table->text('description')->nullable()->after('reason')
                      ->comment('Customer detailed explanation for return');
            }
            if (!Schema::hasColumn('return_requests', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->nullable()->after('description');
            }
            if (!Schema::hasColumn('return_requests', 'type')) {
                $table->string('type', 20)->default('return')->after('status')
                      ->comment('return or exchange');
            }
            if (!Schema::hasColumn('return_requests', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('user_id')
                      ->comment('Vendor whose product is being returned');
                $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
            }
            if (!Schema::hasColumn('return_requests', 'vendor_status')) {
                $table->string('vendor_status', 30)->nullable()->after('vendor_id')
                      ->comment('pending|acknowledged|pickup_arranged|received|refund_initiated');
            }
            if (!Schema::hasColumn('return_requests', 'vendor_note')) {
                $table->text('vendor_note')->nullable()->after('vendor_status');
            }
            if (!Schema::hasColumn('return_requests', 'images')) {
                $table->json('images')->nullable()->after('vendor_note')
                      ->comment('Customer-uploaded photos of return item');
            }
        });
    }

    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn(['description', 'refund_amount', 'type', 'vendor_id', 'vendor_status', 'vendor_note', 'images']);
        });
    }
};
