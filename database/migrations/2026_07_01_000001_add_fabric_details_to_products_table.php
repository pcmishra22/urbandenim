<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * fit_type and stretch already exist on the table (added in
     * 2026_05_28_040246) but were never made mass-assignable, so they were
     * silently never saved from the admin form. This migration only adds
     * the columns that are genuinely missing: fabric_weight and wash.
     * waist_rise is intentionally mapped onto the existing fit_type/stretch
     * pattern via a new column since none of the existing ones cover it.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('fabric_weight', 50)->nullable()->after('fabric_info');
            $table->string('wash', 100)->nullable()->after('fabric_weight');
            $table->string('waist_rise', 50)->nullable()->after('wash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['fabric_weight', 'wash', 'waist_rise']);
        });
    }
};
