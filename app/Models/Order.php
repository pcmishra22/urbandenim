<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
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
        'razorpay_order_id',   // legacy name → payu_txnid (migration renames it)
        'razorpay_payment_id', // legacy name → payu_payment_id (migration renames it)
        'payu_txnid',
        'payu_payment_id',
        'cf_order_id',         // Cashfree order ID (e.g. order_77_timestamp)
        'cf_payment_id',       // Cashfree payment reference ID
    ];

    protected $casts = [
        'total_price'     => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'shipping_cost'   => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(OrderShipment::class, 'order_id');
    }
}
