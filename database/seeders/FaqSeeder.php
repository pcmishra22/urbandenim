<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'How can I determine my size?',
                'answer' => 'Please refer to our Size Guide available on every product page. We provide waist, hip, and length measurements for all fits.',
                'is_active' => true,
            ],
            [
                'question' => 'What is your return policy?',
                'answer' => 'We offer a 30-day return policy for all unworn items with original tags attached.',
                'is_active' => true,
            ],
            [
                'question' => 'How should I wash my UrbanDenim jeans?',
                'answer' => 'To maintain the color and fit, we recommend washing inside out in cold water and hanging to dry.',
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(['question' => $faq['question']], $faq);
        }
    }
}