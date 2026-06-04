<?php

namespace Database\Seeders;

use App\Models\DeliveryCharge;
use App\Models\ShippingRule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryChargeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (DeliveryCharge::count() > 0) {
            return;
        }

        $rules = ShippingRule::query()->where('is_active', true)->get();
        if ($rules->isEmpty()) {
            return;
        }

        foreach ($rules as $rule) {
            // 3 weight tiers
            $tiers = [
                ['from' => 0, 'to' => 1, 'amount' => 499],
                ['from' => 1, 'to' => 3, 'amount' => 699],
                ['from' => 3, 'to' => 10, 'amount' => 999],
            ];

            foreach ($tiers as $t) {
                DeliveryCharge::create([
                    'shipping_rule_id' => $rule->id,
                    'weight_from' => $t['from'],
                    'weight_to' => $t['to'],
                    'charge_amount' => $t['amount'],
                    'currency' => 'USD',
                    'is_active' => true,
                ]);
            }
        }
    }
}

