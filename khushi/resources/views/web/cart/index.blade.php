@extends('layouts.app')

@section('title', 'Shopping Cart - E-Commerce Store')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Shopping Cart</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h2>
        </div>
    </div>

    @if($cartItems->count() > 0)
    <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                <tr id="cart-item-{{ $item->id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item->product->images->first()->url ?? 'https://via.placeholder.com/80x80/f8f9fa/6c757d?text=Product' }}" 
                                                 alt="{{ $item->product->name }}" class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="{{ route('products.show', $item->product->slug) }}" class="text-decoration-none text-dark">
                                                        {{ $item->product->name }}
                                                    </a>
                                                </h6>
                                                @if(isset($item->product_variant_id) && $item->product_variant_id)
                                                <small class="text-muted">Variant: {{ $item->variant->attribute_name ?? 'N/A' }}: {{ $item->variant->attribute_value ?? 'N/A' }}</small>
                                                @endif
                                                <div class="mt-1">
                                                    @if($item->product->stock <= 0)
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                    @elseif($item->product->stock < $item->quantity)
                                                    <span class="badge bg-warning">Limited Stock ({{ $item->product->stock }} available)</span>
                                                    @else
                                                    <span class="badge bg-success">In Stock</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">${{ number_format($item->price, 2) }}</span>
                                    </td>
                                    <td>
                                        <div class="input-group" style="width: 120px;">
                                            <button class="btn btn-outline-secondary btn-sm" type="button" 
                                                    onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})">-</button>
                                            <input type="number" class="form-control form-control-sm text-center" 
                                                   value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}"
                                                   onchange="updateQuantity({{ $item->id }}, this.value)">
                                            <button class="btn btn-outline-secondary btn-sm" type="button" 
                                                    onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold item-total">${{ number_format($item->price * $item->quantity, 2) }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-danger btn-sm" onclick="removeItem({{ $item->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Cart Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Continue Shopping
                        </a>
                        <button class="btn btn-outline-danger" onclick="clearCart()">
                            <i class="fas fa-trash me-1"></i> Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <!-- Coupon Code -->
                    <div class="mb-3">
                        <label class="form-label">Coupon Code</label>
                        <div class="input-group">
                            <input type="text" id="couponCode" class="form-control" placeholder="Enter coupon code">
                            <button class="btn btn-outline-primary" type="button" onclick="applyCoupon()">Apply</button>
                        </div>
                        @if(session('coupon'))
                        <div class="mt-2">
                            <span class="badge bg-success">
                                Coupon "{{ session('coupon.code') }}" applied 
                                <button class="btn btn-sm btn-link text-white p-0 ms-1" onclick="removeCoupon()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        </div>
                        @endif
                    </div>

                    <hr>

                    <!-- Price Breakdown -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">${{ number_format($subtotal, 2) }}</span>
                    </div>

                    @if(session('coupon'))
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Discount ({{ session('coupon.code') }}):</span>
                        <span>-${{ number_format($discount ?? 0, 2) }}</span>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax:</span>
                        <span id="tax">${{ number_format($tax ?? 0, 2) }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span id="shipping">
                            @if($subtotal >= 50)
                            <span class="text-success">Free</span>
                            @else
                            ${{ number_format($shipping ?? 10, 2) }}
                            @endif
                        </span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="total" class="text-primary">${{ number_format($total, 2) }}</strong>
                    </div>

                    <!-- Checkout Button -->
                    <div class="d-grid">
                        <a href="{{ route('checkout') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </a>
                    </div>

                    <!-- Security Badge -->
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Secure checkout with SSL encryption
                        </small>
                    </div>
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-truck me-2"></i>Shipping Information</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1">
                            <i class="fas fa-check text-success me-2"></i>
                            Free shipping on orders over $50
                        </li>
                        <li class="mb-1">
                            <i class="fas fa-clock text-info me-2"></i>
                            Estimated delivery: 3-5 business days
                        </li>
                        <li class="mb-1">
                            <i class="fas fa-undo text-warning me-2"></i>
                            30-day return policy
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommended Products -->
    @if($recommendedProducts->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h4 class="mb-4">You might also like</h4>
            <div class="row g-4">
                @foreach($recommendedProducts as $product)
                <div class="col-lg-3 col-md-6">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="{{ $product->images->first()->url ?? 'https://via.placeholder.com/300x250/f8f9fa/6c757d?text=Product' }}" 
                                 class="card-img-top" alt="{{ $product->name }}">
                            <div class="position-absolute top-0 end-0 m-2">
                                <button class="btn btn-sm btn-light rounded-circle wishlist-btn" 
                                        data-product-id="{{ $product->id }}">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">{{ Str::limit($product->name, 50) }}</h6>
                            <div class="price-section">
                                <span class="price">${{ number_format($product->price, 2) }}</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-grid">
                                <button class="btn btn-primary btn-sm add-to-cart-btn" 
                                        data-product-id="{{ $product->id }}">
                                    <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @else
    <!-- Empty Cart -->
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h3>Your cart is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Update cart item quantity
function updateQuantity(itemId, quantity) {
    if (quantity < 1) {
        removeItem(itemId);
        return;
    }

    $.ajax({
        url: '{{ route("cart.update", ":itemId") }}'.replace(':itemId', itemId),
        method: 'PUT',
        data: {
            _token: '{{ csrf_token() }}',
            quantity: quantity
        },
        success: function(data) {
            if(data.success) {
                location.reload();
            } else {
                showAlert('danger', data.message);
            }
        },
        error: function(xhr) {
            showAlert('danger', 'Error updating cart item');
        }
    });
}

// Remove cart item
function removeItem(itemId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        $.ajax({
            url: '{{ route("cart.remove", ":itemId") }}'.replace(':itemId', itemId),
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                if(data.success) {
                    $(`#cart-item-${itemId}`).fadeOut(300, function() {
                        $(this).remove();
                        updateCartCount();
                        
                        // Check if cart is empty
                        if ($('tbody tr').length === 0) {
                            location.reload();
                        }
                    });
                    showAlert('success', data.message);
                } else {
                    showAlert('danger', data.message);
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Error removing cart item');
            }
        });
    }
}

// Clear entire cart
function clearCart() {
    if (confirm('Are you sure you want to clear your entire cart?')) {
        $.ajax({
            url: '{{ route("cart.clear") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                if(data.success) {
                    location.reload();
                } else {
                    showAlert('danger', data.message);
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Error clearing cart');
            }
        });
    }
}

