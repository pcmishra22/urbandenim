<?php

if (!function_exists('category_url')) {
    /**
     * Return the SEO-friendly URL for a category.
     * Uses /{slug} for any category that has a slug.
     * Falls back to /products?category={id} only if no slug.
     *
     * @param  \App\Models\Category|null  $category
     * @param  array  $extra   Additional query params (sort, color, size etc.)
     * @return string
     */
    function category_url($category, array $extra = []): string
    {
        if (!$category) {
            return route('products.index', $extra);
        }

        $slug = $category->slug ?? '';

        if ($slug) {
            $url = url('/' . $slug);
            if (!empty($extra)) {
                $url .= '?' . http_build_query($extra);
            }
            return $url;
        }

        // Fallback: /products?category=ID
        return route('products.index', array_merge(['category' => $category->id], $extra));
    }
}

if (!function_exists('seo_image_filename')) {
    /**
     * Build an SEO-friendly filename for a product image, e.g.
     * "womens-skinny-light-blue-jeans-2.jpg" instead of a random hash
     * like "aB3xY9fkq2.jpg".
     *
     * @param  \App\Models\Product  $product
     * @param  int     $sequence   1-based position of this image for the product
     * @param  string  $extension  File extension without the leading dot
     * @return string
     */
    function seo_image_filename($product, int $sequence, string $extension): string
    {
        $base = $product->slug ?: \Illuminate\Support\Str::slug($product->name);
        $ext  = strtolower($extension) ?: 'jpg';

        return "{$base}-{$sequence}.{$ext}";
    }
}

if (!function_exists('product_image_alt')) {
    /**
     * Build descriptive, keyword-rich, unique ALT text for a product image,
     * e.g. "Women's Skinny Light Blue Denim Jeans - Front View" instead of
     * a bare repeated product name across every thumbnail.
     *
     * @param  \App\Models\Product  $product
     * @param  int|null  $index  0-based position of this image among the product's images
     * @return string
     */
    function product_image_alt($product, ?int $index = null): string
    {
        $name     = trim($product->name);
        $nameLower = strtolower($name);
        $parts    = [$name];

        if (!empty($product->color_family) && !str_contains($nameLower, strtolower($product->color_family))) {
            $parts[] = $product->color_family;
        }
        if (!str_contains($nameLower, 'jean') && !str_contains($nameLower, 'denim')) {
            $parts[] = 'Denim Jeans';
        }

        $alt = implode(' ', array_filter($parts));

        // View-position labels match the 5 upload slots used in the admin panel
        // (Front, Back, Left, Right, Detail) so every image gets a unique, useful ALT.
        $viewLabels = ['Front View', 'Back View', 'Left Side', 'Right Side', 'Detail View'];

        if ($index !== null && isset($viewLabels[$index])) {
            $alt .= ' - ' . $viewLabels[$index];
        }

        return $alt;
    }
}

