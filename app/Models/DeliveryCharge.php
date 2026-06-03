<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_rule_id',
        'weight_from',
        'weight_to',
        'charge_amount',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'weight_from' => 'decimal:2',
        'weight_to' => 'decimal:2',
        'charge_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function shippingRule(): BelongsTo
    {
        return $this->belongsTo(ShippingRule::class, 'shipping_rule_id');
    }
}

