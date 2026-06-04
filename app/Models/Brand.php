<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo_url',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'description',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the public URL for the brand logo, falling back to default.
     */
    public function getLogoAttribute(): string
    {
        if ($this->logo_url && Storage::disk('public')->exists($this->logo_url)) {
            return Storage::disk('public')->url($this->logo_url);
        }
        return asset('storage/default.jpeg');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
