@extends('layouts.app')

@section('title', $product->name . ' - E-Commerce Store')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            @if($product->category)
            <li class="breadcrumb-item"><a href="{{ route('categories.show', $product->category->slug) }}">{{ $product->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="product-images">
                @if($product->images->count() > 0)
                <!-- Main Image -->
                <div class="main-image mb-3">
                    <img id="mainImage" src="{{ $product->image_url }}" 
                         alt="{{ $product->name }}" class="img-fluid rounded shadow">
                </div>
                
                <!-- Thumbnail Images -->
                @if($product->images->count() > 1)
                <div class="row g-2">
                    @foreach($product->images as $image)
                    <div class="col-3">
                        <img src="{{ $image->url }}" alt="{{ $product->name }}" 
                             class="img-fluid rounded thumbnail-image cursor-pointer {{ $loop->first ? 'active' : '' }}"
                             onclick="changeMainImage('{{ $image->url }}', this)">
                    </div>
                    @endforeach
                </div>
                @endif
                @else
                <img src="https://via.placeholder.com/500x400/f8f9fa/6c757d?text=No+Image" 
                     alt="{{ $product->name }}" class="img-fluid rounded shadow">
                @endif
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="product-details">
                <h1 class="h2 mb-3">{{ $product->name }}</h1>
                
                <!-- Rating -->
                @if($product->reviews_avg_rating)
                <div class="rating mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star{{ $i <= $product->reviews_avg_rating ? '' : '-o' }}"></i>
                    @endfor
                    <span class="ms-2 text-muted">({{ $product->reviews_count }} reviews)</span>
                </div>
                @endif

                <!-- Price -->
                <div class="price-section mb-4">
                    @if($product->sale_price && $product->sale_price < $product->price)
                    <h3 class="price mb-1">${{ number_format($product->sale_price, 2) }}</h3>
                    <p class="original-price mb-0">${{ number_format($product->price, 2) }}</p>
                    <span class="badge bg-danger">Save {{ number_format((($product->price - $product->sale_price) / $product->price) * 100, 0) }}%</span>
                    @else
                    <h3 class="price mb-1">${{ number_format($product->price, 2) }}</h3>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="stock-status mb-4">
                    @if($product->stock_quantity > 0)
                    <span class="badge bg-success fs-6">
                        <i class="fas fa-check-circle me-1"></i>In Stock ({{ $product->stock_quantity }} available)
                    </span>
                    @else
                    <span class="badge bg-danger fs-6">
                        <i class="fas fa-times-circle me-1"></i>Out of Stock
                    </span>
                    @endif
                </div>

                <!-- Description -->
                <div class="description mb-4">
                    <h5>Description</h5>
                    <p>{{ $product->description }}</p>
                </div>

                <!-- Product Variants -->
                @if($product->variants->count() > 0)
                <div class="variants mb-4">
                    <h6>Available Options</h6>
                    @foreach($product->variants->groupBy('attribute_name') as $attributeName => $variants)
                    <div class="mb-3">
                        <label class="form-label">{{ ucfirst($attributeName) }}</label>
                        <div class="btn-group" role="group">
                            @foreach($variants as $variant)
                            <input type="radio" class="btn-check" name="{{ $attributeName }}" 
                                   id="variant{{ $variant->id }}" value="{{ $variant->id }}">
                            <label class="btn btn-outline-primary" for="variant{{ $variant->id }}">
                                {{ $variant->attribute_value }}
                                @if($variant->price_adjustment != 0)
                                <small>({{ $variant->price_adjustment > 0 ? '+' : '' }}${{ number_format($variant->price_adjustment, 2) }})</small>
                                @endif
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Quantity and Add to Cart -->
                @if($product->stock_quantity > 0)
                <div class="add-to-cart-section mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" onclick="decreaseQuantity()">-</button>
                                <input type="number" id="quantity" class="form-control text-center" 
                                       value="1" min="1" max="{{ $product->stock_quantity }}">
                                <button class="btn btn-outline-secondary" type="button" onclick="increaseQuantity()">+</button>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-lg" id="addToCartBtn" onclick="addProductToCart()">
                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="action-buttons mb-4">
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-danger wishlist-btn" data-product-id="{{ $product->id }}">
                            <i class="far fa-heart me-1"></i>Add to Wishlist
                        </button>
                        <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#shareModal">
                            <i class="fas fa-share-alt me-1"></i>Share
                        </button>
                    </div>
                </div>

                <!-- Product Features -->
                @if($product->features)
                <div class="features mb-4">
                    <h6>Key Features</h6>
                    <ul class="list-unstyled">
                        @foreach(json_decode($product->features, true) ?? [] as $feature)
                        <li><i class="fas fa-check text-success me-2"></i>{{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Shipping Info -->
                <div class="shipping-info">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-shipping-fast me-2"></i>Shipping Information</h6>
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-truck me-2 text-primary"></i>Free shipping on orders over $50</li>
                                <li><i class="fas fa-clock me-2 text-primary"></i>Estimated delivery: 3-5 business days</li>
                                <li><i class="fas fa-undo me-2 text-primary"></i>30-day return policy</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" 
                            data-bs-target="#description" type="button" role="tab">
                        Description
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" 
                            data-bs-target="#specifications" type="button" role="tab">
                        Specifications
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" 
                            data-bs-target="#reviews" type="button" role="tab">
                        Reviews ({{ $product->reviews_count }})
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="productTabsContent">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="p-4">
                        <h5>Product Description</h5>
                        <p>{{ $product->long_description ?? $product->description }}</p>
                        
                        @if($product->specifications)
                        <h6 class="mt-4">Key Specifications</h6>
                        <div class="row">
                            @foreach(json_decode($product->specifications, true) ?? [] as $key => $value)
                            <div class="col-md-6 mb-2">
                                <strong>{{ ucfirst($key) }}:</strong> {{ $value }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div class="tab-pane fade" id="specifications" role="tabpanel">
                    <div class="p-4">
                        @if($product->specifications)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    @foreach(json_decode($product->specifications, true) ?? [] as $key => $value)
                                    <tr>
                                        <td class="fw-bold">{{ ucfirst($key) }}</td>
                                        <td>{{ $value }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No specifications available for this product.</p>
                        @endif
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <div class="p-4">
                        <!-- Review Summary -->
                        @if($product->reviews_count > 0)
                        <div class="review-summary mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Customer Reviews</h5>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="rating me-3">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $product->reviews_avg_rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="h5 mb-0">{{ number_format($product->reviews_avg_rating, 1) }}</span>
                                        <span class="text-muted ms-2">({{ $product->reviews_count }} reviews)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews List -->
                        <div class="reviews-list">
                            @foreach($product->reviews()->latest()->limit(5)->get() as $review)
                            <div class="review-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $review->user->name }}</h6>
                                        <div class="rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                                </div>
                                <p class="mb-0">{{ $review->comment }}</p>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Add Review Form -->
                        @auth
                        <div class="add-review mt-4">
                            <h6>Write a Review</h6>
                            <form id="reviewForm">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                
                                <div class="mb-3">
                                    <label class="form-label">Rating</label>
                                    <div class="rating-input">
                                        @for($i = 1; $i <= 5; $i++)
                                        <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" required>
                                        <label for="star{{ $i }}"><i class="far fa-star"></i></label>
                                        @endfor
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Comment</label>
                                    <textarea name="comment" class="form-control" rows="4" required></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </form>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <p class="text-muted">Please <a href="{{ route('login') }}">login</a> to write a review.</p>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Related Products</h3>
            <div class="row g-4">
                @foreach($relatedProducts as $relatedProduct)
                <div class="col-lg-3 col-md-6">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="{{ $relatedProduct->images->first()->url ?? 'https://via.placeholder.com/300x250/f8f9fa/6c757d?text=Product' }}" 
                                 class="card-img-top" alt="{{ $relatedProduct->name }}">
                            <div class="position-absolute top-0 end-0 m-2">
                                <button class="btn btn-sm btn-light rounded-circle wishlist-btn" 
                                        data-product-id="{{ $relatedProduct->id }}">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="{{ route('products.show', $relatedProduct->slug) }}" class="text-decoration-none text-dark">
                                    {{ Str::limit($relatedProduct->name, 50) }}
                                </a>
                            </h6>
                            <div class="price-section">
                                <span class="price">${{ number_format($relatedProduct->price, 2) }}</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-grid">
                                <button class="btn btn-primary btn-sm add-to-cart-btn" 
                                        data-product-id="{{ $relatedProduct->id }}">
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
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">Share Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center gap-3">
                    <a href="#" class="btn btn-primary" onclick="shareOnFacebook()">
                        <i class="fab fa-facebook-f me-1"></i> Facebook
                    </a>
                    <a href="#" class="btn btn-info" onclick="shareOnTwitter()">
                        <i class="fab fa-twitter me-1"></i> Twitter
                    </a>
                    <button class="btn btn-secondary" onclick="copyLink()">
                        <i class="fas fa-link me-1"></i> Copy Link
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.thumbnail-image {
    border: 2px solid transparent;
    transition: border-color 0.3s ease;
}

.thumbnail-image.active,
.thumbnail-image:hover {
    border-color: var(--primary-color);
}

.cursor-pointer {
    cursor: pointer;
}

.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-input input {
    display: none;
}

.rating-input label {
    cursor: pointer;
    font-size: 1.5rem;
    color: #ddd;
    margin-right: 5px;
}

.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input:checked ~ label {
    color: #fbbf24;
}
</style>
@endpush

@push('scripts')
<script>
let selectedVariants = {};

$(document).ready(function() {
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

    // Review form submission
    $('#reviewForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("products.review", $product) }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(data) {
                if(data.success) {
                    showAlert('success', 'Review submitted successfully!');
                    $('#reviewForm')[0].reset();
                    location.reload();
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Error submitting review');
            }
        });
    });

    // Variant selection
    $('input[type="radio"][name]').on('change', function() {
        const attributeName = $(this).attr('name');
        const variantId = $(this).val();
        selectedVariants[attributeName] = variantId;
    });
});

// Image gallery functions
function changeMainImage(imageUrl, thumbnail) {
    $('#mainImage').attr('src', imageUrl);
    $('.thumbnail-image').removeClass('active');
    $(thumbnail).addClass('active');
}

// Quantity functions
function increaseQuantity() {
    const quantityInput = $('#quantity');
    const currentValue = parseInt(quantityInput.val());
    const maxValue = parseInt(quantityInput.attr('max'));
    
    if (currentValue < maxValue) {
        quantityInput.val(currentValue + 1);
    }
}

function decreaseQuantity() {
    const quantityInput = $('#quantity');
    const currentValue = parseInt(quantityInput.val());
    
    if (currentValue > 1) {
        quantityInput.val(currentValue - 1);
    }
}

// Add to cart function
function addProductToCart() {
    const quantity = parseInt($('#quantity').val());
    const button = $('#addToCartBtn');
    
    button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Adding...');
    
    const data = {
        _token: '{{ csrf_token() }}',
        product_id: {{ $product->id }},
        quantity: quantity,
        variants: selectedVariants
    };
    
    $.ajax({
        url: '{{ route("cart.add") }}',
        method: 'POST',
        data: data,
        success: function(response) {
            if(response.success) {
                updateCartCount();
                showAlert('success', response.message);
            } else {
                showAlert('danger', response.message);
            }
        },
        error: function(xhr) {
            showAlert('danger', 'Error adding product to cart');
        },
        complete: function() {
            button.prop('disabled', false).html('<i class="fas fa-shopping-cart me-2"></i>Add to Cart');
        }
    });
}

// Share functions
function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('{{ $product->name }}');
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        showAlert('success', 'Link copied to clipboard!');
        $('#shareModal').modal('hide');
    });
}
</script>
@endpush
