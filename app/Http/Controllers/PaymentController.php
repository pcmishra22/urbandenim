<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Front\CheckoutController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cashfree Payment Gateway — pure HTTP implementation (no SDK dependency).
 *
 * Uses Laravel's Http client (Guzzle wrapper) directly against Cashfree REST API.
 * This avoids any composer package issues with the cashfree/cashfree-pg SDK.
 *
 * Flow:
 *  1. POST /payment/create-order  → create Cashfree order → return payment_session_id
 *  2. Frontend Cashfree JS SDK calls cashfree.checkout({ paymentSessionId })
 *  3. Cashfree GET-redirects to /payment/verify?order_id={cf_order_id}
 *  4. verify() calls GET /pg/orders/{cf_order_id} to confirm PAID status
 *  5. POST /payment/webhook — server-to-server backup (CSRF-exempt)
 *
 * .env keys required:
 *   CASHFREE_APP_ID=your_app_id
 *   CASHFREE_SECRET_KEY=your_secret_key
 *   CASHFREE_ENV=sandbox          ← change to production when going live
 */
class PaymentController extends Controller
{
    private const API_VERSION = '2022-09-01';

    private function baseUrl(): string
    {
        $env = config('services.cashfree.env', 'sandbox');
        return $env === 'production'
            ? 'https://api.cashfree.com/pg'
            : 'https://sandbox.cashfree.com/pg';
    }

