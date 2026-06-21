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
        'order_id', 'user_id', 'vendor_id',
        'type', 'reason', 'description',
        'refund_amount', 'refund_wallet_amount', 'refund_wallet_currency',
        'status', 'vendor_status', 'vendor_note', 'images',
        'approved_at', 'rejected_at', 'approved_by_admin_id',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
        'images'       => 'array',
    ];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function user(): BelongsTo  { return $this->belongsTo(User::class); }
    public function vendor(): BelongsTo { return $this->belongsTo(\App\Models\Vendor::class); }

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

    public function getStatusLabelAttribute(): string
    {
        return [
            'requested'           => 'Return Requested',
            'approved'            => 'Approved',
            'rejected'            => 'Rejected',
            'pickup_requested'    => 'Pickup Arranged',
            'pickup_received'     => 'Item Received',
            'refund_wallet_queued'=> 'Refund Processing',
            'refund_completed'    => 'Refund Completed',
        ][$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'requested'           => 'warning',
            'approved'            => 'info',
            'rejected'            => 'danger',
            'pickup_requested'    => 'primary',
            'pickup_received'     => 'primary',
            'refund_wallet_queued'=> 'info',
            'refund_completed'    => 'success',
        ][$this->status] ?? 'secondary';
    }
}



