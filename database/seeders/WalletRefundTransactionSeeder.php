<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Models\WalletRefundTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletRefundTransactionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (WalletRefundTransaction::count() > 0) {
            return;
        }

        $users = User::query()->whereIn('role', ['customer'])->pluck('id')->all();
        $orders = Order::query()->inRandomOrder()->limit(30)->get();
        if (empty($users) || $orders->isEmpty()) {
            return;
        }

        $returnRequests = ReturnRequest::query()->pluck('id')->all();

        $statuses = ['queued', 'completed', 'failed'];

        foreach ($orders as $order) {
            $userId = $users[array_rand($users)];
            $returnRequestId = empty($returnRequests) ? null : $returnRequests[array_rand($returnRequests)];

            // Create some transactions
            if (rand(0, 100) > 55) {
                continue;
            }

            WalletRefundTransaction::create([
                'user_id' => $userId,
                'order_id' => $order->id,
                'return_request_id' => $returnRequestId,
                'type' => 'credit',
                'amount' => (float) round(max(0, (float) $order->total_price) * (0.1 + mt_rand(0, 60) / 100), 2),
                'currency' => 'USD',
                'status' => $statuses[array_rand($statuses)],
                'meta' => [
                    'source' => 'seed',
                    'note' => 'Refund transaction seed row',
                ],
                'created_by_admin_id' => null,
            ]);
        }
    }
}

