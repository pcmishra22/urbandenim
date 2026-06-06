<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed defaults
        $defaults = [
            'facebook_url'  => 'https://facebook.com/eshopper',
            'twitter_url'   => 'https://twitter.com/eshopper',
            'instagram_url' => 'https://instagram.com/eshopper',
            'linkedin_url'  => 'https://linkedin.com/company/eshopper',
            'youtube_url'   => 'https://youtube.com/@eshopper',
            'store_address' => '123, Main Market, Ludhiana, Punjab - 141001',
            'store_phone'   => '+91-98765-43210',
            'store_email'   => 'info@eshopper.com',
        ];
        foreach ($defaults as $key => $value) {
            DB::table('site_settings')->insert(['key'=>$key,'value'=>$value,'created_at'=>now(),'updated_at'=>now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
