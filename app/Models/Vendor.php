<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model

{
    protected $fillable = [
        'user_id',
        'shop_name',
        'vendor_code',
        'approval_status',
        'rejection_reason',
        'is_active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'vendor_id');
    }


    public function kycDocuments(): HasMany
    {
        return $this->hasMany(VendorKycDocument::class);
    }

    public function wallet(): HasMany
    {
        return $this->hasMany(VendorWallet::class);
    }

    public function commissionRule(): HasMany
    {
        return $this->hasMany(VendorCommissionRule::class);
    }


    public function performanceMetrics(): HasMany
    {
        return $this->hasMany(VendorPerformanceMetric::class);
    }

    public function settlementReports(): HasMany
    {
        return $this->hasMany(VendorSettlementReport::class);
    }
}

