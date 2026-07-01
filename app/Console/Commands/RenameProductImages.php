<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class RenameProductImages extends Command
{
    /**
     * Usage:
     *   php artisan products:rename-images            → rename ALL products' images
     *   php artisan products:rename-images --id=209   → rename only product 209's images
     *   php artisan products:rename-images --dry-run  → preview without touching files
     */
    protected $signature = 'products:rename-images
                            {--id= : Only rename images for this specific product ID}
                            {--dry-run : Show what would change without renaming anything}';

    protected $description = 'Rename product image files from random hash names (e.g. aB3xY9fkq2.jpg) to SEO-friendly names (e.g. womens-skinny-light-blue-jeans-1.jpg)';

    public function handle(): int
    {
        $disk    = Storage::disk('public');
        $dryRun  = $this->option('dry-run');
        $onlyId  = $this->option('id');

        $query = Product::with('images')->withTrashed();
        if ($onlyId) {
            $query->where('id', $onlyId);
        }

        $products = $query->get();
        $renamed  = 0;
        $skipped  = 0;

        foreach ($products as $product) {
            $images = $product->images()->orderBy('sort_order')->get();

            foreach ($images as $index => $image) {
                $seq          = $index + 1;
                $oldRelative  = 'products/' . $product->id . '/images/' . $image->image;

                if (!$disk->exists($oldRelative)) {
                    $this->warn("  [skip] Missing file for product {$product->id}: {$image->image}");
                    $skipped++;
                    continue;
                }

                $extension = pathinfo($image->image, PATHINFO_EXTENSION) ?: 'jpg';
                $newName   = seo_image_filename($product, $seq, $extension);

                if ($image->image === $newName) {
                    // Already renamed
                    continue;
                }

                $newRelative = 'products/' . $product->id . '/images/' . $newName;

                // Avoid collisions if a file with the target name already exists
                $suffix = 1;
                while ($disk->exists($newRelative) && $newRelative !== $oldRelative) {
                    $newName     = seo_image_filename($product, $seq, $extension);
                    $newName     = pathinfo($newName, PATHINFO_FILENAME) . '-' . $suffix . '.' . $extension;
                    $newRelative = 'products/' . $product->id . '/images/' . $newName;
                    $suffix++;
                }

                $this->line("  [{$product->id}] <comment>{$image->image}</comment> → <info>{$newName}</info>");

                if (!$dryRun) {
                    $disk->move($oldRelative, $newRelative);
                    $image->update(['image' => $newName]);
                }

                $renamed++;
            }
        }

        $this->info("\nDone. " . ($dryRun ? '[DRY RUN] Would rename' : 'Renamed') . " {$renamed} image(s). Skipped {$skipped} missing file(s).");

        return self::SUCCESS;
    }
}
