<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class PayPalController extends Controller
{
    private function getAccessToken()
    {
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.client_secret');
        $baseUrl = config('services.paypal.mode') === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->asForm()
            ->post($baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Failed to get PayPal access token');
    }

    public function initiate(Request $request, Order $order)
    {
        // Authorize order owner
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Create/find a pending payment record for this order
        $payment = Payment::firstOrCreate(
            ['order_id' => $order->id, 'status' => 'pending'],
            [
                'payment_method' => 'paypal',
                'amount' => $order->total_amount,
            ]
        );

        try {
            $accessToken = $this->getAccessToken();
            $baseUrl = config('services.paypal.mode') === 'live' 
                ? 'https://api-m.paypal.com' 
                : 'https://api-m.sandbox.paypal.com';

            // Create PayPal order
            $response = Http::withToken($accessToken)
                ->post($baseUrl . '/v2/checkout/orders', [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'reference_id' => $order->order_number,
                            'amount' => [
                                'currency_code' => 'USD',
                                'value' => number_format($order->total_amount, 2, '.', '')
                            ],
                            'description' => 'Order #' . $order->order_number,
                        ]
                    ],
                    'application_context' => [
                        'return_url' => route('paypal.success', ['order' => $order->id]),
                        'cancel_url' => route('paypal.cancel', ['order' => $order->id]),
                        'brand_name' => config('app.name'),
                        'landing_page' => 'BILLING',
                        'user_action' => 'PAY_NOW'
                    ]
                ]);

            if ($response->successful()) {
                $paypalOrder = $response->json();
                $payment->update(['payment_id' => $paypalOrder['id']]);

                // Get approval URL
                $approvalUrl = collect($paypalOrder['links'])
                    ->firstWhere('rel', 'approve')['href'];

                return redirect($approvalUrl);
            } else {
                Log::error('PayPal order creation failed', $response->json());
                return redirect()->route('checkout')->with('error', 'Unable to initiate PayPal payment. Please try again.');
            }
        } catch (\Throwable $e) {
            Log::error('Error creating PayPal order: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'Unable to initiate payment. Please try again.');
        }
    }

    public function success(Request $request, Order $order)
    {
        // Authorize order owner
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $token = $request->get('token');
        $payerId = $request->get('PayerID');

        if (!$token || !$payerId) {
            return redirect()->route('checkout')->with('error', 'Invalid PayPal response.');
        }

        try {
            $accessToken = $this->getAccessToken();
            $baseUrl = config('services.paypal.mode') === 'live' 
                ? 'https://api-m.paypal.com' 
                : 'https://api-m.sandbox.paypal.com';

            // Capture the payment
            $response = Http::withToken($accessToken)
                ->post($baseUrl . "/v2/checkout/orders/{$token}/capture");

            if ($response->successful()) {
                $captureData = $response->json();
                
                if ($captureData['status'] === 'COMPLETED') {
                    // Mark payment as paid
                    $payment = Payment::where('order_id', $order->id)
                                    ->where('payment_id', $token)
                                    ->where('status', 'pending')
                                    ->first();
                    
                    if ($payment) {
                        $captureId = $captureData['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
                        
                        $payment->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                            'transaction_id' => $captureId,
                        ]);

                        // Update order status
                        $order->update(['status' => 'confirmed']);

                        // Clear cart & coupon for this user
                        $cart = Cart::where('user_id', Auth::id())->first();
                        if ($cart) {
                            $cart->items()->delete();
                            $cart->delete();
                        }
                        Session::forget('applied_coupon');

                        return redirect()->route('order.success', $order->id)->with('success', 'Payment successful!');
                    }
                }
            }

            Log::error('PayPal capture failed', $response->json());
            return redirect()->route('checkout')->with('error', 'Payment capture failed. Please contact support.');
        } catch (\Throwable $e) {
            Log::error('Error capturing PayPal payment: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    public function cancel(Request $request, Order $order)
    {
        // Authorize order owner
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Mark payment as cancelled
        $payment = Payment::where('order_id', $order->id)
                         ->where('status', 'pending')
                         ->first();
        
        if ($payment) {
            $payment->update(['status' => 'cancelled']);
        }

        return redirect()->route('checkout')->with('info', 'Payment was cancelled. You can try again or choose a different payment method.');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $headers = $request->headers->all();

        // Verify webhook signature (recommended for production)
        $webhookId = config('services.paypal.webhook_id');
        
        try {
            $accessToken = $this->getAccessToken();
            $baseUrl = config('services.paypal.mode') === 'live' 
                ? 'https://api-m.paypal.com' 
                : 'https://api-m.sandbox.paypal.com';

            // Verify webhook signature
            $verifyResponse = Http::withToken($accessToken)
                ->post($baseUrl . '/v1/notifications/verify-webhook-signature', [
                    'auth_algo' => $headers['paypal-auth-algo'][0] ?? '',
                    'cert_id' => $headers['paypal-cert-id'][0] ?? '',
                    'transmission_id' => $headers['paypal-transmission-id'][0] ?? '',
                    'transmission_sig' => $headers['paypal-transmission-sig'][0] ?? '',
                    'transmission_time' => $headers['paypal-transmission-time'][0] ?? '',
                    'webhook_id' => $webhookId,
                    'webhook_event' => json_decode($payload, true)
                ]);

            if (!$verifyResponse->successful() || $verifyResponse->json()['verification_status'] !== 'SUCCESS') {
                Log::warning('PayPal webhook signature verification failed');
                return response('Unauthorized', 401);
            }

            $event = json_decode($payload, true);
            
            // Handle the event
            switch ($event['event_type']) {
                case 'CHECKOUT.ORDER.APPROVED':
                    $this->handleOrderApproved($event);
                    break;
                case 'PAYMENT.CAPTURE.COMPLETED':
                    $this->handlePaymentCaptured($event);
                    break;
                case 'PAYMENT.CAPTURE.DENIED':
                case 'PAYMENT.CAPTURE.DECLINED':
                    $this->handlePaymentFailed($event);
                    break;
                default:
                    Log::info('Received unknown PayPal event type: ' . $event['event_type']);
            }

            return response('OK');
        } catch (\Throwable $e) {
            Log::error('PayPal webhook error: ' . $e->getMessage());
            return response('Webhook error', 500);
        }
    }

    private function handleOrderApproved($event)
    {
        $paypalOrderId = $event['resource']['id'];
        
        Log::info('PayPal order approved', [
            'paypal_order_id' => $paypalOrderId
        ]);
    }

    private function handlePaymentCaptured($event)
    {
        $captureId = $event['resource']['id'];
        $paypalOrderId = $event['resource']['supplementary_data']['related_ids']['order_id'] ?? null;
        
        if ($paypalOrderId) {
            $payment = Payment::where('payment_id', $paypalOrderId)->first();
            
            if ($payment && $payment->status === 'pending') {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'transaction_id' => $captureId,
                ]);

                $payment->order->update(['status' => 'confirmed']);
                
                Log::info('PayPal payment confirmed via webhook', [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'paypal_order_id' => $paypalOrderId,
                    'capture_id' => $captureId
                ]);
            }
        }
    }

    private function handlePaymentFailed($event)
    {
        $paypalOrderId = $event['resource']['supplementary_data']['related_ids']['order_id'] ?? null;
        
        if ($paypalOrderId) {
            $payment = Payment::where('payment_id', $paypalOrderId)->first();
            
            if ($payment && $payment->status === 'pending') {
                $payment->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                ]);

                Log::info('PayPal payment failed via webhook', [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'paypal_order_id' => $paypalOrderId
                ]);
            }
        }
    }
}
