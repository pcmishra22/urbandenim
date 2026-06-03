<?php

namespace Database\Seeders;

use App\Models\ExchangeRequest;
use App\Models\ExchangeRequestItem;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExchangeRequestItemSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (ExchangeRequestItem::count() > 0) {
            return;
        }

        $exchangeRequests = ExchangeRequest::query()->inRandomOrder()->limit(20)->get();
        if ($exchangeRequests->isEmpty()) {
            return;
        }

        $products = Product::query()->inRandomOrder()->limit(80)->get();
        if ($products->isEmpty()) {
            return;
        }

        $reasons = ['Exchange required', 'Wrong variant', 'Damaged', 'Other'];

        foreach ($exchangeRequests as $er) {
            $itemCount = rand(1, 2);

            for ($i = 0; $i < $itemCount; $i++) {
                $product = $products[array_rand($products->all())];

                ExchangeRequestItem::create([
                    'exchange_request_id' => $er->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 2),
                    'reason' => $reasons[array_rand($reasons)],
                ]);
            }
        }
    }
}

