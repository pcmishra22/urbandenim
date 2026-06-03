<?php

namespace Database\Seeders;

use App\Models\ShippingRule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingRuleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (ShippingRule::count() > 0) {
            return;
        }

        $rules = [
            ['country' => 'US', 'region' => null, 'service_level' => 'standard', 'base_days' => 3, 'extra_days' => 0],
            ['country' => 'US', 'region' => 'CA', 'service_level' => 'standard', 'base_days' => 4, 'extra_days' => 0],
            ['country' => 'IN', 'region' => null, 'service_level' => 'standard', 'base_days' => 5, 'extra_days' => 1],
            ['country' => 'UK', 'region' => null, 'service_level' => 'express', 'base_days' => 2, 'extra_days' => 0],
        ];

        foreach ($rules as $r) {
            ShippingRule::create([
                'country' => $r['country'],
                'region' => $r['region'],
                'service_level' => $r['service_level'],
                'base_days' => $r['base_days'],
                'extra_days' => $r['extra_days'],
                'is_active' => true,
            ]);
        }
    }
}

