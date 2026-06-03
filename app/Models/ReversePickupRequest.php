<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReversePickupRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'status',
        'requested_at',
        'picked_up_at',
        'received_at',
        'courier_id',
        'tracking_id',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class, 'return_request_id');
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class, 'courier_id');
    }
}

