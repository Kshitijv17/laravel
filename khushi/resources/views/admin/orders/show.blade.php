@extends('layouts.admin')

@section('title', 'Order #{{ $order->id }}')
@section('subtitle', 'Order details and management')

@section('content')
<div class="d-flex justify-content-end align-items-center mb-3">
    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-primary me-2">
        <i class="fas fa-edit me-2"></i>Edit Order
    </a>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Orders
    </a>
</div>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
        <li class="breadcrumb-item active">Order #{{ $order->id }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="rounded me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <strong>{{ $item->product->name ?? 'Product Deleted' }}</strong>
                                            @if($item->product && $item->product->sku)
                                                <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Subtotal:</th>
                                <th>${{ number_format($order->subtotal ?? $order->total_amount, 2) }}</th>
                            </tr>
                            @if($order->tax_amount > 0)
                            <tr>
                                <th colspan="3" class="text-end">Tax:</th>
                                <th>${{ number_format($order->tax_amount, 2) }}</th>
                            </tr>
                            @endif
                            @if($order->shipping_amount > 0)
                            <tr>
                                <th colspan="3" class="text-end">Shipping:</th>
                                <th>${{ number_format($order->shipping_amount, 2) }}</th>
                            </tr>
                            @endif
                            @if($order->discount_amount > 0)
                            <tr>
                                <th colspan="3" class="text-end">Discount:</th>
                                <th>-${{ number_format($order->discount_amount, 2) }}</th>
                            </tr>
                            @endif
                            <tr class="table-active">
                                <th colspan="3" class="text-end">Total:</th>
                                <th>${{ number_format($order->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Timeline -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Order Placed</h6>
                            <p class="timeline-text">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($order->status == 'processing' || $order->status == 'shipped' || $order->status == 'delivered')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Order Processing</h6>
                            <p class="timeline-text">Order is being prepared</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($order->status == 'shipped' || $order->status == 'delivered')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Order Shipped</h6>
                            <p class="timeline-text">Order has been shipped</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($order->status == 'delivered')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Order Delivered</h6>
                            <p class="timeline-text">Order has been delivered</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($order->status == 'cancelled')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-danger"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Order Cancelled</h6>
                            <p class="timeline-text">Order has been cancelled</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Order Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>Order ID:</strong></div>
                    <div class="col-sm-6">#{{ $order->id }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>Status:</strong></div>
                    <div class="col-sm-6">
                        <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>Payment:</strong></div>
                    <div class="col-sm-6">
                        <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>Date:</strong></div>
                    <div class="col-sm-6">{{ $order->created_at->format('M d, Y') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>Total:</strong></div>
                    <div class="col-sm-6"><strong>${{ number_format($order->total_amount, 2) }}</strong></div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                @if($order->user)
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Name:</strong></div>
                    <div class="col-sm-7">{{ $order->user->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Email:</strong></div>
                    <div class="col-sm-7">{{ $order->user->email }}</div>
                </div>
                @if($order->user->phone)
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Phone:</strong></div>
                    <div class="col-sm-7">{{ $order->user->phone }}</div>
                </div>
                @endif
                @else
                <p class="text-muted">Guest Order</p>
                @endif
            </div>
        </div>

        <!-- Shipping Address -->
        @if($order->address)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Shipping Address</h5>
            </div>
            <div class="card-body">
                <address class="mb-0">
                    {{ $order->address->first_name }} {{ $order->address->last_name }}<br>
                    {{ $order->address->address_line_1 }}<br>
                    @if($order->address->address_line_2)
                        {{ $order->address->address_line_2 }}<br>
                    @endif
                    {{ $order->address->city }}, {{ $order->address->state }} {{ $order->address->postal_code }}<br>
                    {{ $order->address->country }}
                </address>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($order->status != 'delivered' && $order->status != 'cancelled')
                    <button class="btn btn-success btn-sm" onclick="updateOrderStatus({{ $order->id }}, 'delivered')">
                        <i class="fas fa-check me-2"></i>Mark as Delivered
                    </button>
                    @endif
                    
                    @if($order->status != 'cancelled')
                    <button class="btn btn-danger btn-sm" onclick="updateOrderStatus({{ $order->id }}, 'cancelled')">
                        <i class="fas fa-times me-2"></i>Cancel Order
                    </button>
                    @endif
                    
                    <button class="btn btn-info btn-sm" onclick="printOrder()">
                        <i class="fas fa-print me-2"></i>Print Order
                    </button>
                    
                    <button class="btn btn-secondary btn-sm" onclick="sendOrderEmail()">
                        <i class="fas fa-envelope me-2"></i>Send Email
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-text {
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 0;
}
</style>
@endpush

@push('scripts')
<script>
function updateOrderStatus(orderId, status) {
    if (confirm('Are you sure you want to update this order status?')) {
        fetch(`/admin/orders/${orderId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating order status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating order status');
        });
    }
}

function printOrder() {
    window.print();
}

function sendOrderEmail() {
    alert('Email functionality would be implemented here');
}
</script>
@endpush
