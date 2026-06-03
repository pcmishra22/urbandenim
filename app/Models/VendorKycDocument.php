<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorKycDocument extends Model
{
    protected $fillable = [
        'vendor_id',
        'document_type',
        'file_path',
        'submitted_at',
        'verified_at',
        'verifier_id',
        'verification_status',
        'rejection_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }
}

