<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Target products without images
        $products = Product::doesntHave('images')->get();

        foreach ($products as $product) {
            ProductImage::create([
                'product_id' => $product->id,
                'image' => 'sample-' . Str::random(6) . '.jpg',
                'sort_order' => 1,
            ]);
        }
    }
}

