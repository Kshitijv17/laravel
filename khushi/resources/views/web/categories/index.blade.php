@extends('layouts.app')

@section('title', 'Categories - E-Commerce Store')

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

    /* Hero Section */
    .categories-hero {
        background: linear-gradient(120deg, var(--fashion-primary), var(--fashion-secondary));
        color: #fff;
        padding: 60px 0;
        border-radius: 18px;
        margin-bottom: 40px;
    }

    /* Category Cards */
    .category-tile {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.35s ease;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .category-tile:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(255,63,108,0.15);
    }

    .category-tile .tile-image {
        position: relative;
        height: 240px;
        overflow: hidden;
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    }

    .category-tile .tile-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }

    .category-tile:hover .tile-image img {
        transform: scale(1.08);
    }

    .category-tile .tile-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, transparent 0%, rgba(17,24,39,0.7) 100%);
        opacity: 0;
        transition: opacity 0.35s ease;
    }

    .category-tile:hover .tile-overlay {
        opacity: 1;
    }

    .category-tile .tile-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 20px;
        color: #fff;
        transform: translateY(20px);
        opacity: 0;
        transition: all 0.35s ease;
    }

    .category-tile:hover .tile-content {
        transform: translateY(0);
        opacity: 1;
    }

    .category-tile .tile-info {
        padding: 20px;
        text-align: center;
    }

    .category-tile .tile-info h5 {
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--fashion-dark);
    }

    .category-tile .tile-info p {
        color: var(--fashion-muted);
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    .category-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.95);
        color: var(--fashion-primary);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }

    .category-icon {
        width: 60px;
        height: 60px;
        background: var(--fashion-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 24px;
        margin: 0 auto 15px;
    }

    .explore-btn {
        background: var(--fashion-primary);
        border: none;
        color: #fff;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .explore-btn:hover {
        background: #e7335c;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255,63,108,0.3);
    }

    /* Breadcrumb styling */
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
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="categories-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Discover Our Collections</h1>
                <p class="lead mb-4">Explore our carefully curated categories and find exactly what you're looking for</p>
                <div class="d-flex align-items-center gap-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-tags fa-lg"></i>
                        <span>{{ $categories->count() }} Categories</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-box fa-lg"></i>
                        <span>{{ $categories->sum('products_count') }}+ Products</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end d-none d-lg-block">
                <i class="fas fa-shopping-bag" style="font-size: 120px; opacity: 0.2;"></i>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb fashion-breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>

    <!-- Categories Grid -->
    <div class="row g-4 mb-5">
        @foreach($categories as $category)
        <div class="col-lg-4 col-md-6">
            <div class="category-tile">
                <div class="tile-image">
                    @if($category->image)
                        <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="category-icon">
                                <i class="fas fa-{{ $category->icon ?? 'tag' }}"></i>
                            </div>
                        </div>
                    @endif
                    
                    <div class="tile-overlay"></div>
                    <div class="tile-content">
                        <h6 class="fw-bold mb-2">{{ $category->name }}</h6>
                        <p class="mb-0 small">{{ $category->products_count }} Products Available</p>
                    </div>
                    
                    <span class="category-badge">
                        {{ $category->products_count }} items
                    </span>
                </div>
                
                <div class="tile-info">
                    <h5>{{ $category->name }}</h5>
                    <p>{{ Str::limit($category->description, 80) }}</p>
                    
                    @if($category->children && $category->children->count() > 0)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">Subcategories:</small>
                        <div class="d-flex flex-wrap justify-content-center gap-1">
                            @foreach($category->children->take(3) as $subcategory)
                            <span class="badge" style="background: var(--fashion-bg); color: var(--fashion-primary);">{{ $subcategory->name }}</span>
                            @endforeach
                            @if($category->children->count() > 3)
                            <span class="badge" style="background: var(--fashion-bg); color: var(--fashion-primary);">+{{ $category->children->count() - 3 }} more</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <a href="{{ route('categories.show', $category->slug) }}" class="explore-btn">
                        Explore Collection <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Quick Stats Section -->
    <section class="py-5" style="background: var(--fashion-bg);">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-icon mb-3">
                            <i class="fas fa-tags fa-2x" style="color: var(--fashion-primary);"></i>
                        </div>
                        <h4 class="fw-bold" style="color: var(--fashion-dark);">{{ $categories->count() }}</h4>
                        <p class="text-muted mb-0">Categories</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-icon mb-3">
                            <i class="fas fa-box fa-2x" style="color: var(--fashion-secondary);"></i>
                        </div>
                        <h4 class="fw-bold" style="color: var(--fashion-dark);">{{ $categories->sum('products_count') }}+</h4>
                        <p class="text-muted mb-0">Products</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-icon mb-3">
                            <i class="fas fa-star fa-2x" style="color: var(--fashion-accent);"></i>
                        </div>
                        <h4 class="fw-bold" style="color: var(--fashion-dark);">4.8</h4>
                        <p class="text-muted mb-0">Avg Rating</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-icon mb-3">
                            <i class="fas fa-shipping-fast fa-2x" style="color: var(--fashion-primary);"></i>
                        </div>
                        <h4 class="fw-bold" style="color: var(--fashion-dark);">Free</h4>
                        <p class="text-muted mb-0">Shipping</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h3 class="fw-bold mb-3" style="color: var(--fashion-dark);">Can't Find What You're Looking For?</h3>
                    <p class="text-muted mb-4">Browse all our products or get in touch with our customer service team</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('products.index') }}" class="explore-btn">
                            <i class="fas fa-search me-2"></i>Browse All Products
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 25px;">
                            <i class="fas fa-headset me-2"></i>Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Category tile hover effects
    $('.category-tile').hover(
        function() {
            $(this).find('.tile-overlay').fadeIn(300);
            $(this).find('.tile-content').slideDown(300);
        },
        function() {
            $(this).find('.tile-overlay').fadeOut(300);
            $(this).find('.tile-content').slideUp(300);
        }
    );

    // Smooth scroll for stats section
    $('.stat-item').each(function(index) {
        $(this).delay(index * 100).animate({
            opacity: 1
        }, 500);
    });
});
</script>
@endpush
