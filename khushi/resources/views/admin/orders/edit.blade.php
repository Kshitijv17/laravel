@extends('layouts.admin')

@section('title', 'Edit Order')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Edit Order #{{ $order->id ?? '001' }}</h1>
            <p class="page-subtitle">Update order details and status</p>
        </div>
        <div>
            <a href="{{ route('admin.orders.show', $order->id ?? 1) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-2"></i>View Order
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Orders
            </a>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.orders.show', $order->id ?? 1) }}">Order #{{ $order->id ?? '001' }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update', $order->id ?? 1) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Order Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    <option value="pending" {{ old('status', $order->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ old('status', $order->status ?? '') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="processing" {{ old('status', $order->status ?? '') == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ old('status', $order->status ?? '') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ old('status', $order->status ?? '') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ old('status', $order->status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="refunded" {{ old('status', $order->status ?? '') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Payment Status *</label>
                                <select class="form-select @error('payment_status') is-invalid @enderror" name="payment_status" required>
                                    <option value="pending" {{ old('payment_status', $order->payment_status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ old('payment_status', $order->payment_status ?? '') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="failed" {{ old('payment_status', $order->payment_status ?? '') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ old('payment_status', $order->payment_status ?? '') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    <option value="partially_refunded" {{ old('payment_status', $order->payment_status ?? '') == 'partially_refunded' ? 'selected' : '' }}>Partially Refunded</option>
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tracking Number</label>
                                <input type="text" class="form-control @error('tracking_number') is-invalid @enderror" 
                                       name="tracking_number" value="{{ old('tracking_number', $order->tracking_number ?? '') }}" 
                                       placeholder="Enter tracking number">
                                @error('tracking_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Tracking number for shipment</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Shipping Method</label>
                                <select class="form-select @error('shipping_method') is-invalid @enderror" name="shipping_method">
                                    <option value="">Select Method</option>
                                    <option value="standard" {{ old('shipping_method', $order->shipping_method ?? '') == 'standard' ? 'selected' : '' }}>Standard Shipping</option>
                                    <option value="express" {{ old('shipping_method', $order->shipping_method ?? '') == 'express' ? 'selected' : '' }}>Express Shipping</option>
                                    <option value="overnight" {{ old('shipping_method', $order->shipping_method ?? '') == 'overnight' ? 'selected' : '' }}>Overnight</option>
                                    <option value="pickup" {{ old('shipping_method', $order->shipping_method ?? '') == 'pickup' ? 'selected' : '' }}>Store Pickup</option>
                                </select>
                                @error('shipping_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estimated Delivery Date</label>
                                <input type="date" class="form-control @error('estimated_delivery') is-invalid @enderror" 
                                       name="estimated_delivery" value="{{ old('estimated_delivery', $order->estimated_delivery ?? '') }}">
                                @error('estimated_delivery')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Actual Delivery Date</label>
                                <input type="date" class="form-control @error('delivered_at') is-invalid @enderror" 
                                       name="delivered_at" value="{{ old('delivered_at', $order->delivered_at ? $order->delivered_at->format('Y-m-d') : '') }}">
                                @error('delivered_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                                  name="admin_notes" rows="4" placeholder="Internal notes about this order">{{ old('admin_notes', $order->admin_notes ?? '') }}</textarea>
                        @error('admin_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Internal notes (not visible to customer)</small>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Order ID:</span>
                        <span class="fw-bold">#{{ $order->id ?? '001' }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Customer:</span>
                        <span class="fw-bold">{{ $order->user->name ?? 'John Doe' }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Order Date:</span>
                        <span>{{ $order->created_at->format('M d, Y') ?? 'Jan 15, 2024' }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Total Amount:</span>
                        <span class="fw-bold text-success">${{ number_format($order->total_amount ?? 150.00, 2) }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Current Status:</span>
                        <span class="badge bg-warning">{{ ucfirst($order->status ?? 'pending') }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Payment Status:</span>
                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $order->payment_status ?? 'pending')) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Status Guidelines</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Status Flow</h6>
                    <ul class="mb-0 small">
                        <li><strong>Pending:</strong> Order placed, awaiting confirmation</li>
                        <li><strong>Confirmed:</strong> Order confirmed, preparing for processing</li>
                        <li><strong>Processing:</strong> Items being prepared/packed</li>
                        <li><strong>Shipped:</strong> Order dispatched for delivery</li>
                        <li><strong>Delivered:</strong> Order successfully delivered</li>
                        <li><strong>Cancelled:</strong> Order cancelled by customer/admin</li>
                        <li><strong>Refunded:</strong> Payment refunded to customer</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="sendStatusEmail()">
                        <i class="fas fa-envelope me-2"></i>Send Status Email
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="printInvoice()">
                        <i class="fas fa-print me-2"></i>Print Invoice
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="addTracking()">
                        <i class="fas fa-truck me-2"></i>Add Tracking
                    </button>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Items</h5>
            </div>
            <div class="card-body">
                <!-- Sample order items -->
                <div class="d-flex align-items-center mb-3">
                    <img src="https://via.placeholder.com/50x50/007bff/ffffff?text=P1" class="rounded me-3" alt="Product">
                    <div class="flex-grow-1">
                        <div class="fw-bold">Sample Product 1</div>
                        <small class="text-muted">Qty: 2 × $25.00</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">$50.00</div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <img src="https://via.placeholder.com/50x50/28a745/ffffff?text=P2" class="rounded me-3" alt="Product">
                    <div class="flex-grow-1">
                        <div class="fw-bold">Sample Product 2</div>
                        <small class="text-muted">Qty: 1 × $75.00</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">$75.00</div>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Subtotal:</span>
                    <span>$125.00</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Shipping:</span>
                    <span>$15.00</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Tax:</span>
                    <span>$10.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total:</span>
                    <span class="text-success">$150.00</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function sendStatusEmail() {
    if (confirm('Send status update email to customer?')) {
        // AJAX call to send email
        alert('Status email sent successfully!');
    }
}

function printInvoice() {
    window.open('{{ route("admin.orders.invoice", $order->id ?? 1) }}', '_blank');
}

function addTracking() {
    const trackingNumber = prompt('Enter tracking number:');
    if (trackingNumber) {
        document.querySelector('input[name="tracking_number"]').value = trackingNumber;
        alert('Tracking number added. Don\'t forget to save the order.');
    }
}

// Auto-update delivery date based on status
document.querySelector('select[name="status"]').addEventListener('change', function() {
    const status = this.value;
    const deliveryInput = document.querySelector('input[name="delivered_at"]');
    
    if (status === 'delivered' && !deliveryInput.value) {
        const today = new Date().toISOString().split('T')[0];
        deliveryInput.value = today;
    } else if (status !== 'delivered') {
        deliveryInput.value = '';
    }
});

// Auto-update payment status based on order status
document.querySelector('select[name="status"]').addEventListener('change', function() {
    const status = this.value;
    const paymentSelect = document.querySelector('select[name="payment_status"]');
    
    if (status === 'delivered' && paymentSelect.value === 'pending') {
        paymentSelect.value = 'paid';
    } else if (status === 'cancelled' || status === 'refunded') {
        paymentSelect.value = 'refunded';
    }
});
</script>
@endpush
