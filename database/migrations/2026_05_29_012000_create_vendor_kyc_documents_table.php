<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_kyc_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();

            $table->string('document_type');
            $table->string('file_path');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->foreignId('verifier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_kyc_documents');
    }
};

