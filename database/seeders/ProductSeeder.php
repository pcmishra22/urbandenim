<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Base templates (existing 5)
        $templates = [
            [
                'title' => 'Black Slim Fit Jeans',
                'short_description' => 'Classic black slim fit jeans for men',
                'description' => 'Premium quality black slim fit jeans perfect for everyday wear. Made from 98% cotton and 2% elastane for ultimate comfort and flexibility.',
                'price' => 2499,
                'sale_price' => 1999,
                'gender' => 'men',
                'age_group' => 'adult',
                'fit_type' => 'slim',
                'fabric' => 'denim',
                'color_family' => 'black',
                'is_featured' => true,
                'category' => 'mens-jeans',
                'brand' => 'UrbanDenim',
                'variants' => [
                    ['size' => '28', 'color' => 'Black', 'stock' => 15],
                    ['size' => '30', 'color' => 'Black', 'stock' => 20],
                    ['size' => '32', 'color' => 'Black', 'stock' => 18],
                    ['size' => '34', 'color' => 'Black', 'stock' => 12],
                ],
            ],
            [
                'title' => 'Blue Baggy Jeans For Boys',
                'short_description' => 'Stylish blue baggy jeans for kids',
                'description' => 'Comfortable and trendy blue baggy denim jeans for kids. Perfect for active kids who love to play and have fun in style.',
                'price' => 1499,
                'sale_price' => 1199,
                'gender' => 'boys',
                'age_group' => 'kids',
                'fit_type' => 'baggy',
                'fabric' => 'denim',
                'color_family' => 'blue',
                'is_featured' => true,
                'category' => 'kids-denim',
                'brand' => 'FitDenim',
                'variants' => [
                    ['size' => '26', 'color' => 'Blue', 'stock' => 10],
                    ['size' => '27', 'color' => 'Blue', 'stock' => 12],
                    ['size' => '28', 'color' => 'Blue', 'stock' => 15],
                ],
            ],
            [
                'title' => 'Navy Straight Fit Jeans Women',
                'short_description' => 'Navy blue straight fit jeans for women',
                'description' => 'Elegant navy blue straight fit jeans for women. Flattering cut that works with any top and perfect for both casual and semi-formal occasions.',
                'price' => 2199,
                'sale_price' => 1799,
                'gender' => 'women',
                'age_group' => 'adult',
                'fit_type' => 'straight',
                'fabric' => 'denim',
                'color_family' => 'navy',
                'is_featured' => false,
                'category' => 'womens-denim',
                'brand' => 'DenimCo',
                'variants' => [
                    ['size' => '26', 'color' => 'Navy', 'stock' => 8],
                    ['size' => '28', 'color' => 'Navy', 'stock' => 10],
                    ['size' => '30', 'color' => 'Navy', 'stock' => 12],
                ],
            ],
            [
                'title' => 'Light Blue Skinny Jeans Premium',
                'short_description' => 'Premium light blue skinny fit jeans',
                'description' => 'Luxury light blue skinny fit jeans for the discerning customer. Made from imported denim with superior finishing and comfort.',
                'price' => 3499,
                'sale_price' => 2799,
                'gender' => 'men',
                'age_group' => 'adult',
                'fit_type' => 'skinny',
                'fabric' => 'denim',
                'color_family' => 'light-blue',
                'is_featured' => true,
                'category' => 'premium-denim',
                'brand' => 'PremiumJeans',
                'variants' => [
                    ['size' => '28', 'color' => 'Light Blue', 'stock' => 5],
                    ['size' => '30', 'color' => 'Light Blue', 'stock' => 8],
                    ['size' => '32', 'color' => 'Light Blue', 'stock' => 6],
                ],
            ],
            [
                'title' => 'Dark Gray Regular Fit Jeans',
                'short_description' => 'Versatile dark gray regular fit jeans',
                'description' => 'Versatile dark gray regular fit jeans that pair well with everything. Comfortable for all-day wear with a timeless style.',
                'price' => 1999,
                'sale_price' => 1599,
                'gender' => 'men',
                'age_group' => 'adult',
                'fit_type' => 'regular',
                'fabric' => 'denim',
                'color_family' => 'gray',
                'is_featured' => false,
                'category' => 'mens-jeans',
                'brand' => 'SkyDenim',
                'variants' => [
                    ['size' => '30', 'color' => 'Dark Gray', 'stock' => 20],
                    ['size' => '32', 'color' => 'Dark Gray', 'stock' => 22],
                    ['size' => '34', 'color' => 'Dark Gray', 'stock' => 18],
                ],
            ],
        ];

        // Generate 200 products total
        $targetCount = 200;
        $existingCount = Product::count();
        $toCreate = max(0, $targetCount - $existingCount);

        if ($toCreate === 0) {
            return;
        }

        $created = 0;
        $templateIndex = 0;

        while ($created < $toCreate) {
            $template = $templates[$templateIndex % count($templates)];
            $templateIndex++;

            $category = Category::where('slug', $template['category'])->first();
            $brand = Brand::where('slug', Str::slug($template['brand']))->first();

            if (!$category || !$brand) {
                continue;
            }

            // Create a deterministic unique slug so firstOrCreate does not collapse duplicates
            $variantSuffix = $existingCount + $created + 1; // 1-based
            $title = $template['title'] . ' #' . $variantSuffix;
            $slug = Str::slug($title);

            $product = Product::firstOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'name' => $title,
                    'sku' => 'UD-' . Str::upper(Str::random(6)) . '-' . $variantSuffix,
                    'short_description' => $template['short_description'],
                    'description' => $template['description'],
                    'price' => $template['price'],
                    'sale_price' => $template['sale_price'],
                    'gender' => $template['gender'],
                    'age_group' => $template['age_group'],
                    'fit_type' => $template['fit_type'],
                    'color_family' => $template['color_family'],
                    'is_featured' => (bool) $template['is_featured'],
                    'is_active' => true,
                    'meta_title' => $title . ' | UrbanDenim',
                    'meta_description' => $template['short_description'],
                ]
            );

            // When product is newly created, create variants + at least one image.
            if ($product->wasRecentlyCreated) {
                // Scale stock for higher overall inventory
                $scale = 5; // base stock * 5 -> generally 150+ total per product across variants

                foreach ($template['variants'] as $variantData) {
                    ProductVariant::firstOrCreate(
                        [
                            'sku' => $product->sku . '-' . Str::upper(substr($variantData['color'], 0, 1)) . $variantData['size'],
                        ],
                        [
                            'product_id' => $product->id,
                            'waist_size' => $variantData['size'],
                            'color' => $variantData['color'],
                            'quantity' => (int) max(1, $variantData['stock'] * $scale),
                            'price' => $template['sale_price'] ?? $template['price'],
                            'is_active' => true,
                        ]
                    );
                }

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => 'products/sample-' . Str::random(6) . '.jpg',
                    'sort_order' => 1,
                ]);

                $created++;
            }
        }
    }
}

