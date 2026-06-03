<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'type',
        'reason',
        'description',
        'refund_amount',
        'refund_wallet_amount',
        'refund_wallet_currency',
        'approved_at',
        'rejected_at',
        'status',
        'approved_by_admin_id',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnRequestItem::class, 'return_request_id');
    }

    public function pickupRequests(): HasMany
    {
        return $this->hasMany(ReversePickupRequest::class, 'return_request_id');
    }

    public function exchangeRequest(): HasOne
    {
        return $this->hasOne(ExchangeRequest::class, 'return_request_id');
    }
}



