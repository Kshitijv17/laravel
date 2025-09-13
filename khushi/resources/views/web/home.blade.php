@extends('layouts.app')

@section('title', 'Home - E-Commerce Store')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Discover Amazing Products</h1>
                <p class="lead mb-4">Shop the latest trends and find everything you need at unbeatable prices. Quality guaranteed with fast shipping.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">Shop Now</a>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-lg">Browse Categories</a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://via.placeholder.com/500x400/ffffff/3b82f6?text=Hero+Image" alt="Hero" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Banners Section -->
@if($banners->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($banners as $index => $banner)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3>{{ $banner->title }}</h3>
                            <p>{{ $banner->description }}</p>
                            @if($banner->button_text && $banner->button_url)
                            <a href="{{ $banner->button_url }}" class="btn btn-primary">{{ $banner->button_text }}</a>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <img src="{{ $banner->image ?? 'https://via.placeholder.com/400x250/3b82f6/ffffff?text=Banner' }}" 
                                 alt="{{ $banner->title }}" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($banners->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
            @endif
        </div>
    </div>
</section>
@endif

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-3">Shop by Category</h2>
                <p class="lead text-muted">Explore our wide range of product categories</p>
            </div>
        </div>
        
        <div class="row g-4">
            @foreach($categories as $category)
            <div class="col-lg-4 col-md-6">
                <div class="card category-card h-100 text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-{{ $category->icon ?? 'tag' }} fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">{{ $category->name }}</h5>
                        <p class="card-text text-muted">{{ $category->description }}</p>
                        <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-outline-primary">
                            View Products <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-3">Featured Products</h2>
                <p class="lead text-muted">Handpicked products just for you</p>
            </div>
        </div>
        
        <div class="row g-4">
            @foreach($featuredProducts as $product)
            <div class="col-lg-3 col-md-6">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <img src="{{ $product->images->first()->url ?? 'https://via.placeholder.com/300x250/f8f9fa/6c757d?text=Product' }}" 
                             class="card-img-top" alt="{{ $product->name }}">
                        @if($product->discount_percentage > 0)
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                            -{{ $product->discount_percentage }}%
                        </span>
                        @endif
                        <div class="position-absolute top-0 end-0 m-2">
                            <button class="btn btn-sm btn-light rounded-circle wishlist-btn" data-product-id="{{ $product->id }}">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">{{ Str::limit($product->name, 50) }}</h6>
                        <div class="mb-2">
                            @if($product->reviews_avg_rating)
                            <div class="rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $product->reviews_avg_rating ? '' : '-o' }}"></i>
                                @endfor
                                <small class="text-muted ms-1">({{ $product->reviews_count }})</small>
                            </div>
                            @endif
                        </div>
                        <div class="price-section">
                            @if($product->sale_price && $product->sale_price < $product->price)
                            <span class="price">${{ number_format($product->sale_price, 2) }}</span>
                            <span class="original-price ms-2">${{ number_format($product->price, 2) }}</span>
                            @else
                            <span class="price">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary add-to-cart-btn" data-product-id="{{ $product->id }}">
                                <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                            </button>
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-secondary btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-5">
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                View All Products <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>

<!-- New Products Section -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-3">New Arrivals</h2>
                <p class="lead text-muted">Check out our latest products</p>
            </div>
        </div>
        
        <div class="row g-4">
            @foreach($newProducts as $product)
            <div class="col-lg-3 col-md-6">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <img src="{{ $product->images->first()->url ?? 'https://via.placeholder.com/300x250/f8f9fa/6c757d?text=Product' }}" 
                             class="card-img-top" alt="{{ $product->name }}">
                        <span class="badge bg-success position-absolute top-0 start-0 m-2">New</span>
                        <div class="position-absolute top-0 end-0 m-2">
                            <button class="btn btn-sm btn-light rounded-circle wishlist-btn" data-product-id="{{ $product->id }}">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">{{ Str::limit($product->name, 50) }}</h6>
                        <div class="mb-2">
                            @if($product->reviews_avg_rating)
                            <div class="rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $product->reviews_avg_rating ? '' : '-o' }}"></i>
                                @endfor
                                <small class="text-muted ms-1">({{ $product->reviews_count }})</small>
                            </div>
                            @endif
                        </div>
                        <div class="price-section">
                            <span class="price">${{ number_format($product->price, 2) }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary add-to-cart-btn" data-product-id="{{ $product->id }}">
                                <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                            </button>
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-secondary btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 text-center">
                <div class="mb-3">
                    <i class="fas fa-shipping-fast fa-3x"></i>
                </div>
                <h5>Free Shipping</h5>
                <p class="mb-0">Free shipping on orders over $50</p>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
                <div class="mb-3">
                    <i class="fas fa-undo fa-3x"></i>
                </div>
                <h5>Easy Returns</h5>
                <p class="mb-0">30-day return policy</p>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
                <div class="mb-3">
                    <i class="fas fa-headset fa-3x"></i>
                </div>
                <h5>24/7 Support</h5>
                <p class="mb-0">Round-the-clock customer service</p>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
                <div class="mb-3">
                    <i class="fas fa-shield-alt fa-3x"></i>
                </div>
                <h5>Secure Payment</h5>
                <p class="mb-0">100% secure transactions</p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add to cart functionality
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
