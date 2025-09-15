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

class StripeController extends Controller
{
    public function initiate(Request $request, Order $order)
    {
        // Authorize order owner
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $amount = (int) round($order->total_amount * 100); // cents
        $currency = 'usd';

        // Create/find a pending payment record for this order
        $payment = Payment::firstOrCreate(
            ['order_id' => $order->id, 'status' => 'pending'],
            [
                'payment_method' => 'stripe',
                'amount' => $order->total_amount,
            ]
        );

        try {
            if (class_exists('\Stripe\Stripe')) {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                
                // Create Stripe Payment Intent
                $intent = \Stripe\PaymentIntent::create([
                    'amount' => $amount,
                    'currency' => $currency,
                    'metadata' => [
                        'order_id' => (string) $order->id,
                        'user_id' => (string) $order->user_id,
                        'payment_id' => (string) $payment->id,
                    ],
                    'receipt_email' => Auth::user()->email,
                ]);

                $payment->update(['payment_id' => $intent->id]);

                return view('web.checkout.stripe', [
                    'order' => $order,
                    'payment' => $payment,
                    'clientSecret' => $intent->client_secret,
                    'stripeKey' => config('services.stripe.key'),
                    'customer' => [
                        'name' => Auth::user()->name ?? '',
                        'email' => Auth::user()->email ?? '',
                        'phone' => Auth::user()->phone ?? ''
                    ],
                ]);
            } else {
                Log::warning('Stripe SDK not installed. Please run composer require stripe/stripe-php');
                return redirect()->route('checkout')->with('error', 'Stripe payment not available. Please try another payment method.');
            }
        } catch (\Throwable $e) {
            Log::error('Error creating Stripe payment intent: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'Unable to initiate payment. Please try again.');
        }
    }

    public function success(Request $request)
    {
        $data = $request->validate([
            'payment_intent' => 'required|string',
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        $order = Order::where('id', $data['order_id'])->where('user_id', Auth::id())->firstOrFail();

        try {
            if (class_exists('\Stripe\Stripe')) {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                
                // Retrieve the payment intent to verify status
                $intent = \Stripe\PaymentIntent::retrieve($data['payment_intent']);
                
                if ($intent->status === 'succeeded') {
                    // Mark payment as paid
                    $payment = Payment::where('order_id', $order->id)
                                    ->where('payment_id', $intent->id)
                                    ->where('status', 'pending')
                                    ->first();
                    
                    if ($payment) {
                        $payment->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                            'transaction_id' => $intent->charges->data[0]->id ?? null,
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
        } catch (\Throwable $e) {
            Log::error('Error verifying Stripe payment: ' . $e->getMessage());
        }

        return redirect()->route('checkout')->with('error', 'Payment verification failed. Please contact support.');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        if (!$endpointSecret) {
            return response('Webhook secret not configured', 400);
        }

        try {
            if (class_exists('\Stripe\Stripe')) {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                
                $event = \Stripe\Webhook::constructEvent(
                    $payload, $signature, $endpointSecret
                );

                // Handle the event
                switch ($event->type) {
                    case 'payment_intent.succeeded':
                        $paymentIntent = $event->data->object;
                        $this->handleSuccessfulPayment($paymentIntent);
                        break;
                    case 'payment_intent.payment_failed':
                        $paymentIntent = $event->data->object;
                        $this->handleFailedPayment($paymentIntent);
                        break;
                    default:
                        Log::info('Received unknown Stripe event type: ' . $event->type);
                }

                return response('OK');
            }
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid Stripe webhook payload: ' . $e->getMessage());
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid Stripe webhook signature: ' . $e->getMessage());
            return response('Invalid signature', 400);
        } catch (\Throwable $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response('Webhook error', 500);
        }

        return response('Stripe SDK not available', 500);
    }

    private function handleSuccessfulPayment($paymentIntent)
    {
        $payment = Payment::where('payment_id', $paymentIntent->id)->first();
        
        if ($payment && $payment->status === 'pending') {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'transaction_id' => $paymentIntent->charges->data[0]->id ?? null,
            ]);

            $payment->order->update(['status' => 'confirmed']);
            
            Log::info('Stripe payment confirmed via webhook', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'stripe_intent_id' => $paymentIntent->id
            ]);
        }
    }

    private function handleFailedPayment($paymentIntent)
    {
        $payment = Payment::where('payment_id', $paymentIntent->id)->first();
        
        if ($payment && $payment->status === 'pending') {
            $payment->update([
                'status' => 'failed',
                'failed_at' => now(),
            ]);

            Log::info('Stripe payment failed via webhook', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'stripe_intent_id' => $paymentIntent->id
            ]);
        }
    }
}