    private function headers(): array
    {
        return [
            'x-client-id'     => config('services.cashfree.app_id'),
            'x-client-secret' => config('services.cashfree.secret_key'),
            'x-api-version'   => self::API_VERSION,
            'Content-Type'    => 'application/json',
            'Accept'          => 'application/json',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 1 — Create Cashfree order, return payment_session_id to the browser
    // POST /payment/create-order  (auth required)
    // ─────────────────────────────────────────────────────────────────────────
    public function createOrder(Request $request)
    {
        $request->validate(['order_id' => 'required|integer']);

        $order = Order::where('user_id', auth()->id())
            ->where('payment_status', 'awaiting_payment')
            ->findOrFail($request->order_id);

        $appId     = config('services.cashfree.app_id');
        $secretKey = config('services.cashfree.secret_key');

        if (!$appId || !$secretKey) {
            Log::error('Cashfree: credentials missing from .env', [
                'CASHFREE_APP_ID set'     => !empty($appId),
                'CASHFREE_SECRET_KEY set' => !empty($secretKey),
            ]);
            return response()->json([
                'error' => 'Payment gateway not configured. Contact support.'
            ], 500);
        }

        $user      = auth()->user();
        $cfOrderId = 'order_' . $order->id . '_' . time();
        $amount    = round((float) $order->total_price, 2);

        // Cashfree requires exactly a 10-digit Indian mobile number
        $phone = preg_replace('/\D/', '', $order->shipping_phone ?? '');
        $phone = substr($phone, -10);                         // last 10 digits
        if (strlen($phone) < 10) $phone = '9999999999';      // fallback

        $payload = [
            'order_id'         => $cfOrderId,
            'order_amount'     => $amount,
            'order_currency'   => 'INR',
            'customer_details' => [
                'customer_id'    => 'user_' . $order->user_id,
                'customer_name'  => $user->name  ?? 'Customer',
                'customer_email' => $user->email ?? 'noreply@example.com',
                'customer_phone' => $phone,
            ],
            'order_meta' => [
                // Cashfree replaces {order_id} with the cf_order_id you sent
                'return_url' => route('payment.verify') . '?order_id={order_id}',
                'notify_url' => route('payment.webhook'),
            ],
            'order_note' => 'Order #' . $order->id,
        ];

        Log::info('Cashfree createOrder: sending request', [
            'order_id'    => $order->id,
            'cf_order_id' => $cfOrderId,
            'amount'      => $amount,
            'env'         => config('services.cashfree.env'),
            'url'         => $this->baseUrl() . '/orders',
        ]);

        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(20)
                ->post($this->baseUrl() . '/orders', $payload);

            $body = $response->json();

            Log::info('Cashfree createOrder: response received', [
                'order_id'    => $order->id,
                'http_status' => $response->status(),
                'body_keys'   => array_keys($body ?? []),
                'has_session' => isset($body['payment_session_id']),
            ]);

            if (!$response->successful()) {
                $msg = $body['message'] ?? ('Cashfree error ' . $response->status());
                Log::error('Cashfree createOrder: API error', [
                    'order_id'    => $order->id,
                    'http_status' => $response->status(),
                    'message'     => $msg,
                    'body'        => $body,
                ]);
                return response()->json(['error' => $msg], 422);
            }

            $paymentSessionId = $body['payment_session_id'] ?? null;

            if (!$paymentSessionId) {
                Log::error('Cashfree createOrder: no payment_session_id in response', [
                    'order_id' => $order->id,
                    'body'     => $body,
                ]);
                return response()->json([
                    'error' => 'Payment session could not be created. Please try again.'
                ], 500);
            }

            $order->update(['cf_order_id' => $cfOrderId]);

            return response()->json([
                'payment_session_id' => $paymentSessionId,
                'cf_order_id'        => $cfOrderId,
                'order_id'           => $order->id,
                'amount'             => number_format($amount, 2),
                'env'                => config('services.cashfree.env', 'sandbox'),
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Cashfree createOrder: connection failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
                'url'      => $this->baseUrl() . '/orders',
            ]);
            return response()->json([
                'error' => 'Could not connect to payment gateway. Check server internet/firewall and try again.'
            ], 500);

        } catch (\Throwable $e) {
            Log::error('Cashfree createOrder: unexpected exception', [
                'order_id'  => $order->id,
                'class'     => get_class($e),
                'error'     => $e->getMessage(),
                'file'      => $e->getFile() . ':' . $e->getLine(),
            ]);
            return response()->json([
                'error' => 'Payment gateway error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 2 — Cashfree redirects here after payment (return_url)
    // GET /payment/verify?order_id={cf_order_id}
    // CSRF-exempt (see bootstrap/app.php)
    // ─────────────────────────────────────────────────────────────────────────
    public function verify(Request $request)
    {
        $cfOrderId = $request->query('order_id');

        Log::info('Cashfree verify: incoming', [
            'cf_order_id' => $cfOrderId,
            'all_params'  => $request->query(),
        ]);

        if (!$cfOrderId) {
            return redirect()->route('home')
                ->with('error', 'Payment verification failed — no order reference received.');
        }

        // Find our DB order
        $order = Order::where('cf_order_id', $cfOrderId)->first();
        if (!$order && preg_match('/^order_(\d+)_/', $cfOrderId, $m)) {
            $order = Order::find((int) $m[1]);
        }

        if (!$order) {
            Log::error('Cashfree verify: DB order not found', ['cf_order_id' => $cfOrderId]);
            return redirect()->route('home')
                ->with('error', 'Order not found. Contact support with ref: ' . $cfOrderId);
        }

        // Already paid — idempotent redirect
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('success', 'Order #' . $order->id . ' is already confirmed.');
        }

        // Verify with Cashfree API
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(20)
                ->get($this->baseUrl() . '/orders/' . $cfOrderId);

            $body        = $response->json();
            $orderStatus = strtoupper($body['order_status'] ?? '');

            Log::info('Cashfree verify: order status', [
                'order_id'     => $order->id,
                'cf_order_id'  => $cfOrderId,
                'order_status' => $orderStatus,
                'http_status'  => $response->status(),
            ]);

            if (!$response->successful()) {
                Log::error('Cashfree verify: API error', [
                    'order_id'    => $order->id,
                    'http_status' => $response->status(),
                    'body'        => $body,
                ]);
                return redirect()->route('checkout.confirmation', $order->id)
                    ->with('error', 'Payment verification failed for Order #' . $order->id
                        . '. If amount was deducted, contact support with ref: ' . $cfOrderId);
            }

            if ($orderStatus !== 'PAID') {
                return redirect()->route('checkout.confirmation', $order->id)
                    ->with('error', 'Payment not completed for Order #' . $order->id
                        . ' (status: ' . ucfirst(strtolower($orderStatus ?: 'unknown'))
                        . '). No amount deducted. You may try again.');
            }

            $order->update([
                'payment_status' => 'paid',
                'status'         => 'processing',
                'cf_order_id'    => $cfOrderId,
                'cf_payment_id'  => (string) ($body['cf_order_id'] ?? $cfOrderId),
            ]);

            Log::info('Cashfree verify: marked paid', ['order_id' => $order->id]);

        } catch (\Throwable $e) {
            Log::error('Cashfree verify: exception', [
                'order_id' => $order->id,
                'class'    => get_class($e),
                'error'    => $e->getMessage(),
            ]);
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error', 'Verification error for Order #' . $order->id
                    . '. If amount was deducted, contact support with ref: ' . $cfOrderId);
        }

        // Post-payment cleanup
        try {
            app(\App\Services\CartService::class)->clear();
            session()->forget(['coupon_code', 'coupon_discount']);
            $order = $order->fresh(['products', 'user']);
            app(CheckoutController::class)->sendOrderEmails($order);
        } catch (\Exception $e) {
            Log::error('Cashfree verify: post-payment task failed', ['error' => $e->getMessage()]);
        }

        return redirect()->route('checkout.confirmation', $order->id)
            ->with('success', 'Payment successful! Order #' . $order->id . ' confirmed.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 3 — Server-to-server webhook (backup, async)
    // POST /payment/webhook  (CSRF-exempt)
    // ─────────────────────────────────────────────────────────────────────────
    public function webhook(Request $request)
    {
        Log::info('Cashfree webhook received', ['type' => $request->input('type')]);

        // Verify HMAC signature
        if (!$this->verifyWebhookSignature($request)) {
            Log::warning('Cashfree webhook: bad signature');
            return response()->json(['status' => 'signature_mismatch']);
        }

        $payload     = $request->json()->all();
        $type        = $payload['type'] ?? '';
        $data        = $payload['data'] ?? [];
        $orderData   = $data['order'] ?? [];
        $cfOrderId   = $orderData['order_id'] ?? null;
        $orderStatus = strtoupper($orderData['order_status'] ?? '');

        if (!in_array($type, ['PAYMENT_SUCCESS_WEBHOOK', 'PAYMENT_SUCCESS'], true)) {
            return response()->json(['status' => 'ignored']);
        }

        if (!$cfOrderId || $orderStatus !== 'PAID') {
            return response()->json(['status' => 'not_paid']);
        }

        $order = Order::where('cf_order_id', $cfOrderId)->first();
        if (!$order && preg_match('/^order_(\d+)_/', $cfOrderId, $m)) {
            $order = Order::find((int) $m[1]);
        }

        if (!$order || $order->payment_status === 'paid') {
            return response()->json(['status' => $order ? 'already_paid' : 'not_found']);
        }

        $order->update([
            'payment_status' => 'paid',
            'status'         => 'processing',
            'cf_order_id'    => $cfOrderId,
            'cf_payment_id'  => $data['payment']['cf_payment_id'] ?? $cfOrderId,
        ]);

        Log::info('Cashfree webhook: order paid', ['order_id' => $order->id]);

        try {
            app(\App\Services\CartService::class)->clear();
            $order = $order->fresh(['products', 'user']);
            app(CheckoutController::class)->sendOrderEmails($order);
        } catch (\Exception $e) {
            Log::error('Cashfree webhook: post-payment error', ['error' => $e->getMessage()]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Verify Cashfree webhook HMAC-SHA256 signature.
     * Formula: base64( HMAC-SHA256( timestamp + rawBody, secretKey ) )
     * In sandbox the headers are often absent — allow through.
     */
    private function verifyWebhookSignature(Request $request): bool
    {
        $timestamp = $request->header('x-webhook-timestamp');
        $received  = $request->header('x-webhook-signature');
        $secret    = config('services.cashfree.secret_key', '');

        if (!$timestamp || !$received) {
            // Sandbox may skip signature headers — allow it
            return config('services.cashfree.env', 'sandbox') === 'sandbox';
        }

        $computed = base64_encode(
            hash_hmac('sha256', $timestamp . $request->getContent(), $secret, true)
        );

        return hash_equals($computed, $received);
    }
}
