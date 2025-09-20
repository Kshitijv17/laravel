@extends('layouts.app')

@section('title', $category->name . ' - Products')

@push('styles')
<style>
    :root {
        --fashion-primary: #ff3f6c;
        --fashion-secondary: #ff905a;
        --fashion-accent: #f2c210;
        --fashion-dark: #111827;
        --fashion-muted: #6b7280;
        --fashion-bg: #fff7f9;
    }

    /* Category Header */
    .category-hero {
        background: linear-gradient(120deg, var(--fashion-primary), var(--fashion-secondary));
        color: #fff;
        padding: 80px 0;
        border-radius: 18px;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
    }

    .category-hero::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        transform: translate(100px, -100px);
    }

    /* Filters Section */
    .filters-section {
        background: #fff;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-input {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 15px;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .filter-input:focus {
        border-color: var(--fashion-primary);
        box-shadow: 0 0 0 3px rgba(255,63,108,0.1);
        outline: none;
    }

    .filter-btn {
        background: var(--fashion-primary);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .filter-btn:hover {
        background: #e7335c;
        transform: translateY(-2px);
    }

    /* Product Grid */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .product-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.35s ease;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(255,63,108,0.15);
    }

    .product-image {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.08);
    }

    .product-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--fashion-accent);
        color: var(--fashion-dark);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .wishlist-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--fashion-primary);
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .wishlist-btn:hover {
        background: var(--fashion-primary);
        color: #fff;
        transform: scale(1.1);
    }

    .product-info {
        padding: 20px;
    }

    .product-title {
        font-weight: 700;
        color: var(--fashion-dark);
        margin-bottom: 8px;
        font-size: 16px;
    }

    .product-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .product-title a:hover {
        color: var(--fashion-primary);
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 10px;
    }

    .star {
        color: var(--fashion-accent);
        font-size: 14px;
    }

    .star.empty {
        color: #e5e7eb;
    }

    .product-price {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .current-price {
        font-size: 18px;
        font-weight: 700;
        color: var(--fashion-primary);
    }

    .original-price {
        font-size: 14px;
        color: var(--fashion-muted);
        text-decoration: line-through;
    }

    .add-to-cart-btn {
        width: 100%;
        background: var(--fashion-primary);
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .add-to-cart-btn:hover {
        background: #e7335c;
        transform: translateY(-2px);
    }

    .add-to-cart-btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
    }

    /* Breadcrumb */
    .fashion-breadcrumb {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 12px 20px;
        margin-bottom: 30px;
    }

    .fashion-breadcrumb a {
        color: var(--fashion-primary);
        text-decoration: none;
        font-weight: 500;
    }

    .fashion-breadcrumb a:hover {
        color: #e7335c;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        background: var(--fashion-bg);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: var(--fashion-primary);
        font-size: 32px;
    }

    /* Pagination */
    .pagination {
        justify-content: center;
        margin-top: 40px;
    }

    .pagination .page-link {
        border: 2px solid #e5e7eb;
        color: var(--fashion-dark);
        padding: 10px 15px;
        margin: 0 5px;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        border-color: var(--fashion-primary);
        color: var(--fashion-primary);
        background: var(--fashion-bg);
    }

    .pagination .page-item.active .page-link {
        background: var(--fashion-primary);
        border-color: var(--fashion-primary);
        color: #fff;
    }
</style>
@endpush

@section('content')
<!-- Category Hero -->
<section class="category-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">{{ $category->name }}</h1>
                @if($category->description)
                <p class="lead mb-4">{{ $category->description }}</p>
                @endif
                <div class="d-flex align-items-center gap-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-box fa-lg"></i>
                        <span>{{ $products->total() }} Products</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-star fa-lg"></i>
                        <span>Top Rated</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end d-none d-lg-block">
                @if($category->image)
                    <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" 
                         style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; opacity: 0.8;">
                @else
                    <i class="fas fa-{{ $category->icon ?? 'tag' }}" style="font-size: 120px; opacity: 0.2;"></i>
                @endif
            </div>
        </div>
    </div>
</section>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb fashion-breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <!-- Filters and Sorting -->
    <div class="filters-section">
        <form method="GET" action="{{ route('categories.show', $category->slug) }}">
            <div class="filter-group">
                <div class="d-flex align-items-center gap-2">
                    <label class="fw-semibold text-muted">Price Range:</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                           placeholder="Min" class="filter-input" style="width: 80px;">
                    <span class="text-muted">-</span>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                           placeholder="Max" class="filter-input" style="width: 80px;">
                </div>

                <div class="d-flex align-items-center gap-2">
                    <label class="fw-semibold text-muted">Sort by:</label>
                    <select name="sort" class="filter-input">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                    </select>
                </div>

                <button type="submit" class="filter-btn">
                    <i class="fas fa-filter me-2"></i>Apply Filters
                </button>
                
                @if(request()->hasAny(['min_price', 'max_price', 'sort']))
                <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-outline-secondary" style="border-radius: 10px;">
                    <i class="fas fa-times me-2"></i>Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="product-grid">
            @foreach($products as $product)
                <div class="product-card">
                    <div class="product-image">
                        @if($product->image)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100" style="background: var(--fashion-bg);">
                                <i class="fas fa-image fa-3x" style="color: var(--fashion-muted);"></i>
                            </div>
                        @endif
                        
                        @if($product->discount_percentage > 0)
                            <span class="product-badge">
                                -{{ $product->discount_percentage }}%
                            </span>
                        @endif

                        <button class="wishlist-btn" data-product-id="{{ $product->id }}">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                    
                    <div class="product-info">
                        <h6 class="product-title">
                            <a href="{{ route('products.show', $product->slug) }}">
                                {{ Str::limit($product->name, 50) }}
                            </a>
                        </h6>
                        
                        @if($product->reviews && $product->reviews->count() > 0)
                            <div class="product-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $product->average_rating ? 'star' : 'star empty' }}"></i>
                                @endfor
                                <small class="text-muted">({{ $product->reviews->count() }})</small>
                            </div>
                        @endif
                        
                        <div class="product-price">
                            @if($product->discount_price && $product->discount_price < $product->price)
                                <span class="current-price">${{ number_format($product->discount_price, 2) }}</span>
                                <span class="original-price">${{ number_format($product->price, 2) }}</span>
                            @else
                                <span class="current-price">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                        
                        @if($product->stock > 0)
                            <button class="add-to-cart-btn" data-product-id="{{ $product->id }}">
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        @else
                            <button class="add-to-cart-btn" disabled>
                                <i class="fas fa-ban me-2"></i>Out of Stock
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <nav aria-label="Products pagination">
            {{ $products->appends(request()->query())->links() }}
        </nav>
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3 class="fw-bold mb-3" style="color: var(--fashion-dark);">No Products Found</h3>
            <p class="text-muted mb-4">We couldn't find any products matching your current filters in {{ $category->name }}.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('categories.show', $category->slug) }}" class="filter-btn">
                    <i class="fas fa-times me-2"></i>Clear All Filters
                </a>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary" style="border-radius: 10px; padding: 10px 20px;">
                    <i class="fas fa-arrow-left me-2"></i>Browse Categories
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add to cart functionality
    $('.add-to-cart-btn').on('click', function() {
        const productId = $(this).data('product-id');
        const button = $(this);
        
        if (button.prop('disabled')) return;
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Adding...');
        
        // Add your cart functionality here
        addToCart(productId, 1);
        
        setTimeout(function() {
            button.prop('disabled', false).html('<i class="fas fa-shopping-cart me-2"></i>Add to Cart');
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
                    button.toggleClass('active');
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

    // Product card hover effects
    $('.product-card').hover(
        function() {
            $(this).find('.product-image img').addClass('hovered');
        },
        function() {
            $(this).find('.product-image img').removeClass('hovered');
        }
    );
});
</script>
@endpush
