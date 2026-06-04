<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the product that owns this image.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the public URL for this image, falling back to default.
     */
    public function getUrlAttribute(): string
    {
        $relative = 'products/' . $this->product_id . '/images/' . $this->image;
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relative)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($relative);
        }
        return asset('storage/default.jpeg');
    }
}
// (accessor appended below)
