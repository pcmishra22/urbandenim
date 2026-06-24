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
