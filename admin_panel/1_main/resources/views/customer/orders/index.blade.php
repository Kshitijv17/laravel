@extends('customer.layout')

@section('title', 'My Orders')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-shopping-bag me-2"></i>My Orders</h2>
                <a href="{{ route('customer.home') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Continue Shopping
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(!auth()->check())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-sign-in-alt fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Please Login to View Orders</h4>
                        <p class="text-muted">You need to be logged in to view your order history.</p>
                        <a href="{{ route('user.login') }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </div>
                </div>
            @elseif($orders->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Orders Yet</h4>
                        <p class="text-muted">You haven't placed any orders yet. Start shopping to see your orders here!</p>
                        <a href="{{ route('customer.home') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-1"></i>Start Shopping
                        </a>
                    </div>
                </div>
            @else
                <!-- Orders List -->
                <div class="row">
                    @foreach($orders as $order)
                        <div class="col-12 mb-4">
                            <div class="card order-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Order #{{ $order->order_number }}</h6>
                                        <small class="text-muted">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }} mb-1">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        <br>
                                        <strong class="text-primary">${{ number_format($order->total_amount, 2) }}</strong>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <!-- Order Items -->
                                            @foreach($order->items as $item)
                                                <div class="d-flex align-items-center mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                                    <div class="product-image me-3">
                                                        @if($item->product->image)
                                                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                                 alt="{{ $item->product->title }}" 
                                                                 class="rounded" 
                                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                                 style="width: 60px; height: 60px;">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $item->product->title }}</h6>
                                                        <p class="text-muted mb-1 small">
                                                            <i class="fas fa-store me-1"></i>{{ $order->shop->name }}
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="text-muted small">Qty: {{ $item->quantity }}</span>
                                                            <strong>${{ number_format($item->price, 2) }} each</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-md-4">
                                            <!-- Order Summary -->
                                            <div class="bg-light rounded p-3">
                                                <h6 class="mb-3">Order Summary</h6>
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
                                                <div class="d-flex justify-content-between mb-3">
                                                    <strong>Total:</strong>
                                                    <strong>${{ number_format($order->total_amount, 2) }}</strong>
                                                </div>
                                                
                                                <!-- Payment Method -->
                                                <div class="mb-3">
                                                    <small class="text-muted">Payment Method:</small><br>
                                                    <span class="badge bg-secondary">{{ strtoupper($order->payment_method) }}</span>
                                                </div>
                                                
                                                <!-- Action Buttons -->
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye me-1"></i>View Details
                                                    </a>
                                                    @if($order->status === 'delivered')
                                                        <button class="btn btn-outline-success btn-sm" onclick="alert('Review feature coming soon!')">
                                                            <i class="fas fa-star me-1"></i>Write Review
                                                        </button>
                                                    @elseif($order->status === 'pending')
                                                        <button class="btn btn-outline-danger btn-sm" onclick="alert('Cancel order feature coming soon!')">
                                                            <i class="fas fa-times me-1"></i>Cancel Order
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
.order-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #e3e6f0;
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.product-image img {
    border: 1px solid #e3e6f0;
}

.badge {
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .order-card .card-header {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .order-card .card-header .text-end {
        text-align: left !important;
        margin-top: 10px;
    }
}
</style>
@endsection
