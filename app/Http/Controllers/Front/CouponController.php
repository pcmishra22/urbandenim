<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\CouponService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request, CouponService $couponService)
    {
        $code    = strtoupper(trim($request->input('coupon_code', '')));
        $subtotal = (float) $request->input('subtotal', 0);

        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Please enter a coupon code.']);
        }

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$coupon->isValidForUser(auth()->user())) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired coupon code.']);
        }

        $discount = $couponService->calculateDiscount($coupon, $subtotal);

        session(['coupon_code' => $code, 'coupon_discount' => $discount]);

        return response()->json([
            'success'  => true,
            'message'  => "Coupon applied! You save ₹" . number_format($discount, 2),
            'discount' => $discount,
        ]);
    }

    public function remove()
    {
        session()->forget(['coupon_code', 'coupon_discount']);
        return back()->with('success', 'Coupon removed.');
    }
}
