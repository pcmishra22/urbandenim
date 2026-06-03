<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CouponSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (Coupon::count() > 0) {
            return;
        }

        $customerIds = User::query()->where('role', 'customer')->pluck('id')->all();

        $now = now();
        $templates = [
            ['type' => 'flat', 'value' => 200, 'usage_limit' => 50, 'expires_days' => 60],
            ['type' => 'flat', 'value' => 500, 'usage_limit' => 30, 'expires_days' => 45],
            ['type' => 'percentage', 'value' => 10, 'usage_limit' => 100, 'expires_days' => 30],
            ['type' => 'percentage', 'value' => 15, 'usage_limit' => 80, 'expires_days' => 90],
            ['type' => 'free_shipping', 'value' => null, 'usage_limit' => 70, 'expires_days' => 60],
        ];

        foreach ($templates as $idx => $tpl) {
            $couponCode = Str::upper(Str::slug('URBAN-' . $tpl['type'] . '-' . ($idx + 1) . '-' . Str::random(6), '-'));

            $assignedUserId = null;
            if (!empty($customerIds) && rand(0, 100) < 40) {
                $assignedUserId = $customerIds[array_rand($customerIds)];
            }

            Coupon::create([
                'code' => $couponCode,
                'type' => $tpl['type'],
                'value' => $tpl['value'],
                'usage_limit' => $tpl['usage_limit'],
                'used_count' => rand(0, min(10, $tpl['usage_limit'] ?? 10)),
                'expires_at' => $now->copy()->addDays($tpl['expires_days']),
                'user_id' => $assignedUserId,
                'is_active' => true,
            ]);
        }
    }
}

