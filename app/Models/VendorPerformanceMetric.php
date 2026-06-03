<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorPerformanceMetric extends Model
{
    protected $fillable = [
        'vendor_id',
        'period_start',
        'period_end',
        'orders_count',
        'delivered_count',
        'on_time_delivery_rate',
        'cancel_rate',
        'returns_count',
        'rating_avg',
    ];

    protected $casts = [
        'on_time_delivery_rate' => 'decimal:4',
        'cancel_rate' => 'decimal:4',
        'rating_avg' => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

