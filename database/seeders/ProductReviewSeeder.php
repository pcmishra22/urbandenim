<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProductReviewSeeder extends Seeder
{
    /** Exact product IDs on live site */
    private const PRODUCT_IDS = [
        209,212,213,215,217,218,220,221,222,223,224,225,226,
        227,228,229,230,231,232,233,234,235,236,237,238,239,
        241,242,243,244,
    ];

    private const REVIEWERS = [
        ['name' => 'Rahul Sharma',   'email' => 'rahul.sharma.jz@gmail.com'],
        ['name' => 'Priya Mehta',    'email' => 'priya.mehta.jz@gmail.com'],
        ['name' => 'Arjun Singh',    'email' => 'arjun.singh.jz@gmail.com'],
        ['name' => 'Neha Gupta',     'email' => 'neha.gupta.jz@gmail.com'],
        ['name' => 'Vikram Patel',   'email' => 'vikram.patel.jz@gmail.com'],
        ['name' => 'Anjali Verma',   'email' => 'anjali.verma.jz@gmail.com'],
        ['name' => 'Rohit Kumar',    'email' => 'rohit.kumar.jz@gmail.com'],
        ['name' => 'Sneha Reddy',    'email' => 'sneha.reddy.jz@gmail.com'],
        ['name' => 'Amit Joshi',     'email' => 'amit.joshi.jz@gmail.com'],
        ['name' => 'Kavya Nair',     'email' => 'kavya.nair.jz@gmail.com'],
        ['name' => 'Suresh Yadav',   'email' => 'suresh.yadav.jz@gmail.com'],
        ['name' => 'Pooja Agarwal',  'email' => 'pooja.agarwal.jz@gmail.com'],
        ['name' => 'Deepak Tiwari',  'email' => 'deepak.tiwari.jz@gmail.com'],
        ['name' => 'Riya Kapoor',    'email' => 'riya.kapoor.jz@gmail.com'],
        ['name' => 'Manish Chauhan', 'email' => 'manish.chauhan.jz@gmail.com'],
        ['name' => 'Simran Kaur',    'email' => 'simran.kaur.jz@gmail.com'],
        ['name' => 'Aakash Mishra',  'email' => 'aakash.mishra.jz@gmail.com'],
        ['name' => 'Divya Pillai',   'email' => 'divya.pillai.jz@gmail.com'],
        ['name' => 'Karan Malhotra', 'email' => 'karan.malhotra.jz@gmail.com'],
        ['name' => 'Meera Iyer',     'email' => 'meera.iyer.jz@gmail.com'],
        ['name' => 'Tarun Bhatia',   'email' => 'tarun.bhatia.jz@gmail.com'],
        ['name' => 'Ishaan Saxena',  'email' => 'ishaan.saxena.jz@gmail.com'],
        ['name' => 'Ananya Das',     'email' => 'ananya.das.jz@gmail.com'],
        ['name' => 'Yash Dubey',     'email' => 'yash.dubey.jz@gmail.com'],
        ['name' => 'Shruti Pandey',  'email' => 'shruti.pandey.jz@gmail.com'],
        ['name' => 'Nikhil Rao',     'email' => 'nikhil.rao.jz@gmail.com'],
        ['name' => 'Aditi Bhatt',    'email' => 'aditi.bhatt.jz@gmail.com'],
        ['name' => 'Siddharth Bose', 'email' => 'siddharth.bose.jz@gmail.com'],
        ['name' => 'Kritika Sinha',  'email' => 'kritika.sinha.jz@gmail.com'],
        ['name' => 'Gaurav Thakur',  'email' => 'gaurav.thakur.jz@gmail.com'],
    ];

    private const REVIEWS = [
        5 => [
            "Absolutely love these jeans! The fit is perfect and the denim quality is outstanding. Got so many compliments on day one. Definitely ordering more pairs.",
            "Best jeans I have bought online so far. The fabric feels premium, stitching is solid and the colour hasn't faded after 4 washes. Highly recommend Jeanzo!",
            "Ordered size 32 and it fits perfectly. The stretch is just right — comfortable all day without losing shape. Delivery was super fast too!",
            "Amazing quality for the price. The denim is thick yet comfortable, and the slim fit is exactly as shown in the photos. 5 stars without hesitation.",
            "Finally found a brand that gets Indian body types right. These jeans fit beautifully at the waist and thighs. Packaging was also very neat.",
            "Ordered for my husband and he absolutely loves them. The dark wash looks really premium and the fabric is soft yet sturdy. Will definitely buy again.",
            "This is my third order from Jeanzo and every time the quality has been consistent. The stretch denim is perfect for long working days. Love it!",
            "Great product! The jeans look exactly like the pictures. Size guide was accurate. Shipped in 3 days. Very happy with the purchase.",
            "Superb quality jeans at a very reasonable price. The stitching is clean and the fit is great. Much better than what I expected. Jeanzo fan now!",
            "Bought this as a gift and the recipient was thrilled. Premium denim feel, great packaging. Will shop again for sure.",
            "The colour is exactly as shown and the fabric quality is top notch. Slim fit is perfect without being too tight. Worth every rupee!",
            "Received within 4 days. The denim feels premium and the stitching is very neat. Waistband is comfortable even after sitting for hours. Highly recommended.",
        ],
        4 => [
            "Really good jeans overall. Quality is great and fit is comfortable. Slightly darker shade than what appeared on screen but looks even better in person.",
            "Very nice quality jeans. The denim is sturdy and well-stitched. Only minor issue is the waist runs slightly snug — recommend sizing up if between sizes.",
            "Good product for the price. Looks premium and fits well. Delivery took 5 days which was fine. Would buy again.",
            "Comfortable and stylish. The fabric quality is better than expected. Colour is rich and even after washing it looks great.",
            "Nice fitting jeans with good denim quality. The slim cut is perfect for a formal-casual look. Packaging could be better but product is solid.",
            "These jeans are a great buy. Comfortable, good quality denim and decent price. Just took slightly longer to deliver than expected but worth the wait.",
            "Pretty happy with this purchase. The stitching is neat and the fabric feels premium. Fits true to size.",
            "Good value for money. The jeans look stylish and feel comfortable. The stretch fabric is a bonus.",
            "Solid quality jeans. Fits well and looks great for both casual and office wear. Delivery was prompt.",
            "Happy with the product overall. The denim thickness is just right and the fit is good. Colour matches the website photos accurately.",
            "Very comfortable for all-day wear. The fabric is breathable and does not feel heavy. Good purchase overall.",
        ],
        3 => [
            "Decent jeans but nothing extraordinary. Quality is okay for the price. Fit is average — a bit loose around the thighs.",
            "Product is fine. The denim quality is average — not bad but not premium either. Fit is comfortable but runs a size larger than expected.",
            "Average experience. The jeans are decent quality but I expected better stitching for this price.",
            "It's okay. The colour is slightly different from what was shown. Comfort is decent but wouldn't say it's premium quality.",
            "Mixed feelings. The fabric feels okay but the waist sizing was slightly off. Customer service was helpful when I reached out.",
        ],
    ];

    /**
     * Each product gets a RANDOM number of reviews between 3 and 7,
     * with ratings weighted toward 4–5 stars.
     */
    public function run(): void
    {
        // ── 1. Create reviewer accounts ───────────────────────────────────────
        $userIds = [];
        foreach (self::REVIEWERS as $r) {
            $user = User::firstOrCreate(
                ['email' => $r['email']],
                [
                    'name'              => $r['name'],
                    'password'          => Hash::make('Jeanzo@2025'),
                    'role'              => 'customer',
                    'email_verified_at' => now(),
                ]
            );
            $userIds[] = $user->id;
        }
        $this->command->info('Reviewer accounts ready: ' . count($userIds));

        // ── 2. Seed reviews ───────────────────────────────────────────────────
        $inserted = 0;
        $skipped  = 0;

        // Weighted rating pool: mostly 4s and 5s, occasional 3
        $ratingPool = [5,5,5,5,5,4.5,4.7,4.8,4.9];

        foreach (self::PRODUCT_IDS as $productId) {
            // Skip if already has reviews
            $existing = DB::table('product_reviews')
                ->where('product_id', $productId)
                ->count();

            if ($existing > 0) {
                $this->command->line("  <fg=gray>SKIP (already has {$existing} reviews)</> product #{$productId}");
                $skipped++;
                continue;
            }

            // Random review count between 3 and 7
            $reviewCount = rand(3, 7);

            // Shuffle reviewers and pick $reviewCount unique ones
            $shuffled    = $userIds;
            shuffle($shuffled);
            $reviewerIds = array_slice($shuffled, 0, $reviewCount);

            // Spread review dates over last 120 days
            $ratings = [];
            $rows    = [];

            for ($i = 0; $i < $reviewCount; $i++) {
                $rating = $ratingPool[array_rand($ratingPool)];
                $texts  = self::REVIEWS[$rating];
                $text   = $texts[array_rand($texts)];
                $date   = now()->subDays(rand(1, 120))->subHours(rand(0, 23));

                $rows[] = [
                    'product_id'     => $productId,
                    'user_id'        => $reviewerIds[$i],
                    'rating'         => $rating,
                    'review_text'    => $text,
                    'status'         => 'approved',
                    'is_approved'    => true,
                    'is_spam'        => false,
                    'is_featured'    => ($i === 0),
                    'spam_score'     => 0,
                    'reported_count' => 0,
                    'created_at'     => $date,
                    'updated_at'     => $date,
                ];
                $ratings[] = $rating;
            }

            DB::table('product_reviews')->insert($rows);
            $inserted += $reviewCount;

            $avg = number_format(array_sum($ratings) / count($ratings), 1);
            $this->command->info("  ✓ product #{$productId} — {$reviewCount} reviews, avg rating {$avg} (" . implode(',', $ratings) . ")");
        }

        $this->command->newLine();
        $this->command->info("Done! {$inserted} reviews inserted. {$skipped} products skipped (already had reviews).");
    }
}
