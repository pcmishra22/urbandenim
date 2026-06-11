<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

/**
 * Seeds default page_header banner records.
 * These have no image_url — they'll use the gradient fallback until
 * an admin uploads an actual image via Admin > Banners.
 *
 * Run: php artisan db:seed --class=PageBannerSeeder
 */
class PageBannerSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['title' => 'shop',            'heading' => 'Our Collection',       'sort_order' => 10],
            ['title' => 'product-detail',  'heading' => 'Product Details',       'sort_order' => 10],
            ['title' => 'Shopping Cart',   'heading' => 'Your Shopping Cart',    'sort_order' => 10],
            ['title' => 'checkout',        'heading' => 'Secure Checkout',        'sort_order' => 10],
            ['title' => 'Order Confirmed', 'heading' => 'Order Confirmed ✓',      'sort_order' => 10],
            ['title' => 'My Account',      'heading' => 'My Account',             'sort_order' => 10],
            ['title' => 'My Orders',       'heading' => 'My Orders',              'sort_order' => 10],
            ['title' => 'My Wishlist',     'heading' => 'My Wishlist',            'sort_order' => 10],
            ['title' => 'Contact Us',      'heading' => 'Get In Touch',           'sort_order' => 10],
            ['title' => 'FAQs',            'heading' => 'Frequently Asked Questions', 'sort_order' => 10],
            ['title' => null,              'heading' => null,                     'sort_order' => 1],  // global fallback
        ];

        foreach ($pages as $page) {
            Banner::firstOrCreate(
                ['type' => 'page_header', 'title' => $page['title']],
                [
                    'heading'    => $page['heading'],
                    'image_url'  => '',
                    'link_url'   => null,
                    'sort_order' => $page['sort_order'],
                    'is_active'  => true,
                ]
            );
        }

        $this->command->info('Page header banners seeded. Upload images via Admin > Banners > Page Header.');
    }
}
