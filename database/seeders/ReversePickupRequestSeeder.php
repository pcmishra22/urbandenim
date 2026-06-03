<?php

namespace Database\Seeders;

use App\Models\Courier;
use App\Models\ReversePickupRequest;
use App\Models\ReturnRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReversePickupRequestSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (ReversePickupRequest::count() > 0) {
            return;
        }

        $returnRequests = ReturnRequest::query()->inRandomOrder()->limit(20)->get();
        if ($returnRequests->isEmpty()) {
            return;
        }

        $couriers = Courier::query()->where('is_active', true)->pluck('id')->all();

        $statuses = ['requested', 'picked_up', 'received', 'cancelled'];

        foreach ($returnRequests as $rr) {
            if (rand(0, 100) > 70) {
                continue;
            }

            $courierId = empty($couriers) ? null : $couriers[array_rand($couriers)];

            ReversePickupRequest::create([
                'return_request_id' => $rr->id,
                'status' => $statuses[array_rand($statuses)],
                'requested_at' => now()->subDays(rand(1, 25)),
                'picked_up_at' => now()->subDays(rand(0, 15)),
                'received_at' => now()->subDays(rand(0, 10)),
                'courier_id' => $courierId,
                'tracking_id' => 'RP-' . strtoupper(bin2hex(random_bytes(4))),
            ]);
        }
    }
}

