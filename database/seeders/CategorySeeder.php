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
                    'meta_title' => $category['name'] . ' - Premium Denim Collection | UrbanDenim',
                    'meta_description' => $category['description'],
                ]
            );
        }

        // Create subcategories for Men's Jeans
        $mensCategory = Category::where('slug', 'mens-jeans')->first();
        if ($mensCategory) {
            $subcategories = [
                'Slim Fit',
                'Regular Fit',
                'Baggy Fit',
                'Skinny Fit',
                'Straight Fit',
            ];

            foreach ($subcategories as $subcat) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($subcat)],
                    [
                        'name' => $subcat,
                        'description' => "Men's {$subcat} Jeans",
                        'parent_id' => $mensCategory->id,
                        'is_active' => true,
                        'meta_title' => "Men's {$subcat} Fit Jeans | UrbanDenim",
                        'meta_description' => "Discover the best Men's {$subcat} jeans. High quality, durable denim for the modern urban lifestyle.",
                    ]
                );
            }
        }
    }
}
