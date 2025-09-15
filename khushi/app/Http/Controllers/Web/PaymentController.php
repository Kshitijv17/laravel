<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    /**
     * Show Razorpay checkout page by creating a Razorpay Order
     */
    public function initiate(Request $request, Order $order)
    {
        // Authorize order owner
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $amount = (int) round($order->total_amount * 100); // paise
        $currency = 'INR';

        // Create/find a pending payment record for this order
        $payment = Payment::firstOrCreate(
            ['order_id' => $order->id, 'status' => 'pending'],
            [
                'payment_method' => 'razorpay',
                'amount' => $order->total_amount,
            ]
        );

        // Create Razorpay Order via SDK if available
        $razorpayOrderId = $payment->payment_id; // reuse if we already created one
        try {
            if (class_exists('Razorpay\\Api\\Api')) {
                $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                if (!$razorpayOrderId) {
                    $rzpOrder = $api->order->create([
                        'amount' => $amount,
                        'currency' => $currency,
                        'receipt' => $order->order_number,
                        'payment_capture' => 1,
                        'notes' => [
                            'order_id' => (string) $order->id,
                            'user_id' => (string) $order->user_id,
                        ],
                    ]);
                    $razorpayOrderId = $rzpOrder['id'] ?? null;
                    if ($razorpayOrderId) {
                        $payment->update(['payment_id' => $razorpayOrderId]); // temporarily store RZP order id
                    }
                }
            } else {
                Log::warning('Razorpay SDK not installed. Please run composer require razorpay/razorpay');
            }
        } catch (\Throwable $e) {
            Log::error('Error creating Razorpay order: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'Unable to initiate payment. Please try again.');
        }

        return view('web.checkout.pay', [
            'order' => $order,
            'amountPaise' => $amount,
            'currency' => $currency,
            'razorpayKey' => config('services.razorpay.key'),
            'razorpayOrderId' => $razorpayOrderId,
            'brandName' => config('app.name', 'srcreationworld'),
            'customer' => [
                'name' => Auth::user()->name ?? '',
                'email' => Auth::user()->email ?? '',
                'contact' => Auth::user()->phone ?? ''
            ],
        ]);
    }

    /**
     * Verify Razorpay signature after client-side checkout success
     */
    public function verify(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $order = Order::where('id', $data['order_id'])->where('user_id', Auth::id())->firstOrFail();

        // Compute signature HMAC
        $expected = hash_hmac('sha256', $data['razorpay_order_id'] . '|' . $data['razorpay_payment_id'], config('services.razorpay.secret'));
        if (!hash_equals($expected, $data['razorpay_signature'])) {
            return redirect()->route('checkout')->with('error', 'Payment signature verification failed.');
        }

        // Mark payment as paid
        $payment = Payment::where('order_id', $order->id)->where('status', 'pending')->first();
        if (!$payment) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'razorpay',
                'amount' => $order->total_amount,
                'status' => 'pending',
            ]);
        }
        $payment->update([
            'payment_id' => $data['razorpay_payment_id'],
            'status' => 'paid',
            'paid_at' => now(),
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

    /**
     * Razorpay webhook handler (optional but recommended)
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');
        $secret = config('services.razorpay.webhook_secret');

        if (!$secret) {
            return response('Webhook secret not configured', 400);
        }

        $expected = hash_hmac('sha256', $payload, $secret);
        if (!hash_equals($expected, (string) $signature)) {
            return response('Invalid signature', 400);
        }

        $event = json_decode($payload, true);
        try {
            if (($event['event'] ?? '') === 'payment.captured') {
                $paymentId = $event['payload']['payment']['entity']['id'] ?? null;
                $amount = ($event['payload']['payment']['entity']['amount'] ?? 0) / 100;
                $rzpOrderId = $event['payload']['payment']['entity']['order_id'] ?? null;

                // Find our payment by RZP order id if we stored it there
                $payment = Payment::where('payment_id', $rzpOrderId)->where('status', 'pending')->first();
                if ($payment) {
                    $payment->update([
                        'payment_id' => $paymentId,
                        'status' => 'paid',
                        'paid_at' => now(),
                        'amount' => $amount ?: $payment->amount,
                    ]);
                    $payment->order->update(['status' => 'confirmed']);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Razorpay webhook error: ' . $e->getMessage());
        }

        return response('OK');
    }
}
