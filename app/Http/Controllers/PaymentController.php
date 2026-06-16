<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Front\CheckoutController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Cashfree Payment Gateway Controller
 *
 * Uses cashfree/cashfree-pg SDK (master branch, v6.x).
 * Install: composer require cashfree/cashfree-pg
 *
 * Verified namespaces from SDK source:
 *   \Cashfree\Cashfree                    — main client (lib/Cashfree.php)
 *   \Cashfree\Model\CreateOrderRequest    — (lib/Model/CreateOrderRequest.php)
 *   \Cashfree\Model\CustomerDetails       — (lib/Model/CustomerDetails.php)
 *   \Cashfree\Model\OrderMeta             — (lib/Model/OrderMeta.php)
 *
 * NOTE: $SANDBOX = 0 and $PRODUCTION = 1 are INSTANCE properties, not static.
 *       Pass 0 (sandbox) or 1 (production) as the first constructor argument.
 */
class PaymentController extends Controller
{
    /**
     * Return a configured Cashfree client instance.
     */
    private function getCashfree(): \Cashfree\Cashfree
    {
        $env = config('services.cashfree.env', 'sandbox') === 'production' ? 1 : 0;

        return new \Cashfree\Cashfree(
            $env,                                          // 0 = sandbox, 1 = production
            config('services.cashfree.app_id'),            // x-client-id
            config('services.cashfree.secret_key'),        // x-client-secret
            '',                                            // x-partner-api-key (unused)
            '',                                            // x-partner-merchant-id (unused)
            '',                                            // x-client-signature (unused)
            false                                          // error analytics
        );
    }

