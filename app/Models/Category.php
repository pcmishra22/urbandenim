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
}
