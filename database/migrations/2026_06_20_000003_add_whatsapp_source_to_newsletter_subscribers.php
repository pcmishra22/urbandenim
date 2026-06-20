<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->string('whatsapp', 20)->nullable()->after('email')
                  ->comment('WhatsApp number if captured via exit popup');
            $table->string('source', 50)->nullable()->default('newsletter')->after('is_active')
                  ->comment('How they subscribed: newsletter, exit_popup, etc.');
        });
    }

    public function down(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->dropColumn(['whatsapp', 'source']);
        });
    }
};
