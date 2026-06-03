<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'exchange_request_id',
        'product_id',
        'quantity',
        'reason',
    ];

    public function exchangeRequest(): BelongsTo
    {
        return $this->belongsTo(ExchangeRequest::class, 'exchange_request_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

