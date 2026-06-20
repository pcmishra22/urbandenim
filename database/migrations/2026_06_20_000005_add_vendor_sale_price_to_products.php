<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // What the vendor wants to sell at (their price to Jeanzo)
            $table->decimal('vendor_sale_price', 10, 2)->nullable()->after('sale_price')
                  ->comment('Price set by vendor. Jeanzo display price = vendor_sale_price + courier_charge + jeanzo profit margin');
        });

        // Copy existing sale_price into vendor_sale_price for existing products
        DB::statement('UPDATE products SET vendor_sale_price = sale_price WHERE vendor_id IS NOT NULL AND sale_price IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('vendor_sale_price');
        });
    }
};
