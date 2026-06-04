<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix category image paths: rename typo folder "cateogories" -> "categories"
        DB::table('categories')
            ->where('image_url', 'like', 'cateogories/%')
            ->update([
                'image_url' => DB::raw("REPLACE(image_url, 'cateogories/', 'categories/')")
            ]);

        // Clear out image_url values that just contain "default.jpeg" as filename
        // (these were placeholder uploads, not real images)
        DB::table('categories')
            ->where('image_url', 'like', '%/default.jpeg')
            ->update(['image_url' => null]);

        DB::table('banners')
            ->where('image_url', 'like', '%/default.jpeg')
            ->update(['image_url' => null]);

        DB::table('brands')
            ->where('logo_url', 'like', '%/default.jpeg')
            ->update(['logo_url' => null]);
    }

    public function down(): void
    {
        // Reverse: categories -> cateogories
        DB::table('categories')
            ->where('image_url', 'like', 'categories/%')
            ->update([
                'image_url' => DB::raw("REPLACE(image_url, 'categories/', 'cateogories/')")
            ]);
    }
};
