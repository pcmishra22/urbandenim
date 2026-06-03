<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'waist_size',
        'size',
        'length',
        'color',
        'stretch_level',
        'price',
        'quantity',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the product that owns this variant.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
