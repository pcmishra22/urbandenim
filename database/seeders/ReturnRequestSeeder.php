<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReturnRequestSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (ReturnRequest::count() > 0) {
            return;
        }

        $customerIds = User::query()->where('role', 'customer')->pluck('id')->all();
        $adminIds = User::query()->where('role', 'admin')->pluck('id')->all();

        $orders = Order::query()->inRandomOrder()->limit(25)->get();
        if ($orders->isEmpty() || empty($customerIds)) {
            return;
        }

        $statuses = ['requested', 'approved', 'rejected', 'pickup_requested', 'pickup_received', 'refund_wallet_queued', 'refund_completed'];
        $reasons = [
            'Size not as expected',
            'Item arrived damaged',
            'Wrong item delivered',
            'Changed mind',
            'Quality not as expected',
        ];

        foreach ($orders as $order) {
            $userId = $customerIds[array_rand($customerIds)];
            $approvedByAdminId = empty($adminIds) ? null : $adminIds[array_rand($adminIds)];

            ReturnRequest::create([
                'order_id' => $order->id,
                'user_id' => $userId,
                'reason' => $reasons[array_rand($reasons)],
                'status' => $statuses[array_rand($statuses)],
                'requested_at' => now()->subDays(rand(1, 30)),
                'approved_at' => now()->subDays(rand(0, 20)),
                'rejected_at' => now()->subDays(rand(0, 20)),
                'approved_by_admin_id' => $approvedByAdminId,
            ]);
        }
    }
}

