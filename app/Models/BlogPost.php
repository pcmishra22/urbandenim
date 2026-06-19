<?php

namespace App\Models;

use App\Traits\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes, HasSeo;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image_url',
        'blog_category_id',
        'is_featured',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_title',
        'og_description',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Unified image URL — works as both old-style accessor ($post->image_url)
     * and is explicitly appended so it serialises correctly.
     */
    public function getImageUrlAttribute(): string
    {
        // 1. Stored URL in DB (set by seeder or admin upload)
        if (!empty($this->attributes['featured_image_url'])) {
            return $this->attributes['featured_image_url'];
        }

        // 2. Category-based Unsplash defaults
        $defaults = [
            'style-guide'    => 'https://images.unsplash.com/photo-1604176354204-9268737828e4?w=800&q=80',
            'fit-comfort'    => 'https://images.unsplash.com/photo-1582552938357-32b906df40cb?w=800&q=80',
            'care-maintain'  => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&q=80',
            'trends'         => 'https://images.unsplash.com/photo-1551537482-f2075a1d41f2?w=800&q=80',
            'brand-story'    => 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800&q=80',
        ];

        // Use relation if already loaded, else slug from attributes
        $slug = $this->relationLoaded('category')
            ? optional($this->category)->slug
            : null;

        if ($slug && isset($defaults[$slug])) {
            return $defaults[$slug];
        }

        // 3. Generic denim fallback
        return 'https://images.unsplash.com/photo-1604176354204-9268737828e4?w=800&q=80';
    }

    public function getOgImageAttribute(): string
    {
        return $this->getImageUrlAttribute();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag', 'blog_post_id', 'blog_tag_id');
    }

    public function relatedPosts(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogPost::class,
            'blog_post_related',
            'blog_post_id',
            'related_blog_post_id'
        );
    }
}

