<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

/**
 * Backfills featured_image_url on existing seeded blog posts.
 * Uses free Unsplash denim/fashion images — no storage upload needed.
 * Safe to run multiple times (only updates posts with null image).
 *
 * Run: php artisan db:seed --class=BlogImageSeeder
 */
class BlogImageSeeder extends Seeder
{
    // Curated Unsplash denim images, one per seeded post (by slug)
    private const POST_IMAGES = [
        'how-to-style-slim-fit-jeans-for-every-occasion'
            => 'https://images.unsplash.com/photo-1604176354204-9268737828e4?w=800&q=80',
        'the-ultimate-guide-to-womens-jeans-fits-find-your-perfect-pair'
            => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=800&q=80',
        '5-ways-to-wear-straight-leg-jeans-this-season'
            => 'https://images.unsplash.com/photo-1475178626620-a4d074967452?w=800&q=80',
        'how-to-find-your-perfect-jeans-size-the-jeenzo-sizing-guide'
            => 'https://images.unsplash.com/photo-1582552938357-32b906df40cb?w=800&q=80',
        'skinny-vs-slim-fit-jeans-whats-the-actual-difference'
            => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=800&q=80',
        'how-to-wash-your-jeans-the-right-way-and-how-often'
            => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&q=80',
        '7-ways-to-make-your-jeans-last-longer'
            => 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800&q=80',
        'denim-trends-in-india-2025-whats-in-and-whats-out'
            => 'https://images.unsplash.com/photo-1551537482-f2075a1d41f2?w=800&q=80',
        'how-to-style-jeans-for-indian-summers'
            => 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=800&q=80',
        'what-to-wear-to-an-indian-wedding-in-jeans'
            => 'https://images.unsplash.com/photo-1594938298603-c8148c4b4c35?w=800&q=80',
        'why-jeenzo-our-story-our-denim-our-promise'
            => 'https://images.unsplash.com/photo-1582552938357-32b906df40cb?w=800&q=80',
    ];

    public function run(): void
    {
        $updated = 0;

        foreach (self::POST_IMAGES as $slug => $imageUrl) {
            $rows = BlogPost::where('slug', $slug)
                ->whereNull('featured_image_url')
                ->update(['featured_image_url' => $imageUrl]);
            if ($rows) {
                $this->command->info("  ✓ {$slug}");
                $updated += $rows;
            }
        }

        $this->command->info("BlogImageSeeder: {$updated} posts updated.");
    }
}
