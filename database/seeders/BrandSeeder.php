<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'UrbanDenim',
                'description' => 'Premium quality urban denim brand',
                'is_featured' => true,
            ],
            [
                'name' => 'DenimCo',
                'description' => 'Classic denim collection for all ages',
                'is_featured' => false,
            ],
            [
                'name' => 'FitDenim',
                'description' => 'Fashion-forward denim with perfect fit',
                'is_featured' => true,
            ],
            [
                'name' => 'SkyDenim',
                'description' => 'Light and comfortable denim wear',
                'is_featured' => false,
            ],
            [
                'name' => 'PremiumJeans',
                'description' => 'Luxury denim for discerning customers',
                'is_featured' => true,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(
                ['slug' => Str::slug($brand['name'])],
                [
                    'name' => $brand['name'],
                    'description' => $brand['description'],
                    'is_active' => true,
                    'is_featured' => $brand['is_featured'],
                ]
            );
        }
    }
}
