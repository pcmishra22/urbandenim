<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'country',
        'region',
        'service_level',
        'base_days',
        'extra_days',
        'is_active',
    ];

    protected $casts = [
        'base_days' => 'integer',
        'extra_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function deliveryCharges(): HasMany
    {
        return $this->hasMany(DeliveryCharge::class, 'shipping_rule_id');
    }
}

