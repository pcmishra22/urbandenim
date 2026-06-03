<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorCommissionRule extends Model
{
    protected $fillable = [
        'vendor_id',
        'commission_rate',
        'commission_flat',
        'payout_frequency',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:4',
        'commission_flat' => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

