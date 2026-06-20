<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('model_info', 100)->nullable()->after('short_description')
                  ->comment('e.g. Model is 5\'5", wearing size 30');
            $table->string('fabric_info', 150)->nullable()->after('model_info')
                  ->comment('e.g. 98% Cotton, 2% Elastane — Medium Stretch');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['model_info', 'fabric_info']);
        });
    }
};
