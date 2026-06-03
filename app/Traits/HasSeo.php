<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSeo
{
    /**
     * Generate JSON-LD schema for the model.
     */
    public function generateJsonLd(): string
    {
        // This would be customized per model to output Product or WebPage schema
        $data = [
            '@context' => 'https://schema.org',
            '@type' => $this instanceof \App\Models\Product ? 'Product' : 'WebPage',
            'name' => $this->meta_title ?? $this->name ?? $this->title,
            'description' => $this->meta_description ?? $this->short_description,
            'url' => $this->canonical_url ?? url($this->slug),
        ];

        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Boot the trait to handle automatic slug generation and SEO logic.
     */
    protected static function bootHasSeo()
    {
        static::saving(function ($model) {
            // Auto SEO slug handling
            if (empty($model->slug)) {
                $nameField = isset($model->name) ? 'name' : 'title';
                if (!empty($model->$nameField)) {
                    $model->slug = Str::slug($model->$nameField);
                }
            }
        });
    }
}