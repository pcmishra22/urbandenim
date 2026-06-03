<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'subtotal',
        'shipping_cost',
        'discount_amount',
        'coupon_code',
        'status',
        'payment_method',
        'payment_status',
        'shipping_full_name',
        'shipping_phone',
        'shipping_street',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'notes',
    ];

    protected $casts = [
        'total_price'     => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'shipping_cost'   => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /**
     * Get the user who placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products in this order.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
                    ->withPivot('quantity', 'price');
    }

    public function shipments()
    {
        return $this->hasMany(OrderShipment::class, 'order_id');
    }
}

