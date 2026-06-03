<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (Banner::count() > 0) {
            return;
        }

        $banners = [
            [
                'type' => 'homepage',
                'title' => 'Summer Denim Sale',
                'image_url' => '/eshopper/img/carousel-1.jpg',
                'link_url' => '/shop',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'type' => 'homepage',
                'title' => 'New Arrivals',
                'image_url' => '/eshopper/img/carousel-2.jpg',
                'link_url' => '/shop',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'type' => 'sale',
                'title' => 'Up to 30% Off',
                'image_url' => '/eshopper/img/offer-1.png',
                'link_url' => '/shop?on_sale=1',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'type' => 'mobile',
                'title' => 'Easy Returns',
                'image_url' => '/eshopper/img/offer-2.png',
                'link_url' => '/returns',
                'sort_order' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $b) {
            Banner::firstOrCreate(
                ['type' => $b['type'], 'title' => $b['title']],
                [
                    'image_url' => $b['image_url'],
                    'link_url' => $b['link_url'],
                    'sort_order' => $b['sort_order'],
                    'is_active' => $b['is_active'],
                ]
            );
        }
    }
}

