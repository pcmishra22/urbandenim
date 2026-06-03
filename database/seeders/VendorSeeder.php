<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VendorSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (Vendor::count() > 0) {
            return;
        }

        $vendors = [
            ['shop_name' => 'Denim Hub', 'vendor_code' => 'DH', 'is_active' => true],
            ['shop_name' => 'BlueCraft', 'vendor_code' => 'BC', 'is_active' => true],
            ['shop_name' => 'StreetStitch', 'vendor_code' => 'SS', 'is_active' => true],
        ];

        $vendorUsers = User::query()->where('role', 'vendor')->orderBy('id')->get();
        if ($vendorUsers->isEmpty()) {
            return;
        }

        foreach ($vendors as $i => $v) {
            $user = $vendorUsers[$i % $vendorUsers->count()];

            Vendor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'shop_name' => $v['shop_name'],
                    'vendor_code' => $v['vendor_code'] . '-' . Str::upper(Str::random(4)),
                    'approval_status' => 'approved',
                    'rejection_reason' => null,
                    'is_active' => $v['is_active'],
                ]
            );
        }
    }
}

