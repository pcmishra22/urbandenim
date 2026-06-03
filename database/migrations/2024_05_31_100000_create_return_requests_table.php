<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // NOTE:
        // This migration creates an early version of `return_requests`.
        // The project later introduced a newer returns schema migration
        // (2026_05_31_000001_create_returns_tables.php) that should be the source of truth.
        // Keeping this migration as a no-op avoids FK/table-order issues and duplicate table errors.
    }


    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};