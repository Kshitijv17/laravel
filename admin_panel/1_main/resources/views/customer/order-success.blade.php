@extends('customer.layout')

@section('title', 'Order Confirmed')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            <div class="text-center mb-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                </div>
                <h1 class="text-success mb-3">Order Confirmed!</h1>
                <p class="lead text-muted">Thank you for your purchase. Your order has been successfully placed.</p>
                <div class="alert alert-success">
                    <strong>Order Number: #{{ $order->order_number }}</strong>
                </div>
            </div>

            <!-- Order Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p class="mb-1"><strong>Order Number:</strong> #{{ $order->order_number }}</p>
                            <p class="mb-1"><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                            <p class="mb-1"><strong>Status:</strong> 
                                <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                            </p>
                            <p class="mb-1"><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                            <p class="mb-1"><strong>Payment Status:</strong> 
                                <span class="badge bg-warning">{{ ucfirst($order->payment_status) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Shop Information</h6>
                            <p class="mb-1"><strong>Shop:</strong> {{ $order->shop->name }}</p>
                            @if($order->shop->email)
                                <p class="mb-1"><strong>Shop Email:</strong> {{ $order->shop->email }}</p>
                            @endif
                            @if($order->shop->phone)
                                <p class="mb-1"><strong>Shop Phone:</strong> {{ $order->shop->phone }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Contact Details</h6>
                            <p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $order->customer_email }}</p>
                            <p class="mb-1"><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Shipping Address</h6>
                            @php
                                $shippingAddress = json_decode($order->shipping_address, true);
                            @endphp
                            @if($shippingAddress)
                                <p class="mb-1">{{ $shippingAddress['address'] }}</p>
                                <p class="mb-1">{{ $shippingAddress['city'] }}, {{ $shippingAddress['postal_code'] }}</p>
                                <p class="mb-1">{{ $shippingAddress['country'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Order Items</h5>
                </div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        <div class="d-flex align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            @if($item->product && $item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name ?? 'Product' }}" 
                                     class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $item->product->name ?? 'Product' }}</h6>
                                <p class="text-muted mb-1">Quantity: {{ $item->quantity }}</p>
                                <p class="text-muted mb-0">Price: ${{ number_format($item->price, 2) }} each</p>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0">${{ number_format($item->total, 2) }}</h6>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>${{ number_format($order->shipping_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span>${{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold h5">
                                <span>Total:</span>
                                <span>${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>What's Next?</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Order Processing</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Order received and confirmed</li>
                                <li><i class="fas fa-clock text-warning me-2"></i>Seller will process your order</li>
                                <li><i class="fas fa-truck text-info me-2"></i>Order will be shipped</li>
                                <li><i class="fas fa-home text-primary me-2"></i>Delivery to your address</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Important Information</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-envelope me-2"></i>Order confirmation sent to your email</li>
                                <li><i class="fas fa-phone me-2"></i>Seller may contact you for verification</li>
                                <li><i class="fas fa-shipping-fast me-2"></i>Estimated delivery: 3-5 business days</li>
                                <li><i class="fas fa-undo me-2"></i>30-day return policy available</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center">
                <a href="{{ route('customer.home') }}" class="btn btn-primary btn-lg me-3">
                    <i class="fas fa-home me-2"></i>Continue Shopping
                </a>
                <a href="{{ route('customer.order.details', $order) }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-eye me-2"></i>View Order Details
                </a>
            </div>

            <!-- Contact Support -->
            <div class="text-center mt-4">
                <p class="text-muted">
                    Need help with your order? 
                    <a href="#" class="text-decoration-none">Contact Support</a> or 
                    <a href="mailto:{{ $order->shop->email }}" class="text-decoration-none">Contact Seller</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
