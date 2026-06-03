<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReturnRequestItemSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (ReturnRequestItem::count() > 0) {
            return;
        }

        $returnRequests = ReturnRequest::query()->inRandomOrder()->limit(25)->get();
        if ($returnRequests->isEmpty()) {
            return;
        }

        $products = Product::query()->inRandomOrder()->limit(80)->get();
        if ($products->isEmpty()) {
            return;
        }

        $reasons = ['Damaged', 'Not needed', 'Wrong size', 'Defective', 'Other'];

        foreach ($returnRequests as $rr) {
            $itemCount = rand(1, 3);

            for ($i = 0; $i < $itemCount; $i++) {
                $product = $products[array_rand($products->all())];

                ReturnRequestItem::create([
                    'return_request_id' => $rr->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 2),
                    'reason' => $reasons[array_rand($reasons)],
                ]);
            }
        }
    }
}

