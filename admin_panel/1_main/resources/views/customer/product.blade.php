@extends('customer.layout')

@section('title', $product->title)

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.home') }}">Home</a></li>
            @if($product->category)
                <li class="breadcrumb-item"><a href="{{ route('customer.category', $product->category) }}">{{ $product->category->title }}</a></li>
            @endif
            <li class="breadcrumb-item"><a href="{{ route('customer.shop', $product->shop) }}">{{ $product->shop->name }}</a></li>
            <li class="breadcrumb-item active">{{ $product->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images Gallery -->
        <div class="col-lg-6 mb-4">
            <div class="product-gallery">
                <!-- Main Image -->
                <div class="main-image-container mb-3">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" 
                             class="main-product-image img-fluid w-100" 
                             alt="{{ $product->title }}" 
                             id="mainImage">
                    @else
                        <div class="main-product-image bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-image fa-5x text-muted"></i>
                        </div>
                    @endif
                    
                    <!-- Image Controls -->
                    <div class="image-controls">
                        <button class="btn btn-light btn-sm zoom-btn" onclick="zoomImage()">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        <button class="btn btn-light btn-sm share-btn" onclick="shareProduct()">
                            <i class="fas fa-share-alt"></i>
                        </button>
                        <button class="btn btn-light btn-sm wishlist-btn" onclick="toggleWishlist({{ $product->id }})">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Thumbnail Images -->
                @if($product->images && $product->images->count() > 0)
                    <div class="thumbnail-container">
                        <div class="row g-2">
                            <!-- Main image thumbnail -->
                            @if($product->image)
                                <div class="col-3">
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         class="thumbnail-image active" 
                                         onclick="changeMainImage(this.src)"
                                         alt="Main image">
                                </div>
                            @endif
                            
                            <!-- Additional images -->
                            @foreach($product->images->take(3) as $image)
                                <div class="col-3">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                         class="thumbnail-image" 
                                         onclick="changeMainImage(this.src)"
                                         alt="Product image">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Discount Badge -->
                @if($product->discount_tag)
                    <div class="discount-badge-large" style="background-color: {{ $product->discount_color ?? '#FF0000' }}">
                        {{ $product->discount_tag }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-3">{{ $product->title }}</h1>
                    
                    <!-- Shop and Category Info -->
                    <div class="mb-3">
                        <a href="{{ route('customer.shop', $product->shop) }}" class="shop-badge text-decoration-none">
                            <i class="fas fa-store me-1"></i>{{ $product->shop->name }}
                        </a>
                        @if($product->category)
                            <span class="category-badge ms-2">{{ $product->category->title }}</span>
                        @endif
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                        @if($product->discount_price && $product->discount_price < $product->price)
                            <div class="d-flex align-items-center">
                                <h2 class="price-tag mb-0">${{ number_format($finalPrice, 2) }}</h2>
                                <span class="original-price ms-3 h5">${{ number_format($product->price, 2) }}</span>
                                @if($discountPercentage > 0)
                                    <span class="badge bg-danger ms-3">{{ $discountPercentage }}% OFF</span>
                                @endif
                            </div>
                        @else
                            <h2 class="price-tag">${{ number_format($finalPrice, 2) }}</h2>
                        @endif
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-4">
                        @if($product->quantity <= 0)
                            <span class="stock-badge out-of-stock">Out of Stock</span>
                        @elseif($product->quantity <= 10)
                            <span class="stock-badge low-stock">Only {{ $product->quantity }} left in stock</span>
                        @else
                            <span class="stock-badge in-stock">In Stock ({{ $product->quantity }} available)</span>
                        @endif
                    </div>

                    <!-- Product Details -->
                    @if($product->description)
                        <div class="mb-4">
                            <h5>Description</h5>
                            <p class="text-muted">{{ $product->description }}</p>
                        </div>
                    @endif

                    <!-- Product Specifications -->
                    <div class="mb-4">
                        <h5>Product Details</h5>
                        <div class="row">
                            @if($product->sku)
                                <div class="col-sm-6 mb-2">
                                    <strong>SKU:</strong> <span class="font-monospace">{{ $product->sku }}</span>
                                </div>
                            @endif
                            @if($product->weight)
                                <div class="col-sm-6 mb-2">
                                    <strong>Weight:</strong> {{ $product->weight }} kg
                                </div>
                            @endif
                            <div class="col-sm-6 mb-2">
                                <strong>Category:</strong> {{ $product->category->title ?? 'Uncategorized' }}
                            </div>
                            <div class="col-sm-6 mb-2">
                                <strong>Seller:</strong> {{ $product->shop->name }}
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        @if($product->quantity > 0)
                            <a href="{{ route('customer.buy-now', $product) }}" class="btn btn-buy-now btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>Buy Now - ${{ number_format($finalPrice, 2) }}
                            </a>
                            <button class="btn btn-outline-primary btn-lg" onclick="addToCart({{ $product->id }})">
                                <i class="fas fa-heart me-2"></i>Add to Wishlist
                            </button>
                        @else
                            <button class="btn btn-secondary btn-lg" disabled>
                                <i class="fas fa-times me-2"></i>Out of Stock
                            </button>
                        @endif
                    </div>

                    <!-- Shipping Info -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6><i class="fas fa-truck me-2"></i>Shipping Information</h6>
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-check text-success me-2"></i>Free shipping on orders over $100</li>
                            <li><i class="fas fa-check text-success me-2"></i>Standard delivery: 3-5 business days</li>
                            <li><i class="fas fa-check text-success me-2"></i>Express delivery available</li>
                            <li><i class="fas fa-check text-success me-2"></i>Easy returns within 30 days</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shop Information -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-store me-2"></i>About {{ $product->shop->name }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            @if($product->shop->description)
                                <p class="text-muted">{{ $product->shop->description }}</p>
                            @endif
                            <div class="row">
                                @if($product->shop->email)
                                    <div class="col-sm-6 mb-2">
                                        <strong>Email:</strong> {{ $product->shop->email }}
                                    </div>
                                @endif
                                @if($product->shop->phone)
                                    <div class="col-sm-6 mb-2">
                                        <strong>Phone:</strong> {{ $product->shop->phone }}
                                    </div>
                                @endif
                                @if($product->shop->address)
                                    <div class="col-12 mb-2">
                                        <strong>Address:</strong> {{ $product->shop->address }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('customer.shop', $product->shop) }}" class="btn btn-primary">
                                <i class="fas fa-store me-1"></i>Visit Shop
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                                <i class="fas fa-info-circle me-1"></i>Description
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab">
                                <i class="fas fa-cog me-1"></i>Specifications
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                                <i class="fas fa-star me-1"></i>Reviews ({{ $reviews->count() ?? 0 }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab">
                                <i class="fas fa-truck me-1"></i>Shipping & Returns
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="productTabsContent">
                        <!-- Description Tab -->
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            @if($product->description)
                                <div class="product-description">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            @endif
                            
                            @if($product->features)
                                <h5 class="mt-4 mb-3">Key Features</h5>
                                <div class="features-list">
                                    {!! nl2br(e($product->features)) !!}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Specifications Tab -->
                        <div class="tab-pane fade" id="specifications" role="tabpanel">
                            @if($product->specifications)
                                <div class="specifications-content">
                                    {!! nl2br(e($product->specifications)) !!}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No specifications available for this product.</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Reviews Tab -->
                        <div class="tab-pane fade" id="reviews" role="tabpanel">
                            <!-- Review Summary -->
                            <div class="review-summary mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center">
                                        <div class="overall-rating">
                                            <h2 class="display-4 mb-0">{{ number_format($averageRating ?? 4.5, 1) }}</h2>
                                            <div class="stars mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= ($averageRating ?? 4.5) ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                            <p class="text-muted">Based on {{ $reviews->count() ?? 0 }} reviews</p>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="rating-breakdown">
                                            @for($i = 5; $i >= 1; $i--)
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="me-2">{{ $i }} star</span>
                                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                        <div class="progress-bar bg-warning" style="width: {{ rand(10, 80) }}%"></div>
                                                    </div>
                                                    <span class="text-muted">{{ rand(5, 50) }}</span>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Write Review Button -->
                            <div class="mb-4">
                                <button class="btn btn-primary" onclick="showReviewForm()">
                                    <i class="fas fa-edit me-1"></i>Write a Review
                                </button>
                            </div>
                            
                            <!-- Review Form (Hidden by default) -->
                            <div id="reviewForm" class="card mb-4" style="display: none;">
                                <div class="card-header">
                                    <h6 class="mb-0">Write Your Review</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('customer.reviews.store', $product) }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Rating</label>
                                            <div class="rating-input">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}">
                                                    <label for="star{{ $i }}" class="star-label">
                                                        <i class="fas fa-star"></i>
                                                    </label>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Review Title</label>
                                            <input type="text" name="title" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Your Review</label>
                                            <textarea name="comment" class="form-control" rows="4" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">Submit Review</button>
                                        <button type="button" class="btn btn-secondary" onclick="hideReviewForm()">Cancel</button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Reviews List -->
                            <div class="reviews-list">
                                @forelse($reviews ?? [] as $review)
                                    <div class="review-item border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">{{ $review->user->name ?? 'Anonymous' }}</h6>
                                                <div class="stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                                        </div>
                                        @if($review->title)
                                            <h6>{{ $review->title }}</h6>
                                        @endif
                                        <p class="mb-0">{{ $review->comment }}</p>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        
                        <!-- Shipping Tab -->
                        <div class="tab-pane fade" id="shipping" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-truck me-2"></i>Shipping Information</h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Free shipping on orders over $100</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Standard delivery: 3-5 business days</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Express delivery: 1-2 business days (+$15)</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Same day delivery available in select areas</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-undo me-2"></i>Returns & Exchanges</h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>30-day return policy</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Free returns on defective items</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Easy online return process</li>
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Refund processed within 5-7 business days</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
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
                <div class="row">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card product-card h-100">
                                <div class="position-relative">
                                    @if($relatedProduct->image)
                                        <img src="{{ asset('storage/' . $relatedProduct->image) }}" class="product-image" alt="{{ $relatedProduct->name }}">
                                    @else
                                        <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    @if($relatedProduct->discount_price && $relatedProduct->discount_price < $relatedProduct->price)
                                        @php
                                            $discount = round((($relatedProduct->price - $relatedProduct->discount_price) / $relatedProduct->price) * 100);
                                        @endphp
                                        <span class="discount-badge">-{{ $discount }}%</span>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">{{ Str::limit($relatedProduct->name, 50) }}</h6>
                                    <div class="mb-2">
                                        <span class="shop-badge">{{ $relatedProduct->shop->name }}</span>
                                    </div>
                                    
                                    <div class="price-section mb-2">
                                        @if($relatedProduct->discount_price && $relatedProduct->discount_price < $relatedProduct->price)
                                            <span class="price-tag">${{ number_format($relatedProduct->discount_price, 2) }}</span>
                                            <span class="original-price ms-2">${{ number_format($relatedProduct->price, 2) }}</span>
                                        @else
                                            <span class="price-tag">${{ number_format($relatedProduct->price, 2) }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <a href="{{ route('customer.product.show', $relatedProduct) }}" class="btn btn-primary btn-sm w-100 mb-2">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                        @if($relatedProduct->quantity > 0)
                                            <a href="{{ route('customer.buy-now', $relatedProduct) }}" class="btn btn-buy-now btn-sm w-100">
                                                <i class="fas fa-shopping-cart me-1"></i>Buy Now
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- More from this Shop -->
    @if($shopProducts->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">More from {{ $product->shop->name }}</h3>
                <div class="row">
                    @foreach($shopProducts as $shopProduct)
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card product-card h-100">
                                <div class="position-relative">
                                    @if($shopProduct->image)
                                        <img src="{{ asset('storage/' . $shopProduct->image) }}" class="product-image" alt="{{ $shopProduct->name }}">
                                    @else
                                        <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    @if($shopProduct->discount_price && $shopProduct->discount_price < $shopProduct->price)
                                        @php
                                            $discount = round((($shopProduct->price - $shopProduct->discount_price) / $shopProduct->price) * 100);
                                        @endphp
                                        <span class="discount-badge">-{{ $discount }}%</span>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">{{ Str::limit($shopProduct->name, 50) }}</h6>
                                    @if($shopProduct->category)
                                        <div class="mb-2">
                                            <span class="category-badge">{{ $shopProduct->category->title }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="price-section mb-2">
                                        @if($shopProduct->discount_price && $shopProduct->discount_price < $shopProduct->price)
                                            <span class="price-tag">${{ number_format($shopProduct->discount_price, 2) }}</span>
                                            <span class="original-price ms-2">${{ number_format($shopProduct->price, 2) }}</span>
                                        @else
                                            <span class="price-tag">${{ number_format($shopProduct->price, 2) }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <a href="{{ route('customer.product.show', $shopProduct) }}" class="btn btn-primary btn-sm w-100 mb-2">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                        @if($shopProduct->quantity > 0)
                                            <a href="{{ route('customer.buy-now', $shopProduct) }}" class="btn btn-buy-now btn-sm w-100">
                                                <i class="fas fa-shopping-cart me-1"></i>Buy Now
                                            </a>
                                        @endif
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

<style>
/* Product Gallery Styles */
.product-gallery {
    position: relative;
}

.main-image-container {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}

.main-product-image {
    height: 500px;
    object-fit: cover;
    transition: transform 0.3s ease;
    cursor: zoom-in;
}

.main-product-image:hover {
    transform: scale(1.05);
}

.image-controls {
    position: absolute;
    top: 15px;
    right: 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.image-controls .btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.9);
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.thumbnail-image {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.thumbnail-image:hover,
.thumbnail-image.active {
    border-color: #007bff;
    transform: scale(1.05);
}

.discount-badge-large {
    position: absolute;
    top: 15px;
    left: 15px;
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

/* Rating Input Styles */
.rating-input {
    display: flex;
    gap: 5px;
}

.rating-input input[type="radio"] {
    display: none;
}

.star-label {
    font-size: 24px;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s ease;
}

.rating-input input[type="radio"]:checked ~ .star-label,
.rating-input input[type="radio"]:checked + .star-label,
.star-label:hover {
    color: #ffc107;
}

/* Product Details Enhancements */
.product-description,
.features-list,
.specifications-content {
    line-height: 1.6;
    font-size: 16px;
}

.review-item:last-child {
    border-bottom: none !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .main-product-image {
        height: 300px;
    }
    
    .image-controls {
        top: 10px;
        right: 10px;
    }
    
    .image-controls .btn {
        width: 35px;
        height: 35px;
    }
}
</style>

<script>
// Image Gallery Functions
function changeMainImage(src) {
    const mainImage = document.getElementById('mainImage');
    if (mainImage) {
        mainImage.src = src;
    }
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail-image').forEach(img => {
        img.classList.remove('active');
    });
    event.target.classList.add('active');
}

function zoomImage() {
    const mainImage = document.getElementById('mainImage');
    if (mainImage) {
        // Create modal for zoomed image
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            cursor: zoom-out;
        `;
        
        const zoomedImg = document.createElement('img');
        zoomedImg.src = mainImage.src;
        zoomedImg.style.cssText = `
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        `;
        
        modal.appendChild(zoomedImg);
        document.body.appendChild(modal);
        
        modal.onclick = () => document.body.removeChild(modal);
    }
}

function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Product link copied to clipboard!');
        });
    }
}

function toggleWishlist(productId) {
    const btn = event.target.closest('.wishlist-btn');
    const icon = btn.querySelector('i');
    
    // Toggle heart icon
    if (icon.classList.contains('far')) {
        icon.classList.remove('far');
        icon.classList.add('fas');
        btn.classList.add('text-danger');
        // Here you would make an AJAX call to add to wishlist
        console.log('Added to wishlist:', productId);
    } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        btn.classList.remove('text-danger');
        // Here you would make an AJAX call to remove from wishlist
        console.log('Removed from wishlist:', productId);
    }
}

// Review Functions
function showReviewForm() {
    document.getElementById('reviewForm').style.display = 'block';
}

function hideReviewForm() {
    document.getElementById('reviewForm').style.display = 'none';
}

// Rating input functionality
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('.rating-input input[type="radio"]');
    const starLabels = document.querySelectorAll('.star-label');
    
    starLabels.forEach((label, index) => {
        label.addEventListener('mouseover', function() {
            // Highlight stars up to hovered star
            starLabels.forEach((star, starIndex) => {
                if (starIndex <= index) {
                    star.style.color = '#ffc107';
                } else {
                    star.style.color = '#ddd';
                }
            });
        });
        
        label.addEventListener('click', function() {
            ratingInputs[index].checked = true;
        });
    });
    
    // Reset on mouse leave
    document.querySelector('.rating-input').addEventListener('mouseleave', function() {
        const checkedInput = document.querySelector('.rating-input input[type="radio"]:checked');
        if (checkedInput) {
            const checkedIndex = Array.from(ratingInputs).indexOf(checkedInput);
            starLabels.forEach((star, starIndex) => {
                if (starIndex <= checkedIndex) {
                    star.style.color = '#ffc107';
                } else {
                    star.style.color = '#ddd';
                }
            });
        } else {
            starLabels.forEach(star => {
                star.style.color = '#ddd';
            });
        }
    });
});
</script>
@endsection