// Apply coupon code
function applyCoupon() {
    const couponCode = $('#couponCode').val().trim();
    
    if (!couponCode) {
        showAlert('warning', 'Please enter a coupon code');
        return;
    }

    $.ajax({
        url: '{{ route("cart.coupon.apply") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            code: couponCode
        },
        success: function(data) {
            if(data.success) {
                location.reload();
            } else {
                showAlert('danger', data.message);
            }
        },
        error: function(xhr) {
            showAlert('danger', 'Invalid coupon code');
        }
    });
}

// Remove coupon
function removeCoupon() {
    $.ajax({
        url: '{{ route("cart.coupon.remove") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(data) {
            if(data.success) {
                location.reload();
            }
        },
        error: function(xhr) {
            showAlert('danger', 'Error removing coupon');
        }
    });
}

$(document).ready(function() {
    // Add to cart functionality for recommended products
    $('.add-to-cart-btn').on('click', function() {
        const productId = $(this).data('product-id');
        const button = $(this);
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Adding...');
        
        addToCart(productId, 1);
        
        setTimeout(function() {
            button.prop('disabled', false).html('<i class="fas fa-shopping-cart me-1"></i> Add to Cart');
        }, 1000);
    });

    // Wishlist functionality
    $('.wishlist-btn').on('click', function() {
        const productId = $(this).data('product-id');
        const button = $(this);
        const icon = button.find('i');
        
        @auth
        $.ajax({
            url: `/products/${productId}/wishlist`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                if(data.success) {
                    icon.toggleClass('far fas');
                    showAlert('success', data.message);
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Error updating wishlist');
            }
        });
        @else
        window.location.href = '{{ route("login") }}';
        @endauth
    });
});
</script>
@endpush