    /**
     * Create a Cashfree order via SDK and return payment_session_id to JS.
     *
     * POST /payment/create-order  { order_id }
     */
    public function createOrder(Request $request)
    {
        $request->validate(['order_id' => 'required|integer']);

        $order = Order::where('user_id', auth()->id())
            ->where('payment_status', 'awaiting_payment')
            ->findOrFail($request->order_id);

        if (!config('services.cashfree.app_id') || !config('services.cashfree.secret_key')) {
            Log::error('Cashfree: credentials not set in .env');
            return response()->json(['error' => 'Payment gateway not configured.'], 500);
        }

        $user      = auth()->user();
        $cfOrderId = 'order_' . $order->id . '_' . time();
        $amount    = round((float) $order->total_price, 2);
        $returnUrl = route('payment.verify')
            . '?order_id=' . $order->id
            . '&cf_order_id=' . urlencode($cfOrderId);

        try {
            // Build customer details
            $customerDetails = new \Cashfree\Model\CustomerDetails();
            $customerDetails->setCustomerId('cust_' . $user->id);
            $customerDetails->setCustomerName($user->name ?? 'Customer');
            $customerDetails->setCustomerEmail($user->email ?? '');
            $customerDetails->setCustomerPhone($order->shipping_phone ?? '9999999999');

            // Build order meta
            $orderMeta = new \Cashfree\Model\OrderMeta();
            $orderMeta->setReturnUrl($returnUrl);
            $orderMeta->setNotifyUrl(route('payment.webhook'));

            // Build create order request
            $orderRequest = new \Cashfree\Model\CreateOrderRequest();
            $orderRequest->setOrderId($cfOrderId);
            $orderRequest->setOrderAmount($amount);
            $orderRequest->setOrderCurrency('INR');
            $orderRequest->setOrderNote('Order #' . $order->id . ' - Jeanzo');
            $orderRequest->setCustomerDetails($customerDetails);
            $orderRequest->setOrderMeta($orderMeta);

            $response         = $this->getCashfree()->PGCreateOrder($orderRequest);
            $paymentSessionId = $response->getPaymentSessionId();

            if (!$paymentSessionId) {
                Log::error('Cashfree: no payment_session_id returned', ['response' => $response]);
                return response()->json(['error' => 'Failed to create payment session. Please try again.'], 500);
            }

            $order->update([
                'cf_order_id' => $cfOrderId,
                'payu_txnid'  => $cfOrderId,
            ]);

            Log::info('Cashfree order created', [
                'order_id'    => $order->id,
                'cf_order_id' => $cfOrderId,
            ]);

            return response()->json([
                'payment_session_id' => $paymentSessionId,
                'cashfree_order_id'  => $cfOrderId,
                'order_id'           => $order->id,
                'amount'             => $amount,
                'cf_env'             => config('services.cashfree.env', 'sandbox'),
            ]);

        } catch (\Exception $e) {
            Log::error('Cashfree createOrder error', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Payment gateway error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Verify payment after Cashfree redirects the browser back.
     *
     * GET /payment/verify?order_id=X&cf_order_id=Y
     */
    public function verify(Request $request)
    {
        $orderId   = $request->query('order_id');
        $cfOrderId = $request->query('cf_order_id');

        if (!$orderId || !is_numeric($orderId)) {
            return redirect()->route('home')->with('error', 'Invalid payment callback. Please contact support.');
        }

        $order = Order::find((int) $orderId);
        if (!$order) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('success', 'Payment already confirmed for Order #' . $order->id . '.');
        }

        $lookupId = $cfOrderId ?: $order->cf_order_id ?: $order->payu_txnid;

        if (!$lookupId) {
            Log::error('Cashfree verify: no cf_order_id available', ['order_id' => $orderId]);
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error', 'Unable to verify payment. Contact support with Order #' . $order->id);
        }

        try {
            $cfOrder  = $this->getCashfree()->PGFetchOrder($lookupId);
            $cfStatus = $cfOrder->getOrderStatus();

            Log::info('Cashfree verify: status', ['order_id' => $orderId, 'cf_status' => $cfStatus]);

            if ($cfStatus !== 'PAID') {
                return redirect()->route('checkout.confirmation', $order->id)
                    ->with('error', 'Payment not successful for Order #' . $order->id
                        . ' (status: ' . $cfStatus . '). If amount was deducted, contact support.');
            }

            $paymentId = (string) ($cfOrder->getCfOrderId() ?? $lookupId);

            $order->update([
                'payment_status'  => 'paid',
                'status'          => 'processing',
                'cf_payment_id'   => $paymentId,
                'payu_payment_id' => $paymentId,
            ]);

            Log::info('Cashfree verify: order paid', ['order_id' => $order->id]);

            app(\App\Services\CartService::class)->clear();
            session()->forget(['coupon_code', 'coupon_discount']);

            $order = $order->fresh(['products', 'user']);
            app(CheckoutController::class)->sendOrderEmails($order);

            return redirect()->route('checkout.confirmation', $order->id)
                ->with('success', 'Payment successful! Order #' . $order->id . ' confirmed.');

        } catch (\Exception $e) {
            Log::error('Cashfree verify error', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error', 'Unable to verify payment. Contact support with Order #' . $order->id);
        }
    }

    /**
     * Cashfree webhook — server-to-server async payment confirmation.
     * POST /payment/webhook  (CSRF-exempt)
     *
     * Uses the SDK's built-in PGVerifyWebhookSignature() method.
     */
    public function webhook(Request $request)
    {
        $rawBody   = $request->getContent();
        $timestamp = $request->header('x-webhook-timestamp');
        $signature = $request->header('x-webhook-signature');

        $cashfree = $this->getCashfree();

        // Verify signature using SDK helper
        try {
            $event = $cashfree->PGVerifyWebhookSignature($signature, $rawBody, $timestamp);
        } catch (\Exception $e) {
            Log::warning('Cashfree webhook: signature verification failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $eventType = $event->type ?? '';
        Log::info('Cashfree webhook received', ['type' => $eventType]);

        if ($eventType !== 'PAYMENT_SUCCESS_WEBHOOK') {
            return response()->json(['status' => 'ignored']);
        }

        $cfOrderId = $event->object->data->order->order_id ?? null;
        if (!$cfOrderId) {
            return response()->json(['status' => 'missing_order_id']);
        }

        $order = Order::where('cf_order_id', $cfOrderId)
            ->orWhere('payu_txnid', $cfOrderId)
            ->first();

        if (!$order) {
            Log::warning('Cashfree webhook: order not found', ['cf_order_id' => $cfOrderId]);
            return response()->json(['status' => 'order_not_found']);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['status' => 'already_paid']);
        }

        $paymentId = (string) ($event->object->data->payment->cf_payment_id ?? $cfOrderId);

        $order->update([
            'payment_status'  => 'paid',
            'status'          => 'processing',
            'cf_payment_id'   => $paymentId,
            'payu_payment_id' => $paymentId,
        ]);

        Log::info('Cashfree webhook: order paid', ['order_id' => $order->id]);

        try {
            app(\App\Services\CartService::class)->clear();
            session()->forget(['coupon_code', 'coupon_discount']);
            $order = $order->fresh(['products', 'user']);
            app(CheckoutController::class)->sendOrderEmails($order);
        } catch (\Exception $e) {
            Log::error('Cashfree webhook: post-payment error', ['error' => $e->getMessage()]);
        }

        return response()->json(['status' => 'ok']);
    }
}
