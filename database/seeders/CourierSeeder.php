<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourierSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (Courier::count() > 0) {
            return;
        }

        $couriers = [
            ['name' => 'UrbanXpress', 'code' => 'URX'],
            ['name' => 'BlueShip', 'code' => 'BLU'],
            ['name' => 'DenimExpress', 'code' => 'DNX'],
        ];

        foreach ($couriers as $c) {
            Courier::create([
                'name' => $c['name'],
                'code' => $c['code'] . '-' . Str::upper(Str::random(3)),
                'is_active' => true,
            ]);
        }
    }
}

