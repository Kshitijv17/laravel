@extends('layouts.app')

@section('title', 'Order Confirmation - E-Commerce Store')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            <div class="text-center mb-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h1 class="h2 text-success mb-3">Order Placed Successfully!</h1>
                <p class="lead text-muted">Thank you for your purchase. Your order has been confirmed and is being processed.</p>
            </div>

            <!-- Order Details Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Number</h6>
                            <p class="h5 text-primary">#{{ $order->order_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Date</h6>
                            <p class="h6">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Payment Method</h6>
                            <p class="mb-0">
                                @switch($order->payment_method)
                                    @case('credit_card')
                                        <i class="fas fa-credit-card me-1"></i>Credit Card
                                        @break
                                    @case('paypal')
                                        <i class="fab fa-paypal me-1"></i>PayPal
                                        @break
                                    @case('bank_transfer')
                                        <i class="fas fa-university me-1"></i>Bank Transfer
                                        @break
                                    @default
                                        {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                                @endswitch
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Payment Status</h6>
                            <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }} fs-6">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="mb-4">
                        <h6 class="text-muted">Shipping Address</h6>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-1"><strong>{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</strong></p>
                            <p class="mb-1">{{ $order->shipping_address }}</p>
                            <p class="mb-0">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip_code }}</p>
                            <p class="mb-0">{{ $order->shipping_country }}</p>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="mb-4">
                        <h6 class="text-muted">Order Items</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->product->images->first()->url ?? 'https://via.placeholder.com/50x50/f8f9fa/6c757d?text=Product' }}" 
                                                     alt="{{ $item->product->name }}" class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0">{{ $item->product->name }}</h6>
                                                    @if($item->product_variant_id)
                                                    <small class="text-muted">{{ $item->variant->attribute_name }}: {{ $item->variant->attribute_value }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>${{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                @if($order->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Discount:</span>
                                    <span>-${{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax:</span>
                                    <span>${{ number_format($order->tax_amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span>
                                        @if($order->shipping_amount == 0)
                                        <span class="text-success">Free</span>
                                        @else
                                        ${{ number_format($order->shipping_amount, 2) }}
                                        @endif
                                    </span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Total:</strong>
                                    <strong class="text-primary">${{ number_format($order->total_amount, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>What's Next?</h5>
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                            <h6>Email Confirmation</h6>
                            <p class="small text-muted">You'll receive an order confirmation email shortly.</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-box fa-2x text-info mb-2"></i>
                            <h6>Processing</h6>
                            <p class="small text-muted">We'll prepare your order for shipment within 1-2 business days.</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-shipping-fast fa-2x text-success mb-2"></i>
                            <h6>Delivery</h6>
                            <p class="small text-muted">Estimated delivery: 3-5 business days.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center">
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('user.orders') }}" class="btn btn-primary">
                        <i class="fas fa-list me-1"></i>View All Orders
                    </a>
                    <a href="{{ route('orders.track', $order->order_number) }}" class="btn btn-outline-info">
                        <i class="fas fa-search me-1"></i>Track Order
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-shopping-bag me-1"></i>Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Support Info -->
            <div class="text-center mt-4">
                <p class="text-muted">
                    Need help? <a href="{{ route('contact') }}" class="text-decoration-none">Contact our support team</a> 
                    or call <strong>(555) 123-4567</strong>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Print order functionality
    $('#printOrder').on('click', function() {
        window.print();
    });
    
    // Auto-scroll to top on page load
    window.scrollTo(0, 0);
});
</script>
@endpush
