<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExchangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'status',
        'requested_at',
        'approved_at',
        'rejected_at',
        'approved_by_admin_id',
        'exchange_wallet_amount',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'exchange_wallet_amount' => 'decimal:2',
    ];

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class, 'return_request_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ExchangeRequestItem::class, 'exchange_request_id');
    }
}

