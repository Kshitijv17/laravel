@extends('customer.layout')

@section('title', 'Buy Now - ' . $product->name)

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.product.show', $product) }}">{{ $product->name }}</a></li>
            <li class="breadcrumb-item active">Buy Now</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Order Summary -->
        <div class="col-lg-4 order-lg-2 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Order Summary</h5>
                </div>
                <div class="card-body">
                    <!-- Product Info -->
                    <div class="d-flex mb-3">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                                 class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $product->name }}</h6>
                            <small class="text-muted">by {{ $product->shop->name }}</small>
                            <div class="mt-1">
                                <span class="price-tag">${{ number_format($finalPrice, 2) }}</span>
                                @if($product->discount_price && $product->discount_price < $product->price)
                                    <span class="original-price ms-2">${{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Price Calculation -->
                    <div id="price-summary">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Price per item:</span>
                            <span>${{ number_format($finalPrice, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Quantity:</span>
                            <span id="summary-quantity">1</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="summary-subtotal">${{ number_format($finalPrice, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span id="summary-shipping">$10.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (8%):</span>
                            <span id="summary-tax">${{ number_format($finalPrice * 0.08, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span id="summary-total">${{ number_format($finalPrice + 10 + ($finalPrice * 0.08), 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Free shipping on orders over $100
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Form -->
        <div class="col-lg-8 order-lg-1">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Checkout</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.process-buy-now', $product) }}" method="POST" id="checkout-form">
                        @csrf

                        <!-- Quantity Selection -->
                        <div class="mb-4">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <div class="input-group" style="max-width: 200px;">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(-1)">-</button>
                                <input type="number" name="quantity" id="quantity" class="form-control text-center @error('quantity') is-invalid @enderror"
                                       value="{{ old('quantity', 1) }}" min="1" max="{{ $product->quantity }}" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(1)">+</button>
                            </div>
                            <small class="text-muted">Available: {{ $product->quantity }} items</small>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Customer Information -->
                        <h6 class="mb-3">Customer Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="customer_name" 
                                       class="form-control @error('customer_name') is-invalid @enderror"
                                       value="{{ old('customer_name', auth()->user()->name ?? '') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="customer_email" id="customer_email" 
                                       class="form-control @error('customer_email') is-invalid @enderror"
                                       value="{{ old('customer_email', auth()->user()->email ?? '') }}" required>
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="customer_phone" id="customer_phone" 
                                   class="form-control @error('customer_phone') is-invalid @enderror"
                                   value="{{ old('customer_phone') }}" placeholder="+1 (555) 123-4567" required>
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Shipping Address -->
                        <h6 class="mb-3 mt-4">Shipping Address</h6>
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Street Address <span class="text-danger">*</span></label>
                            <textarea name="shipping_address" id="shipping_address" rows="3" 
                                      class="form-control @error('shipping_address') is-invalid @enderror"
                                      placeholder="Enter your full address" required>{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" id="city" 
                                       class="form-control @error('city') is-invalid @enderror"
                                       value="{{ old('city') }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                <input type="text" name="postal_code" id="postal_code" 
                                       class="form-control @error('postal_code') is-invalid @enderror"
                                       value="{{ old('postal_code') }}" required>
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <h6 class="mb-3 mt-4">Payment Method</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" 
                                           {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cod">
                                        <i class="fas fa-money-bill-wave me-2"></i>Cash on Delivery
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="card" value="card"
                                           {{ old('payment_method') == 'card' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="card">
                                        <i class="fas fa-credit-card me-2"></i>Credit/Debit Card
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer"
                                           {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bank_transfer">
                                        <i class="fas fa-university me-2"></i>Bank Transfer
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('payment_method')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror

                        <!-- Order Notes -->
                        <div class="mb-4 mt-4">
                            <label for="notes" class="form-label">Order Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Any special instructions for your order...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-buy-now btn-lg flex-grow-1">
                                <i class="fas fa-shopping-cart me-2"></i>Place Order
                            </button>
                            <a href="{{ route('customer.product.show', $product) }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const productPrice = {{ $finalPrice }};
    
    function changeQuantity(change) {
        const quantityInput = document.getElementById('quantity');
        const currentQuantity = parseInt(quantityInput.value);
        const maxQuantity = {{ $product->quantity }};
        
        let newQuantity = currentQuantity + change;
        
        if (newQuantity < 1) newQuantity = 1;
        if (newQuantity > maxQuantity) newQuantity = maxQuantity;
        
        quantityInput.value = newQuantity;
        updatePriceSummary();
    }
    
    function updatePriceSummary() {
        const quantity = parseInt(document.getElementById('quantity').value);
        const subtotal = productPrice * quantity;
        const shipping = subtotal >= 100 ? 0 : 10;
        const tax = subtotal * 0.08;
        const total = subtotal + shipping + tax;
        
        document.getElementById('summary-quantity').textContent = quantity;
        document.getElementById('summary-subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('summary-shipping').textContent = shipping === 0 ? 'FREE' : '$' + shipping.toFixed(2);
        document.getElementById('summary-tax').textContent = '$' + tax.toFixed(2);
        document.getElementById('summary-total').textContent = '$' + total.toFixed(2);
    }
    
    // Update price when quantity changes
    document.getElementById('quantity').addEventListener('input', updatePriceSummary);
    
    // Form validation
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        const quantity = parseInt(document.getElementById('quantity').value);
        const maxQuantity = {{ $product->quantity }};
        
        if (quantity > maxQuantity) {
            e.preventDefault();
            alert('Quantity cannot exceed available stock (' + maxQuantity + ')');
            return false;
        }
        
        if (quantity < 1) {
            e.preventDefault();
            alert('Quantity must be at least 1');
            return false;
        }
    });
</script>
@endpush
@endsection
