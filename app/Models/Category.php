<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'image_url',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
        'canonical_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the public URL for the category image, falling back to default.
     */
    public function getImageAttribute(): string
    {
        if ($this->image_url && Storage::disk('public')->exists($this->image_url)) {
            return Storage::disk('public')->url($this->image_url);
        }
        return asset('storage/default.jpeg');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Backwards-compat: some code paths call $category->images (plural) but this model only
     * stores a single image_url.
     *
     * Returning an empty relation would still require schema work; instead we provide a
     * safe accessor-like structure so legacy code won't throw an undefined-relationship
     * exception.
     */
    public function images()
    {
        // Provide a valid Eloquent relation so eager-loading (`with('images')`) works.
        // We don't have a real category_images table in this codebase, so expose a
        // minimal hasMany-like relation that returns zero rows.
        return $this->hasMany(ProductImage::class, 'product_id', 'id')->whereRaw('1 = 0');
    }
}
