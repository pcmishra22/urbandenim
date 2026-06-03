<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;

class CouponService
{
    public function validateAndCalculate(string $code, float $total, ?User $user = null): ?float
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon || !$coupon->isValidForUser($user)) {
            return null;
        }

        return $this->calculateDiscount($coupon, $total);
    }

    public function calculateDiscount(Coupon $coupon, float $total): float
    {
        return match ($coupon->type) {
            'percentage' => $total * ($coupon->value / 100),
            'flat' => (float) $coupon->value,
            'free_shipping' => 0.0, 
            default => 0.0,
        };
    }

    public function applyCoupon(Coupon $coupon): void
    {
        $coupon->increment('used_count');
    }
}