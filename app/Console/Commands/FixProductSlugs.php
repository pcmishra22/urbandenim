<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Str;

class FixProductSlugs extends Command
{
    protected $signature   = 'products:fix-slugs';
    protected $description = 'Fix broken product slugs (spaces, capitals, encoded chars)';

    public function handle(): int
    {
        $products = Product::withTrashed()->get();
        $fixed    = 0;

        foreach ($products as $product) {
            $original = $product->slug;
            $clean    = Str::slug(urldecode((string) $original));

            if ($original === $clean) {
                continue;
            }

            // Make unique if another product already has this slug
            $base = $clean;
            $i    = 1;
            while (
                Product::withTrashed()
                    ->where('slug', $clean)
                    ->where('id', '!=', $product->id)
                    ->exists()
            ) {
                $clean = $base . '-' . $i++;
            }

            Product::withoutTimestamps(function () use ($product, $clean) {
                $product->slug = $clean;
                $product->saveQuietly();
            });

            $this->line("Fixed [{$product->id}]: <comment>{$original}</comment> → <info>{$clean}</info>");
            $fixed++;
        }

        $this->info("\nDone. Fixed {$fixed} product slug(s).");
        return self::SUCCESS;
    }
}
