<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Target products that don't have variants yet
        $products = Product::doesntHave('variants')->get();

        $sizes = ['28', '30', '32', '34', '36'];
        $colors = ['Classic Blue', 'Deep Black', 'Vintage Indigo', 'Stone Wash'];

        foreach ($products as $product) {
            // Create a small set of variants for any product that still doesn't have them
            // (main ProductSeeder should already create variants for new products).
            $selectedColors = (array) array_rand(array_flip($colors), 2);

            foreach ($selectedColors as $color) {
                $size = $sizes[array_rand($sizes)];

                ProductVariant::firstOrCreate(
                    ['sku' => $product->sku . '-' . Str::upper(substr($color, 0, 1)) . $size],
                    [
                        'product_id' => $product->id,
                        'waist_size' => $size,
                        'color' => $color,
                        'quantity' => rand(20, 120),
                        'price' => $product->sale_price ?? $product->price,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}