<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorWalletTransaction extends Model
{
    protected $fillable = [
        'vendor_wallet_id',
        'type',
        'source',
        'amount',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(VendorWallet::class, 'vendor_wallet_id');
    }
}

