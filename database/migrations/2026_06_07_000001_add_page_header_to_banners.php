<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('heading', 255)->nullable()->after('title')
                  ->comment('Override heading text for page_header banners');
            $table->string('subtitle', 255)->nullable()->after('heading');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['heading', 'subtitle']);
        });
    }
};
