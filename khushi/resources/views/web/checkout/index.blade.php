@extends('layouts.app')

@section('title', 'Checkout - E-Commerce Store')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Cart</a></li>
            <li class="breadcrumb-item active">Checkout</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-credit-card me-2"></i>Checkout</h2>
        </div>
    </div>

    <form id="checkoutForm" method="POST" action="{{ route('checkout.process') }}">
        @csrf
        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <!-- Shipping Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="{{ old('first_name', auth()->user()->name ?? '') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="{{ old('last_name') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email', auth()->user()->email ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Street Address *</label>
                            <input type="text" class="form-control" id="address" name="address" 
                                   value="{{ old('address') }}" placeholder="123 Main Street" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="{{ old('city') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="state" class="form-label">State *</label>
                                <input type="text" class="form-control" id="state" name="state" 
                                       value="{{ old('state') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="zip_code" class="form-label">ZIP Code *</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code" 
                                       value="{{ old('zip_code') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="country" class="form-label">Country *</label>
                            <select class="form-select" id="country" name="country" required>
                                <option value="">Select Country</option>
                                <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>United States</option>
                                <option value="CA" {{ old('country') == 'CA' ? 'selected' : '' }}>Canada</option>
                                <option value="UK" {{ old('country') == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                <option value="AU" {{ old('country') == 'AU' ? 'selected' : '' }}>Australia</option>
                            </select>
                        </div>

                        <!-- Billing Address -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="same_as_shipping" name="same_as_shipping" checked>
                            <label class="form-check-label" for="same_as_shipping">
                                Billing address is the same as shipping address
                            </label>
                        </div>

                        <div id="billing_address" style="display: none;">
                            <h6 class="mt-4 mb-3">Billing Address</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="billing_first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="billing_first_name" name="billing_first_name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="billing_last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="billing_last_name" name="billing_last_name">
                                </div>
                            </div>
                            <!-- Add more billing fields as needed -->
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Payment Method *</label>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="cod" value="cod" checked>
                                        <label class="form-check-label" for="cod">
                                            <i class="fas fa-money-bill me-2"></i>Cash on Delivery
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="card" value="card">
                                        <label class="form-check-label" for="card">
                                            <i class="fas fa-credit-card me-2"></i>Credit Card
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="paypal" value="paypal">
                                        <label class="form-check-label" for="paypal">
                                            <i class="fab fa-paypal me-2"></i>PayPal
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="wallet" value="wallet">
                                        <label class="form-check-label" for="wallet">
                                            <i class="fas fa-wallet me-2"></i>Wallet
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- COD Details -->
                        <div id="cod_details">
                            <div class="alert alert-info">
                                <i class="fas fa-money-bill me-2"></i>
                                Pay with cash when your order is delivered to your doorstep.
                            </div>
                        </div>

                        <!-- Credit Card Details -->
                        <div id="card_details" style="display: none;">
                            <div class="mb-3">
                                <label for="card_number" class="form-label">Card Number *</label>
                                <input type="text" class="form-control" id="card_number" name="card_number" 
                                       placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date *</label>
                                    <input type="text" class="form-control" id="expiry_date" name="expiry_date" 
                                           placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cvv" class="form-label">CVV *</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv" 
                                           placeholder="123" maxlength="4">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="card_name" class="form-label">Name on Card *</label>
                                <input type="text" class="form-control" id="card_name" name="card_name" 
                                       placeholder="John Doe">
                            </div>
                        </div>

                        <!-- PayPal Details -->
                        <div id="paypal_details" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fab fa-paypal me-2"></i>
                                You will be redirected to PayPal to complete your payment securely.
                            </div>
                        </div>

                        <!-- Wallet Details -->
                        <div id="wallet_details" style="display: none;">
                            <div class="alert alert-success">
                                <i class="fas fa-wallet me-2"></i>
                                Pay using your wallet balance. Current balance: ${{ auth()->user()->wallet_balance ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Order Notes (Optional)</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="notes" rows="3" 
                                  placeholder="Special instructions for your order..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <!-- Cart Items -->
                        <div class="order-items mb-3">
                            @foreach($cartItems as $item)
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ $item->product->images->first()->url ?? 'https://via.placeholder.com/60x60/f8f9fa/6c757d?text=Product' }}" 
                                     alt="{{ $item->product->name }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ Str::limit($item->product->name, 30) }}</h6>
                                    <small class="text-muted">Qty: {{ $item->quantity }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold">${{ number_format($item->price * $item->quantity, 2) }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <hr>

                        <!-- Price Breakdown -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>${{ number_format($subtotal, 2) }}</span>
                        </div>

                        @if($discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount:</span>
                            <span>-${{ number_format($discount, 2) }}</span>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax:</span>
                            <span>${{ number_format($tax, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>
                                @if($shipping == 0)
                                <span class="text-success">Free</span>
                                @else
                                ${{ number_format($shipping, 2) }}
                                @endif
                            </span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-primary">${{ number_format($total, 2) }}</strong>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="terms_agreement" name="terms_agreement" required>
                            <label class="form-check-label" for="terms_agreement">
                                I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> 
                                and <a href="#" class="text-decoration-none">Privacy Policy</a>
                            </label>
                        </div>

                        <!-- Place Order Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="placeOrderBtn">
                                <i class="fas fa-lock me-2"></i>Place Order
                            </button>
                        </div>

                        <!-- Security Info -->
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Your payment information is secure and encrypted
                            </small>
                        </div>

                        <!-- Payment Icons -->
                        <div class="text-center mt-3">
                            <img src="https://via.placeholder.com/40x25/007bff/ffffff?text=VISA" alt="Visa" class="me-2">
                            <img src="https://via.placeholder.com/40x25/ff6b35/ffffff?text=MC" alt="Mastercard" class="me-2">
                            <img src="https://via.placeholder.com/40x25/00457c/ffffff?text=PP" alt="PayPal" class="me-2">
                            <img src="https://via.placeholder.com/40x25/28a745/ffffff?text=SSL" alt="SSL">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle billing address
    $('#same_as_shipping').on('change', function() {
        if ($(this).is(':checked')) {
            $('#billing_address').hide();
        } else {
            $('#billing_address').show();
        }
    });

    // Payment method change
    $('input[name="payment_method"]').on('change', function() {
        const method = $(this).val();
        
        // Hide all payment details
        $('#cod_details, #card_details, #paypal_details, #wallet_details').hide();
        
        // Show selected payment method details
        $(`#${method}_details`).show();
        
        // Update required fields
        if (method === 'card') {
            $('#card_number, #expiry_date, #cvv, #card_name').prop('required', true);
        } else {
            $('#card_number, #expiry_date, #cvv, #card_name').prop('required', false);
        }
    });

    // Format card number
    $('#card_number').on('input', function() {
        let value = $(this).val().replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        $(this).val(formattedValue);
    });

    // Format expiry date
    $('#expiry_date').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        $(this).val(value);
    });

    // CVV numeric only
    $('#cvv').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });

    // Form submission
    $('#checkoutForm').on('submit', function(e) {
        e.preventDefault();
        
        const button = $('#placeOrderBtn');
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
        
        // Validate form
        if (!this.checkValidity()) {
            this.reportValidity();
            button.prop('disabled', false).html('<i class="fas fa-lock me-2"></i>Place Order');
            return;
        }

        // Submit form
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(data) {
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    showAlert('danger', data.message);
                    button.prop('disabled', false).html('<i class="fas fa-lock me-2"></i>Place Order');
                }
            },
            error: function(xhr) {
                let message = 'Error processing order. Please try again.';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        // Handle validation errors
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        message = errors.join(', ');
                    }
                } else if (xhr.status === 419) {
                    message = 'Session expired. Please refresh the page and try again.';
                } else if (xhr.status === 500) {
                    message = 'Server error. Please try again later.';
                }
                
                showAlert('danger', message);
                button.prop('disabled', false).html('<i class="fas fa-lock me-2"></i>Place Order');
            }
        });
    });

    // Auto-fill billing address when shipping changes
    $('#first_name, #last_name, #address, #city, #state, #zip_code, #country').on('change', function() {
        if ($('#same_as_shipping').is(':checked')) {
            const fieldName = $(this).attr('name');
            const billingFieldName = 'billing_' + fieldName;
            $(`[name="${billingFieldName}"]`).val($(this).val());
        }
    });
});
</script>
@endpush
