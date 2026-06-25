<?php
// Run this as: php artisan tinker --execute="require 'fix_broken_slugs.php';"
// OR paste in tinker

use App\Models\Product;
use Illuminate\Support\Str;

$products = Product::withTrashed()->get();
$fixed = 0;

foreach ($products as $product) {
    $originalSlug = $product->slug;
    $cleanSlug    = Str::slug(urldecode($originalSlug));

    if ($originalSlug !== $cleanSlug) {
        // Make unique if needed
        $base = $cleanSlug;
        $i    = 1;
        while (Product::withTrashed()->where('slug', $cleanSlug)->where('id', '!=', $product->id)->exists()) {
            $cleanSlug = $base . '-' . $i++;
        }
        Product::withoutTimestamps(function () use ($product, $cleanSlug) {
            $product->slug = $cleanSlug;
            $product->saveQuietly();
        });
        echo "Fixed: [{$product->id}] '{$originalSlug}' → '{$cleanSlug}'\n";
        $fixed++;
    }
}

echo "\nDone. Fixed {$fixed} slugs.\n";
