<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert any existing stored values that look like paths (e.g. products/abc.jpg)
        // into a pure filename so frontend can build:
        // storage/products/{product_id}/images/{filename}
        DB::table('product_images')->select('id', 'product_id', 'image')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                $image = (string) $row->image;
                if ($image === '') {
                    continue;
                }

                $basename = basename($image);
                if ($basename !== $image) {
                    DB::table('product_images')->where('id', $row->id)->update(['image' => $basename]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible without original full paths.
    }
};

