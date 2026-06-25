<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Review;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'price', 'quantity',
        'category_id', 'brand_id', 'vendor_id',
        'slug', 'sku', 'short_description',
        'sale_price', 'vendor_sale_price',
        'cost_price', 'courier_charge', 'profit_margin',
        'gender', 'age_group', 'color_family',
        'is_featured', 'is_active',
        'model_info', 'fabric_info',
    ];

    /**
     * The price Jeanzo displays to customers.
     * If vendor has set vendor_sale_price → apply courier + profit on top.
     * Otherwise fall back to manually set sale_price or price.
     *
     * Formula: jeanzo_price = (vendor_sale_price + courier_charge) × (1 + profit_margin/100)
     */
    public function getJeanzoPriceAttribute(): float
    {
        if ($this->vendor_sale_price) {
            $base   = (float) $this->vendor_sale_price + (float) ($this->courier_charge ?? 0);
            $margin = (float) ($this->profit_margin ?? 0);
            return round($base * (1 + $margin / 100), 2);
        }
        return (float) ($this->sale_price ?? $this->price ?? 0);
    }

    /**
     * Auto-calculate sale_price from cost_price + courier_charge + profit_margin%.
     * Used when admin manually enters cost/courier/margin (no vendor).
     */
    public function recalculateSalePrice(): void
    {
        if (!$this->cost_price) return;
        $base  = (float) $this->cost_price + (float) ($this->courier_charge ?? 0);
        $margin = (float) ($this->profit_margin ?? 0);
        $this->sale_price = round($base * (1 + $margin / 100), 2);
    }

    /**
     * Accessor: expose 'title' as an alias for 'name' for backwards-compat.
     */
    public function getTitleAttribute(): string
    {
        return $this->attributes['name'] ?? '';
    }

    
    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug) && !empty($product->name)) {
                $base = Str::slug($product->name);
                $slug = $base;
                $i    = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $product->slug = $slug;
            }
        });

        static::saving(function ($product) {
            // Fix any existing bad slug (spaces, capitals, encoded chars)
            if (!empty($product->slug)) {
                $product->slug = Str::slug(urldecode($product->slug));
            }
        });
    }

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the category that owns this product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns this product.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the vendor that owns this product.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }


    /**
     * Get the variants for this product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the images for this product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the orders that contain this product.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items');
    }

    /**
     * Get wishlists containing this product.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get approved reviews for this product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true)->orderByDesc('created_at');
    }

    /**
     * Get all reviews for this product (including pending/rejected).
     */
    public function allReviews(): HasMany
    {
        return $this->hasMany(Review::class)->orderByDesc('created_at');
    }
}
