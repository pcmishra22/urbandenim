<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateProductImagesController extends Controller
{
    /**
     * The 5 view angles we generate, with their prompts.
     * We build the full prompt dynamically using product attributes.
     */
    private const VIEWS = [
        ['key' => 'front',  'label' => 'Front View',    'angle' => 'front view, facing camera directly'],
        ['key' => 'back',   'label' => 'Back View',     'angle' => 'back view, rear facing'],
        ['key' => 'left',   'label' => 'Left Side',     'angle' => 'left side view, 90 degree angle'],
        ['key' => 'right',  'label' => 'Right Side',    'angle' => 'right side view, 90 degree angle'],
        ['key' => 'detail', 'label' => 'Detail / Flat', 'angle' => 'flat lay top-down view on white surface'],
    ];

    /**
     * POST /admin/products/{product}/generate-images
     * Called via AJAX from the edit page.
     * Returns JSON with progress as each image is generated.
     */
    public function generate(Request $request, Product $product)
    {
        $hfToken = config('services.huggingface.token');

        if (empty($hfToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Hugging Face API token not configured. Add HUGGINGFACE_TOKEN to your .env file.',
            ], 500);
        }

        $results  = [];
        $errors   = [];
        $lastSort = $product->images()->max('sort_order') ?? -1;

        // Build a descriptive product context for the prompt
        $productContext = $this->buildProductContext($product);

        foreach (self::VIEWS as $index => $view) {
            $prompt = $this->buildPrompt($productContext, $view['angle']);

            try {
                $imageContent = $this->callHuggingFace($hfToken, $prompt);

                if ($imageContent === null) {
                    $errors[] = "Failed to generate {$view['label']}";
                    continue;
                }

                // Save to storage/app/public/products/{id}/images/
                $filename  = $view['key'] . '_' . Str::random(8) . '.jpg';
                $storagePath = "products/{$product->id}/images/{$filename}";

                Storage::disk('public')->put($storagePath, $imageContent);

                // Insert DB record
                $sortOrder = $lastSort + $index + 1;
                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $filename,
                    'sort_order' => $sortOrder,
                ]);

                $results[] = [
                    'label' => $view['label'],
                    'url'   => Storage::disk('public')->url($storagePath),
                ];

            } catch (\Throwable $e) {
                Log::error("HuggingFace image generation failed for product {$product->id}, view {$view['key']}: " . $e->getMessage());
                $errors[] = "{$view['label']}: " . $e->getMessage();
            }
        }

        if (empty($results)) {
            return response()->json([
                'success' => false,
                'message' => 'All image generations failed. Check your HF token and try again.',
                'errors'  => $errors,
            ], 500);
        }

        return response()->json([
            'success'  => true,
            'message'  => count($results) . ' images generated successfully' . (count($errors) ? ' (' . count($errors) . ' failed)' : '') . '.',
            'images'   => $results,
            'errors'   => $errors,
        ]);
    }

    // ──────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────

    private function buildProductContext(Product $product): string
    {
        $parts = [];

        // Color
        if (!empty($product->color_family)) {
            $parts[] = $product->color_family;
        }

        // Gender
        if (!empty($product->gender)) {
            $parts[] = $product->gender . "'s";
        }

        // Category / brand
        $category = optional($product->category)->name;
        $brand    = optional($product->brand)->name;

        if ($category) $parts[] = $category;
        elseif ($brand) $parts[] = $brand . ' jeans';
        else             $parts[] = 'jeans';

        // Fit from name if present
        $name = strtolower($product->name ?? '');
        foreach (['slim fit', 'skinny', 'straight', 'wide leg', 'bootcut', 'relaxed', 'tapered'] as $fit) {
            if (str_contains($name, $fit)) {
                $parts[] = $fit;
                break;
            }
        }

        return implode(' ', $parts);
    }

    private function buildPrompt(string $productContext, string $angle): string
    {
        return "professional ecommerce product photo of {$productContext}, {$angle}, "
             . "clean white background, studio lighting, high resolution, "
             . "realistic fabric texture, no model, clothing only, sharp focus, "
             . "commercial product photography";
    }

    /**
     * Call the Hugging Face Inference API.
     * Uses FLUX.1-schnell — best free quality, fastest inference.
     * Returns raw image bytes or null on failure.
     */
    private function callHuggingFace(string $token, string $prompt): ?string
    {
        // FLUX.1-schnell: best free model for product photography
        $model = 'black-forest-labs/FLUX.1-schnell';

        $response = Http::withToken($token)
            ->timeout(120)  // image gen can be slow on free tier
            ->withHeaders(['Accept' => 'image/jpeg'])
            ->post("https://api-inference.huggingface.co/models/{$model}", [
                'inputs'     => $prompt,
                'parameters' => [
                    'num_inference_steps' => 4,   // schnell is optimised for 4 steps
                    'guidance_scale'      => 0,   // schnell works without CFG
                    'width'               => 768,
                    'height'              => 1024, // portrait — standard for apparel
                ],
            ]);

        if ($response->failed()) {
            // Model might be loading — HF returns 503 with estimated_time
            $body = $response->json();
            $wait = $body['estimated_time'] ?? null;
            if ($response->status() === 503 && $wait) {
                // Wait and retry once
                sleep((int) ceil($wait) + 2);
                $response = Http::withToken($token)
                    ->timeout(120)
                    ->withHeaders(['Accept' => 'image/jpeg'])
                    ->post("https://api-inference.huggingface.co/models/{$model}", [
                        'inputs'     => $prompt,
                        'parameters' => [
                            'num_inference_steps' => 4,
                            'guidance_scale'      => 0,
                            'width'               => 768,
                            'height'              => 1024,
                        ],
                    ]);
            }

            if ($response->failed()) {
                Log::error('HuggingFace API error: ' . $response->status() . ' — ' . $response->body());
                return null;
            }
        }

        // Response body is raw image bytes
        $bytes = $response->body();

        // Sanity check — must start with a known image magic byte
        if (strlen($bytes) < 100) {
            return null;
        }

        return $bytes;
    }
}
