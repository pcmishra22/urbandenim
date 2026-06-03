<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rules', function (Blueprint $table) {
            $table->id();
            $table->string('country', 2);
            // Example: state / province / city / zone label
            $table->string('region')->nullable();
            $table->string('service_level')->nullable();

            $table->unsignedInteger('base_days')->default(2);
            $table->unsignedInteger('extra_days')->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['country', 'region']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rules');
    }
};

