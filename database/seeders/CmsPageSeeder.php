<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class CmsPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about',
                'title' => 'About Us',
                'content' => '<h3>Our Story</h3><p>UrbanDenim started with a simple idea: to create the perfect pair of jeans for the modern urban explorer. We combine traditional craftsmanship with innovative fabrics to bring you denim that feels as good as it looks.</p>',
                'meta_title' => 'About UrbanDenim - Our Story & Craftsmanship',
                'meta_description' => 'Learn how UrbanDenim creates the perfect pair of jeans for modern explorers through quality craftsmanship and innovation.',
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms & Conditions',
                'content' => '<h3>1. Acceptance of Terms</h3><p>By accessing and using UrbanDenim, you accept and agree to be bound by the terms and provision of this agreement.</p>',
                'meta_title' => 'Terms of Service | UrbanDenim',
                'meta_description' => 'Read our terms and conditions for using the UrbanDenim platform and purchasing our products.',
            ],
            [
                'slug' => 'privacy',
                'title' => 'Privacy Policy',
                'content' => '<h3>Privacy Policy</h3><p>Your privacy is critical to us. We collect minimal data necessary to fulfill your orders and provide a personalized experience.</p>',
                'meta_title' => 'Privacy Policy - Your Data Security',
                'meta_description' => 'UrbanDenim is committed to your privacy. Read our policy on how we collect and use your data safely.',
            ],
        ];

        foreach ($pages as $page) {
            CmsPage::firstOrCreate(['slug' => $page['slug']], $page);
        }
    }
}