@extends('layouts.app')

@section('title', 'Order Success')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <div class="success-icon mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                </div>
                <h1 class="h2 text-success mb-3">Order Placed Successfully!</h1>
                <p class="lead text-muted">Thank you for your purchase. Your order has been received and is being processed.</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Number</h6>
                            <p class="fw-bold">#{{ $order->order_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Date</h6>
                            <p>{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Total Amount</h6>
                            <p class="h5 text-primary">${{ number_format($order->total_amount, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Payment Status</h6>
                            @if($order->payments->count() > 0)
                                <span class="badge bg-{{ $order->payments->first()->status === 'paid' ? 'success' : ($order->payments->first()->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($order->payments->first()->status) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">No Payment</span>
                            @endif
                        </div>
                    </div>

                    <h6 class="text-muted mb-3">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('images/products/' . $item->product->image) }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="me-3" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $item->product->name ?? 'Product' }}</h6>
                                                @if($item->product && $item->product->sku)
                                                    <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>${{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <div class="row g-3 justify-content-center">
                    <div class="col-auto">
                        <a href="{{ route('user.orders') }}" class="btn btn-primary">
                            <i class="fas fa-list me-2"></i>View All Orders
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <h6 class="alert-heading">What's Next?</h6>
                <ul class="mb-0">
                    <li>You will receive an order confirmation email shortly</li>
                    <li>We'll notify you when your order is shipped</li>
                    <li>You can track your order status in your account</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.success-icon {
    animation: bounceIn 0.6s ease-out;
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
@endpush
