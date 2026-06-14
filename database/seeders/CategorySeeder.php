<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => "Men's Jeans",
                'description' => 'Premium quality jeans for men in various fits and styles',
                'parent_id' => null,
            ],
            [
                'name' => "Women's Denim",
                'description' => 'Stylish denim collection for women',
                'parent_id' => null,
            ],
            [
                'name' => 'Kids Denim',
                'description' => 'Comfortable denim wear for kids',
                'parent_id' => null,
            ],
            [
                'name' => 'Premium Denim',
                'description' => 'Luxury denim pieces for discerning customers',
                'parent_id' => null,
            ],
            [
                'name' => 'Accessories',
                'description' => 'Denim accessories and complementary items',
                'parent_id' => null,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'parent_id' => $category['parent_id'],
                    'is_active' => true,
                ]
            );
        }


        // Men: Jeans Types mapping
        $mensCategory = Category::where('slug', 'mens-jeans')->first();
        if ($mensCategory) {
            // Parent groups
            $byFit = Category::firstOrCreate(
                ['slug' => 'mens-jeans-by-fit'],
                [
                    'name' => "Men's By Fit",
                    'description' => "Men's jeans grouped by fit",
                    'parent_id' => $mensCategory->id,
                    'is_active' => true,
                ]
            );

            $byRise = Category::firstOrCreate(
                ['slug' => 'mens-jeans-by-rise'],
                [
                    'name' => "Men's By Rise",
                    'description' => "Men's jeans grouped by rise",
                    'parent_id' => $mensCategory->id,
                    'is_active' => true,
                ]
            );

            $byStyle = Category::firstOrCreate(
                ['slug' => 'mens-jeans-by-style'],
                [
                    'name' => "Men's By Style",
                    'description' => "Men's jeans grouped by style",
                    'parent_id' => $mensCategory->id,
                    'is_active' => true,
                ]
            );


            $byFitChildren = [
                "Slim Fit Jeans",
                "Skinny Fit Jeans",
                "Regular Fit Jeans",
                "Straight Fit Jeans",
                "Relaxed Fit Jeans",
                "Tapered Fit Jeans",
                "Bootcut Jeans",
                "Loose Fit Jeans",
                "Athletic Fit Jeans",
                "Cargo Jeans",
            ];

            foreach ($byFitChildren as $child) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($child)],
                    [
                        'name' => $child,
                        'description' => "Men's {$child}",
                        'parent_id' => $byFit->id,
                        'is_active' => true,
                    ]
                );
            }

            $byRiseChildren = [
                "Low Rise Jeans",
                "Mid Rise Jeans",
                "High Rise Jeans",
            ];

            foreach ($byRiseChildren as $child) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($child)],
                    [
                        'name' => $child,
                        'description' => "Men's {$child}",
                        'parent_id' => $byRise->id,
                        'is_active' => true,
                    ]
                );
            }

            $byStyleChildren = [
                "Distressed Jeans",
                "Ripped Jeans",
                "Washed Jeans",
                "Stretch Jeans",
                "Vintage Jeans",
                "Carpenter Jeans",
                "Biker Jeans",
            ];

            foreach ($byStyleChildren as $child) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($child)],
                    [
                        'name' => $child,
                        'description' => "Men's {$child}",
                        'parent_id' => $byStyle->id,
                        'is_active' => true,
                    ]
                );
            }
        }

        // Women: Jeans Types mapping
        $womensCategory = Category::where('slug', 'womens-denim')->first();
        if ($womensCategory) {
            $byFit = Category::firstOrCreate(
                ['slug' => 'womens-denim-by-fit'],
                [
                    'name' => "Women's By Fit",
                    'description' => "Women's jeans grouped by fit",
                    'parent_id' => $womensCategory->id,
                    'is_active' => true,
                ]
            );

            $byRise = Category::firstOrCreate(
                ['slug' => 'womens-denim-by-rise'],
                [
                    'name' => "Women's By Rise",
                    'description' => "Women's jeans grouped by rise",
                    'parent_id' => $womensCategory->id,
                    'is_active' => true,
                ]
            );

            $byStyle = Category::firstOrCreate(
                ['slug' => 'womens-denim-by-style'],
                [
                    'name' => "Women's By Style",
                    'description' => "Women's jeans grouped by style",
                    'parent_id' => $womensCategory->id,
                    'is_active' => true,
                ]
            );


            $byFitChildren = [
                "Skinny Jeans",
                "Slim Fit Jeans",
                "Straight Leg Jeans",
                "Relaxed Fit Jeans",
                "Boyfriend Jeans",
                "Girlfriend Jeans",
                "Mom Jeans",
                "Wide Leg Jeans",
                "Flared Jeans",
                "Bootcut Jeans",
            ];

            foreach ($byFitChildren as $child) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($child)],
                    [
                        'name' => $child,
                        'description' => "Women's {$child}",
                        'parent_id' => $byFit->id,
                        'is_active' => true,
                    ]
                );
            }

            $byRiseChildren = [
                "Low Rise Jeans",
                "Mid Rise Jeans",
                "High Rise Jeans",
            ];

            foreach ($byRiseChildren as $child) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($child)],
                    [
                        'name' => $child,
                        'description' => "Women's {$child}",
                        'parent_id' => $byRise->id,
                        'is_active' => true,
                    ]
                );
            }

            $byStyleChildren = [
                "Distressed Jeans",
                "Ripped Jeans",
                "Stretch Jeans",
                "Jeggings",
                "Cropped Jeans",
                "Vintage Jeans",
                "Cargo Jeans",
            ];

            foreach ($byStyleChildren as $child) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($child)],
                    [
                        'name' => $child,
                        'description' => "Women's {$child}",
                        'parent_id' => $byStyle->id,
                        'is_active' => true,
                    ]
                );
            }
        }

    }
}
