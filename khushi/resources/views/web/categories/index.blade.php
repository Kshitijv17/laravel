@extends('layouts.app')

@section('title', 'Categories - E-Commerce Store')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Shop by Category</h2>
            <p class="text-muted mb-5">Explore our wide range of product categories</p>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="row g-4">
        @foreach($categories as $category)
        <div class="col-lg-4 col-md-6">
            <div class="card category-card h-100 border-0 shadow-sm">
                <div class="position-relative overflow-hidden">
                    @if($category->image)
                    <img src="{{ $category->image_url }}" class="card-img-top" alt="{{ $category->name }}" 
                         style="height: 200px; object-fit: cover;">
                    @else
                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                         style="height: 200px;">
                        <i class="fas fa-{{ $category->icon ?? 'tag' }} fa-4x text-primary"></i>
                    </div>
                    @endif
                    
                    <!-- Product Count Badge -->
                    <span class="badge bg-primary position-absolute top-0 end-0 m-3">
                        {{ $category->products_count }} Products
                    </span>
                </div>
                
                <div class="card-body text-center p-4">
                    <h5 class="card-title mb-3">{{ $category->name }}</h5>
                    <p class="card-text text-muted mb-4">{{ $category->description }}</p>
                    
                    <!-- Subcategories -->
                    @if($category->children->count() > 0)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">Subcategories:</small>
                        <div class="d-flex flex-wrap justify-content-center gap-1">
                            @foreach($category->children->take(3) as $subcategory)
                            <span class="badge bg-light text-dark">{{ $subcategory->name }}</span>
                            @endforeach
                            @if($category->children->count() > 3)
                            <span class="badge bg-light text-dark">+{{ $category->children->count() - 3 }} more</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-primary">
                        Explore Category <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Featured Categories -->
    @if($featuredCategories->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Featured Categories</h3>
        </div>
    </div>
    
    <div class="row g-4">
        @foreach($featuredCategories as $category)
        <div class="col-lg-6">
            <div class="card featured-category-card border-0 shadow">
                <div class="row g-0">
                    <div class="col-md-4">
                        @if($category->image)
                        <img src="{{ $category->image_url }}" class="img-fluid rounded-start h-100" 
                             alt="{{ $category->name }}" style="object-fit: cover;">
                        @else
                        <div class="d-flex align-items-center justify-content-center bg-light rounded-start h-100">
                            <i class="fas fa-{{ $category->icon ?? 'tag' }} fa-3x text-primary"></i>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">{{ $category->name }}</h5>
                            <p class="card-text">{{ $category->description }}</p>
                            <p class="card-text">
                                <small class="text-muted">{{ $category->products_count }} products available</small>
                            </p>
                            <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-outline-primary">
                                Shop Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Popular Products by Category -->
    @if($popularProducts->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Popular Products</h3>
        </div>
    </div>
    
    <div class="row g-4">
        @foreach($popularProducts as $product)
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
                        <button class="btn btn-sm btn-light rounded-circle wishlist-btn" 
                                data-product-id="{{ $product->id }}"
                                title="Add to Wishlist">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <h6 class="card-title">
                        <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">
                            {{ Str::limit($product->name, 50) }}
                        </a>
                    </h6>
                    
                    <small class="text-muted">{{ $product->category->name }}</small>

                    <!-- Rating -->
                    @if($product->reviews_avg_rating)
                    <div class="rating mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star{{ $i <= $product->reviews_avg_rating ? '' : '-o' }}"></i>
                        @endfor
                        <small class="text-muted ms-1">({{ $product->reviews_count }})</small>
                    </div>
                    @endif

                    <!-- Price -->
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
                    <div class="d-grid">
                        @if($product->stock_quantity > 0)
                        <button class="btn btn-primary add-to-cart-btn" data-product-id="{{ $product->id }}">
                            <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                        </button>
                        @else
                        <button class="btn btn-secondary" disabled>
                            <i class="fas fa-ban me-1"></i> Out of Stock
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.category-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.featured-category-card {
    transition: transform 0.3s ease;
}

.featured-category-card:hover {
    transform: translateY(-2px);
}

.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.product-card .card-img-top {
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .card-img-top {
    transform: scale(1.05);
}
</style>
@endpush

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
