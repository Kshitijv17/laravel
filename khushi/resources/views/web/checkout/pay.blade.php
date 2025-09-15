@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pay Securely</h5>
                    <span class="badge bg-secondary">Order: {{ $order->order_number }}</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="text-muted small">Amount to pay</div>
                            <div class="h4 mb-0">₹{{ number_format($order->total_amount, 2) }}</div>
                        </div>
                        <img src="{{ asset('images/logo.png') }}" alt="{{ $brandName }}" style="height:40px" onerror="this.style.display='none'">
                    </div>

                    <ul class="list-unstyled text-muted small mb-4">
                        <li><i class="fas fa-lock me-2 text-success"></i>256-bit SSL Encrypted</li>
                        <li><i class="fas fa-shield-alt me-2 text-success"></i>Secure payment by Razorpay</li>
                    </ul>

                    @if(empty($razorpayKey) || empty($razorpayOrderId))
                        <div class="alert alert-danger">
                            Payment configuration missing. Please contact support.
                        </div>
                    @else
                        <div class="d-grid gap-2">
                            <button id="rzpPayBtn" class="btn btn-primary btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Pay ₹{{ number_format($order->total_amount, 2) }}
                            </button>
                            <a href="{{ route('checkout') }}" class="btn btn-outline-secondary">Back to Checkout</a>
                        </div>
                    @endif

                    <form id="rzpVerifyForm" action="{{ route('payment.verify') }}" method="POST" class="d-none">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
                    </form>
                </div>
            </div>
            <p class="text-center text-muted small mt-3">You will be redirected back here after a successful payment.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    (function(){
        const key = @json($razorpayKey);
        const orderId = @json($razorpayOrderId);
        const amountPaise = @json($amountPaise);
        const currency = @json($currency);
        const brandName = @json($brandName);
        const customer = @json($customer);

        const btn = document.getElementById('rzpPayBtn');
        if(!btn || !key || !orderId) return;

        const options = {
            key: key,
            amount: amountPaise,
            currency: currency,
            name: brandName,
            description: 'Payment for {{ $order->order_number }}',
            order_id: orderId,
            prefill: {
                name: customer?.name || '',
                email: customer?.email || '',
                contact: customer?.contact || ''
            },
            theme: { color: '#ff3f6c' },
            handler: function (response) {
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                document.getElementById('razorpay_signature').value = response.razorpay_signature;
                document.getElementById('rzpVerifyForm').submit();
            }
        };

        const rzp = new Razorpay(options);
        btn.addEventListener('click', function(e){
            e.preventDefault();
            rzp.open();
        });
    })();
</script>
@endpush
