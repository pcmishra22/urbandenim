<?php

namespace Database\Seeders;

use App\Models\ExchangeRequest;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExchangeRequestSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (ExchangeRequest::count() > 0) {
            return;
        }

        $returnRequests = ReturnRequest::query()->inRandomOrder()->limit(20)->get();
        if ($returnRequests->isEmpty()) {
            return;
        }

        $adminIds = User::query()->where('role', 'admin')->pluck('id')->all();
        $statuses = ['requested', 'approved', 'rejected', 'refund_wallet_queued', 'completed'];

        foreach ($returnRequests as $rr) {
            if (rand(0, 100) > 65) {
                continue;
            }

            $approvedByAdminId = empty($adminIds) ? null : $adminIds[array_rand($adminIds)];

            ExchangeRequest::create([
                'return_request_id' => $rr->id,
                'status' => $statuses[array_rand($statuses)],
                'requested_at' => now()->subDays(rand(1, 25)),
                'approved_at' => now()->subDays(rand(0, 20)),
                'rejected_at' => now()->subDays(rand(0, 20)),
                'approved_by_admin_id' => $approvedByAdminId,
                'exchange_wallet_amount' => (float) round(rand(100, 800) / 10, 2),
            ]);
        }
    }
}

