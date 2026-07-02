<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class GenerateProductContent extends Command
{
    /**
     * php artisan products:generate-content
     *      → fills only EMPTY fields, for all active products
     *
     * php artisan products:generate-content --id=209
     *      → just one product
     *
     * php artisan products:generate-content --force
     *      → regenerate meta_title / meta_description / color /
     *        short_description / description even if already set
     *        (model_info / fabric_info / fabric_weight / wash /
     *        waist_rise / fit_type / stretch are never touched by
     *        --force if they already have a real value — those are
     *        treated as curated data, not auto-fill data)
     *
     * php artisan products:generate-content --dry-run
     *      → preview only, nothing is saved
     */
    protected $signature = 'products:generate-content
                            {--id= : Only process a single product ID}
                            {--force : Regenerate SEO/description fields even if already set}
                            {--dry-run : Preview changes without saving}';

    protected $description = 'Fill meta title/description, model/fabric/wash info, fit type, color and descriptions from the product name';

    private const WASH_STYLES = [
        'Stone Washed', 'Stone-Washed', 'Stone Wash', 'Stone-Wash',
        'Dark Washed', 'Dark-Washed', 'Faded', 'Light Washed', 'Light-Washed',
    ];

    // Style-specific keywords are checked before generic fit adjectives,
    // so "Relaxed Fit Wide-Leg Palazzo Style" resolves to "Palazzo", not "Relaxed Fit".
    private const FIT_ORDER = [
        'palazzo style'  => 'Palazzo',
        'baggy style'    => 'Baggy Fit',
        'bootcut'        => 'Bootcut',
        'skin fit'       => 'Skin Fit',
        'slim fit'       => 'Slim Fit',
        'straight fit'   => 'Straight Fit',
        'regular fit'    => 'Regular Fit',
        'relaxed fit'    => 'Relaxed Fit',
        'wide-leg'       => 'Wide-Leg',
        'wide leg'       => 'Wide-Leg',
    ];

    private const STRIP_WORDS = [
        "Women's", 'Women', "Men's", 'Men', 'with Embroidery',
        'with Front Seam Stitch Detail', 'Casual Denim Jeans', 'Denim Jeans',
        'Jeans', 'Slim Fit', 'Straight Fit', 'Regular Fit', 'Relaxed Fit',
        'Skin Fit', 'Bootcut', 'Wide-Leg', 'Wide Leg', 'Palazzo Style',
        'Baggy Style', 'Straight-Leg', 'High-Rise', 'High Rise',
        'Stone Washed', 'Stone-Washed', 'Stone Wash', 'Stone-Wash',
        'Dark Washed', 'Dark-Washed', 'Faded', 'Light Washed', 'Light-Washed',
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $force  = (bool) $this->option('force');

        $query = Product::query();
        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }
        $products = $query->get();

        if ($products->isEmpty()) {
            $this->warn('No matching products found.');
            return self::SUCCESS;
        }

        $updated = 0;

        foreach ($products as $product) {
            $data = $this->buildFields($product, $force);

            if (empty($data)) {
                continue;
            }

            $this->line("[{$product->id}] {$product->name}");
            foreach ($data as $field => $value) {
                $preview = strlen($value) > 80 ? substr($value, 0, 80) . '…' : $value;
                $this->line("   <comment>{$field}</comment>: {$preview}");
            }

            if (!$dryRun) {
                $product->update($data);
            }

            $updated++;
        }

        $this->newLine();
        $this->info(
            $dryRun
                ? "Dry run — {$updated} product(s) would be updated."
                : "Done — {$updated} product(s) updated."
        );

        return self::SUCCESS;
    }

    /**
     * Build the array of fields to update for a single product.
     * Returns [] if nothing needs changing.
     */
    private function buildFields(Product $product, bool $force): array
    {
        $name        = $product->name;
        $gender      = $product->gender ?: 'unisex';
        $genderLabel = match ($gender) {
            'men'   => "Men's",
            'women' => "Women's",
            'girls' => "Girls'",
            'boys'  => "Boys'",
            default => '',
        };

        $fitLabel  = $product->fit_type ?: $this->fitLabelFromName($name);
        $colorVal  = $product->color ?: $this->extractColor($name, $product->color_family ?: 'Multi');
        $washStyle = $this->extractWashStyle($name);

        // ---- fields only filled if currently empty (curated data wins) ----
        $modelInfo = $product->model_info ?: (
            $gender === 'men'
                ? 'Model is 5\'9", wearing size 32 — VERIFY & UPDATE with real measurements'
                : 'Model is 5\'5", wearing size 28 — VERIFY & UPDATE with real measurements'
        );

        $fabricInfo = $product->fabric_info ?: '98% Cotton, 2% Elastane — Stretch';

        if (!empty($product->stretch) && strtolower(trim($product->stretch)) !== 'no stretch') {
            $stretch = $product->stretch;
        } else {
            $stretch = 'Stretch';
        }

        $fabricWeight = $product->fabric_weight ?: '12 oz Denim';
        $wash         = $product->wash ?: 'Machine wash cold, inside out. Do not bleach. Line dry in shade.';
        $waistRise    = $product->waist_rise ?: ($gender === 'men' ? 'Mid Rise' : 'High Rise');
        $fitType      = $product->fit_type ?: $fitLabel;

        $isStretch = $stretch && stripos($stretch, 'stretch') !== false && stripos($stretch, 'no stretch') === false;
        $stretchBit = $isStretch ? 'stretch comfort fit' : 'everyday comfort fit';

        // ---- meta_title ----
        $core = strlen($name) <= 50 ? $name : trim("{$genderLabel} {$fitLabel} {$colorVal} Jeans");
        $metaTitle = "{$core} | Jeanzo";
        if (strlen($metaTitle) > 65) {
            $metaTitle = trim("{$genderLabel} {$fitLabel} {$colorVal} Denim Jeans | Jeanzo");
        }

        // ---- meta_description ----
        $washBit = $washStyle ? strtolower($washStyle) . ' finish, ' : '';
        $metaDescription = "Shop {$genderLabel} {$fitLabel} {$colorVal} denim jeans online at Jeanzo. "
            . "{$washBit}{$stretchBit}, durable denim built for daily wear. "
            . "COD available, easy returns, fast shipping across India.";
        $metaDescription = preg_replace('/\s+/', ' ', trim($metaDescription));
        if (strlen($metaDescription) > 160) {
            $metaDescription = "Shop {$genderLabel} {$fitLabel} {$colorVal} jeans online at Jeanzo. "
                . ucfirst($stretchBit) . ", quality denim. COD available, easy returns.";
        }

        $canonicalUrl = 'https://jeanzo.in/' . $product->slug;

        // ---- short_description / description ----
        $shortDescription = trim("{$genderLabel} {$fitLabel} {$colorVal} denim jeans — {$stretchBit}, made for everyday wear.");
        $shortDescription = preg_replace('/\s+/', ' ', $shortDescription);

        $washSentence = $washStyle
            ? " Finished with a " . strtolower($washStyle) . " wash for an authentic, worn-in look."
            : '';
        $fwLower  = strtolower($fabricWeight);
        $fwPhrase = str_contains($fwLower, 'denim') ? $fwLower : "{$fwLower} denim";
        $description = "Step out in the {$name}. This " . strtolower($fitLabel) . " silhouette sits at a "
            . strtolower($waistRise) . " and pairs the classic denim look with a "
            . ($isStretch ? 'stretch-comfort' : 'sturdy, structured') . " feel that moves with you all day."
            . "{$washSentence} Made from {$fwPhrase}, it's built to hold its shape wash after wash. "
            . "Pair it with a plain tee for a casual day out or dress it up with a shirt for a smart-casual look.";
        $description = preg_replace('/\s+/', ' ', trim($description));

        // ---- assemble: always-regenerate fields vs fill-if-empty fields ----
        $data = [];

        $alwaysRegen = [
            'meta_title'        => $metaTitle,
            'meta_description'  => $metaDescription,
            'canonical_url'     => $canonicalUrl,
            'color'             => $colorVal,
            'short_description' => $shortDescription,
            'description'       => $description,
        ];
        foreach ($alwaysRegen as $field => $value) {
            $current = $product->{$field};
            $isThin  = empty($current) || $current === $name; // duplicate-of-name = thin content
            if ($force || $isThin) {
                $data[$field] = $value;
            }
        }

        $fillIfEmpty = [
            'model_info'    => $modelInfo,
            'fabric_info'   => $fabricInfo,
            'fabric_weight' => $fabricWeight,
            'wash'          => $wash,
            'waist_rise'    => $waistRise,
            'fit_type'      => $fitType,
            'stretch'       => $stretch,
        ];
        foreach ($fillIfEmpty as $field => $value) {
            $current = $product->{$field};

            // Self-heal: an earlier version of this command wrote a wrong
            // "100% Cotton — No Stretch" placeholder for fabric_info/stretch
            // on products that are actually 98% cotton / 2% elastane and
            // stretchable. Overwrite that specific stale placeholder even
            // though the field is technically non-empty; leave any other
            // real/curated value untouched.
            $isStalePlaceholder = false;
            if ($field === 'fabric_info' && $current && str_contains($current, 'VERIFY actual fabric label')) {
                $isStalePlaceholder = true;
            }
            if ($field === 'stretch' && $current && strtolower(trim($current)) === 'no stretch') {
                $isStalePlaceholder = true;
            }

            if (empty($current) || $isStalePlaceholder) {
                $data[$field] = $value;
            }
        }

        return $data;
    }

    private function extractWashStyle(string $name): ?string
    {
        foreach (self::WASH_STYLES as $w) {
            if (stripos($name, $w) !== false) {
                return $w;
            }
        }
        return null;
    }

    private function fitLabelFromName(string $name): string
    {
        $lower = strtolower($name);
        foreach (self::FIT_ORDER as $needle => $label) {
            if (str_contains($lower, $needle)) {
                return $label;
            }
        }
        return 'Regular Fit';
    }

    private function extractColor(string $name, string $fallbackFamily): string
    {
        $n = $name;
        foreach (self::STRIP_WORDS as $w) {
            $n = preg_replace('/' . preg_quote($w, '/') . '/i', '', $n);
        }
        $n = trim(preg_replace('/\s+/', ' ', $n), " -");
        return $n !== '' ? $n : ucfirst($fallbackFamily);
    }
}
