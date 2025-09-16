@extends('layouts.web')

@section('title', 'Home')

@push('styles')
<style>
    /* Override all previous styles */
    .main-content {
        margin: 0 !important;
        border-radius: 0 !important;
        background: white !important;
        backdrop-filter: none !important;
        box-shadow: none !important;
        border: none !important;
        min-height: 100vh !important;
        overflow: visible !important;
    }
    
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%) !important;
        padding: 100px 0 !important;
        margin: 0 !important;
        border-radius: 0 !important;
        min-height: 80vh !important;
    }
    
    section {
        padding: 80px 0 !important;
        margin: 0 !important;
        background: white !important;
    }
    
    .product-card-premium {
        background: white !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.3s ease !important;
    }
    
    .product-card-premium:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }
    
    .category-tile-premium {
        background: white !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.3s ease !important;
        padding: 40px 20px !important;
        text-align: center !important;
        min-height: 200px !important;
    }
    
    .category-tile-premium:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }
    
    .btn-premium {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border: none !important;
        color: white !important;
        padding: 16px 32px !important;
        border-radius: 12px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        transition: all 0.3s ease !important;
    }
    
    .btn-premium:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4) !important;
        color: white !important;
    }
</style>
@endpush

@section('content')
<div style="background: white; min-height: 100vh; margin: 0; padding: 0;">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="display-2 fw-bold mb-4 text-white">
                            Fashion That <span class="text-warning">Defines</span> You
                        </h1>
                        <p class="lead mb-5 text-white fs-4">
                            Discover premium fashion that speaks to your soul. From cutting-edge streetwear to timeless elegance.
                        </p>
                        <div class="d-flex gap-3 mb-4">
                            <a href="#products" class="btn-premium">
                                <i class="fas fa-shopping-bag"></i>
                                Shop Collection
                            </a>
                            <a href="#categories" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill">
                                <i class="fas fa-compass me-2"></i>
                                Explore
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=600&h=600&fit=crop" 
                             alt="Fashion Collection" 
                             class="img-fluid rounded-4 shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-3 fw-bold mb-4" style="background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Shop by Category
                </h2>
            </div>
            
            <div class="row g-4">
                @if($categories && $categories->count() > 0)
                    @foreach($categories as $index => $category)
                    <div class="col-lg-4 col-md-6">
                        <div class="category-tile-premium h-100">
                            <div class="mb-3">
                                @switch($category->name)
                                    @case('Men')
                                        <i class="fas fa-male fa-3x" style="color: #667eea;"></i>
                                        @break
                                    @case('Women')
                                        <i class="fas fa-female fa-3x" style="color: #764ba2;"></i>
                                        @break
                                    @case('Kids')
                                        <i class="fas fa-child fa-3x" style="color: #f093fb;"></i>
                                        @break
                                    @case('Footwear')
                                        <i class="fas fa-shoe-prints fa-3x" style="color: #4facfe;"></i>
                                        @break
                                    @case('Accessories')
                                        <i class="fas fa-gem fa-3x" style="color: #00f2fe;"></i>
                                        @break
                                    @default
                                        <i class="fas fa-tshirt fa-3x" style="color: #667eea;"></i>
                                @endswitch
                            </div>
                            <h4 class="fw-bold mb-2">{{ $category->name }}</h4>
                            <p class="text-muted mb-4">{{ $category->products_count ?? 0 }} Items</p>
                            <a href="#" class="btn btn-outline-primary rounded-pill px-4 py-2">
                                Explore Collection
                            </a>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12 text-center">
                        <p class="text-muted">No categories available at the moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section id="products">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-3 fw-bold mb-4" style="background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Featured Products
                </h2>
            </div>
            
            <div class="row g-4">
                @if($featuredProducts && $featuredProducts->count() > 0)
                    @foreach($featuredProducts as $index => $product)
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card-premium h-100">
                            <div class="position-relative" style="height: 280px; overflow: hidden; border-radius: 16px 16px 0 0;">
                                @if($product->image)
                                    <img src="{{ $product->image }}" 
                                         alt="{{ $product->name }}" 
                                         class="img-fluid w-100 h-100" style="object-fit: cover;">
                                @else
                                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=280&fit=crop" 
                                         alt="{{ $product->name }}" 
                                         class="img-fluid w-100 h-100" style="object-fit: cover;">
                                @endif
                                
                                @if($product->discount_price && $product->discount_price < $product->price)
                                <div class="position-absolute top-0 start-0 p-3">
                                    <div class="badge" style="background: linear-gradient(135deg, #ff6b6b, #ee5a52); color: white; padding: 8px 12px; border-radius: 20px;">
                                        {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <div class="p-3">
                                <h5 class="fw-bold mb-2">
                                    {{ $product->name }}
                                </h5>
                                
                                <div class="mb-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star" style="color: #ffd700; font-size: 0.9rem;"></i>
                                    @endfor
                                    <span class="text-muted ms-2 small">({{ $product->reviews_count ?? rand(15, 89) }} reviews)</span>
                                </div>
                                
                                <div class="mb-3">
                                    @if($product->discount_price && $product->discount_price < $product->price)
                                        <span class="fw-bold fs-5 text-primary">${{ number_format($product->discount_price, 2) }}</span>
                                        <span class="text-muted text-decoration-line-through ms-2">${{ number_format($product->price, 2) }}</span>
                                    @else
                                        <span class="fw-bold fs-5 text-primary">${{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                
                                <button class="btn-premium w-100">
                                    <i class="fas fa-shopping-cart"></i>
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12 text-center">
                        <p class="text-muted">No featured products available at the moment.</p>
                    </div>
                @endif
            </div>
            
            @if($featuredProducts && $featuredProducts->count() > 0)
            <div class="text-center mt-5">
                <a href="#" class="btn-premium" style="padding: 18px 40px; font-size: 1.2rem;">
                    View All Products
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @endif
        </div>
    </section>

    <!-- Features Section -->
    <section style="background: #f8f9fa;">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-shipping-fast fa-3x" style="color: #667eea;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Free Shipping</h5>
                        <p class="text-muted mb-0">Free shipping on orders over $99</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-undo fa-3x" style="color: #667eea;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Easy Returns</h5>
                        <p class="text-muted mb-0">30-day return policy</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-headset fa-3x" style="color: #667eea;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">24/7 Support</h5>
                        <p class="text-muted mb-0">Round-the-clock assistance</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-shield-alt fa-3x" style="color: #667eea;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Secure Payment</h5>
                        <p class="text-muted mb-0">100% secure transactions</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
        animation: fadeInUp 1s ease-out 0.2s both;
    }

    .hero-cta {
        animation: fadeInUp 1s ease-out 0.4s both;
    }

    /* Enhanced Category Grid */
    .category-showcase {
        padding: 4rem 0;
    }

    .category-card {
        position: relative;
        height: 300px;
        border-radius: 1.5rem;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
    }

    .category-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.1) 100%);
        transition: all 0.3s ease;
        z-index: 2;
    }

    .category-card:hover::before {
        background: linear-gradient(135deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.05) 100%);
    }

    .category-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.4s ease;
    }

    .category-card:hover {
        transform: translateY(-15px) rotateY(5deg);
        box-shadow: 0 25px 50px rgba(0,0,0,0.2);
    }

    .category-card:hover img {
        transform: scale(1.1);
    }

    .category-label {
        position: absolute;
        bottom: 2rem;
        left: 2rem;
        right: 2rem;
        z-index: 3;
        color: white;
        text-align: center;
    }

    .category-name {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }

    .category-count {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    /* Enhanced Product Cards */
    .product-showcase {
        padding: 4rem 0;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    .product-card-enhanced {
        background: white;
        border-radius: 1.5rem;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        position: relative;
    }

    .product-card-enhanced::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 1;
    }

    .product-card-enhanced:hover::before {
        opacity: 1;
    }

    .product-card-enhanced:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .product-image-container {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.4s ease;
    }

    .product-card-enhanced:hover .product-image {
        transform: scale(1.05);
    }

    .product-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.8rem;
        font-weight: 600;
        z-index: 2;
        animation: pulse 2s infinite;
    }

    .product-actions {
        position: absolute;
        top: 1rem;
        right: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        opacity: 0;
        transform: translateX(20px);
        transition: all 0.3s ease;
        z-index: 2;
    }

    .product-card-enhanced:hover .product-actions {
        opacity: 1;
        transform: translateX(0);
    }

    .action-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        color: #333;
    }

    .action-btn:hover {
        background: #667eea;
        color: white;
        transform: scale(1.1);
    }

    .product-info {
        padding: 1.5rem;
        position: relative;
        z-index: 2;
    }

    .product-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2d3748;
    }

    .product-price {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .current-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: #00d4aa;
    }

    .original-price {
        font-size: 1rem;
        color: #a0aec0;
        text-decoration: line-through;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-bottom: 1rem;
    }

    .star {
        color: #fbbf24;
        font-size: 0.9rem;
    }

    .rating-count {
        color: #718096;
        font-size: 0.8rem;
        margin-left: 0.5rem;
    }

    /* Features Section */
    .features-section {
        padding: 4rem 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .features-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .feature-item {
        text-align: center;
        padding: 2rem;
        position: relative;
        z-index: 2;
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        transition: all 0.3s ease;
    }

    .feature-item:hover .feature-icon {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1) rotateY(180deg);
    }

    /* Newsletter Section */
    .newsletter-section {
        padding: 4rem 0;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .newsletter-form {
        max-width: 500px;
        margin: 0 auto;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .newsletter-input {
        flex: 1;
        padding: 1rem 1.5rem;
        border: none;
        border-radius: 3rem;
        font-size: 1rem;
        min-width: 250px;
    }

    .newsletter-btn {
        padding: 1rem 2rem;
        background: white;
        color: #f5576c;
        border: none;
        border-radius: 3rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .newsletter-btn:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    /* Scroll Animations */
    .animate-on-scroll {
        opacity: 0;
        transform: translateY(50px);
        transition: all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .animate-on-scroll.animated {
        opacity: 1;
        transform: translateY(0);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .category-card {
            height: 200px;
        }
        
        .newsletter-form {
            flex-direction: column;
        }

@section('content')
<!-- Premium Hero Section -->
<section class="hero-section position-relative overflow-hidden">
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6">
                <div class="hero-content fade-in-up">
                    <h1 class="display-1 fw-black mb-4 text-white position-relative">
                        Fashion That Defines You
                        <span class="d-block fs-2 fw-light opacity-75 mt-2">Elevate Your Style</span>
                    </h1>
                    <p class="lead mb-5 text-white fs-4 lh-lg">
                        Discover premium fashion that speaks to your soul. From cutting-edge streetwear to 
                        timeless elegance, find pieces that tell your unique story.
                    </p>
                    <div class="d-flex gap-4 flex-wrap mb-4">
                        <a href="{{ route('products.index') }}" class="btn-premium">
                            <i class="fas fa-shopping-bag"></i>
                            Shop Collection
                        </a>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill fw-semibold border-2">
                            <i class="fas fa-compass me-2"></i>
                            Explore Categories
                        </a>
                    </div>
                    <div class="hero-stats d-flex gap-4 mt-5">
                        <div class="stat-item text-center">
                            <div class="fs-2 fw-bold text-white mb-1">10K+</div>
                            <div class="small text-white-50">Happy Customers</div>
                        </div>
                        <div class="stat-item text-center">
                            <div class="fs-2 fw-bold text-white mb-1">500+</div>
                            <div class="small text-white-50">Premium Products</div>
                        </div>
                        <div class="stat-item text-center">
                            <div class="fs-2 fw-bold text-white mb-1">50+</div>
                            <div class="small text-white-50">Global Brands</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image fade-in-right" data-delay="200">
                    <div class="position-relative">
                        <div class="hero-image-stack">
                            <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=600&h=600&fit=crop" 
                                 alt="Fashion Collection" 
                                 class="img-fluid rounded-4 shadow-lg main-hero-img">
                            <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=300&h=400&fit=crop" 
                                 alt="Fashion Detail" 
                                 class="img-fluid rounded-3 shadow floating-img-1">
                            <img src="https://images.unsplash.com/photo-1544966503-7cc5ac882d5f?w=250&h=300&fit=crop" 
                                 alt="Accessories" 
                                 class="img-fluid rounded-3 shadow floating-img-2">
                        </div>
                        <div class="floating-elements">
                            <div class="floating-badge position-absolute top-0 end-0 m-3">
                                <span class="badge bg-gradient text-white px-3 py-2 rounded-pill fs-6 fw-bold shadow">
                                    <i class="fas fa-star me-1"></i>
                                    New Collection
                                </span>
                            </div>
                            <div class="floating-discount position-absolute bottom-0 start-0 m-3">
                                <div class="bg-white rounded-circle p-3 shadow-lg text-center" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                    <div>
                                        <div class="fw-bold text-primary fs-5">50%</div>
                                        <div class="small text-muted">OFF</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Premium Categories Section -->
<section class="py-5" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-3 fw-bold mb-4 fade-in-up" style="background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Shop by Category
            </h2>
            <p class="lead text-muted fs-3 fade-in-up stagger-1 mb-5">
                Discover your perfect style across our premium collections
            </p>
        </div>
        
        <div class="row g-4">
            @foreach($categories as $index => $category)
            <div class="col-lg-4 col-md-6">
                <div class="category-tile-premium h-100 fade-in-up stagger-{{ ($index % 6) + 1 }}">
                    <div class="category-icon-premium mb-3">
                        @switch($category->name)
                            @case('Men')
                                <i class="fas fa-male"></i>
                                @break
                            @case('Women')
                                <i class="fas fa-female"></i>
                                @break
                            @case('Kids')
                                <i class="fas fa-child"></i>
                                @break
                            @case('Footwear')
                                <i class="fas fa-shoe-prints"></i>
                                @break
                            @case('Accessories')
                                <i class="fas fa-gem"></i>
                                @break
                            @default
                                <i class="fas fa-tshirt"></i>
                        @endswitch
                    </div>
                    <h4 class="category-title-premium">{{ $category->name }}</h4>
                    <p class="category-count-premium mb-4">{{ $category->products_count ?? rand(15, 150) }} Premium Items</p>
                    <a href="{{ route('categories.show', $category->slug) }}" 
                       class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold">
                        Explore Collection
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Enhanced Product Showcase -->
<section class="product-showcase">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll">
            <h2 class="display-4 fw-bold mb-3" style="color: #2d3748;">Featured Products</h2>
            <p class="lead text-muted">Handpicked items that define contemporary fashion</p>
        </div>
        
        <div class="row g-4">
            @foreach($featuredProducts->take(8) as $product)
            <div class="col-lg-3 col-md-6 animate-on-scroll">
                <div class="product-card-enhanced">
                    <div class="product-image-container">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-image">
                        
                        @if($product->discount_percentage > 0)
                        <div class="product-badge">-{{ $product->discount_percentage }}%</div>
                        @endif
                        
                        <div class="product-actions">
                            <button class="action-btn wishlist-btn" data-product-id="{{ $product->id }}" title="Add to Wishlist">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="action-btn" title="Quick View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn" title="Compare">
                                <i class="fas fa-balance-scale"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="product-info">
                        <h6 class="product-title">{{ Str::limit($product->name, 40) }}</h6>
                        
                        <div class="product-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star star{{ $i <= 4 ? '' : ' text-muted' }}"></i>
                            @endfor
                            <span class="rating-count">({{ rand(10, 150) }})</span>
                        </div>
                        
                        <div class="product-price">
                            @if($product->discount_price && $product->discount_price < $product->price)
                            <span class="current-price">${{ number_format($product->discount_price, 2) }}</span>
                            <span class="original-price">${{ number_format($product->price, 2) }}</span>
                            @else
                            <span class="current-price">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                        
                        <div class="d-grid">
                            <button class="btn btn-primary add-to-cart-btn" data-product-id="{{ $product->id }}">
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-5 animate-on-scroll">
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                View All Products <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Enhanced Features Section -->
<section class="features-section">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll">
            <h2 class="display-4 fw-bold mb-3">Why Choose Us</h2>
            <p class="lead">Experience the best in fashion retail</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 animate-on-scroll">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Free Shipping</h5>
                    <p>Free delivery on orders over $50. Fast and reliable shipping worldwide.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 animate-on-scroll">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-undo-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Easy Returns</h5>
                    <p>30-day hassle-free returns. Not satisfied? Get your money back.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 animate-on-scroll">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5 class="fw-bold mb-3">24/7 Support</h5>
                    <p>Round-the-clock customer service. We're here to help anytime.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 animate-on-scroll">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Secure Payment</h5>
                    <p>100% secure transactions. Your payment information is safe with us.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section">
    <div class="container">
        <div class="text-center animate-on-scroll">
            <h2 class="display-5 fw-bold mb-3">Stay in Style</h2>
            <p class="lead mb-4">Subscribe to our newsletter and be the first to know about new collections and exclusive offers</p>
            
            <form class="newsletter-form">
                <input type="email" class="newsletter-input" placeholder="Enter your email address" required>
                <button type="submit" class="newsletter-btn">
                    Subscribe <i class="fas fa-paper-plane ms-2"></i>
                </button>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Newsletter form submission
    $('.newsletter-form').on('submit', function(e) {
        e.preventDefault();
        const email = $('.newsletter-input').val();
        
        if (email) {
            showNotification('success', 'Thank you for subscribing! Check your email for confirmation.');
            $('.newsletter-input').val('');
        }
    });
    
    // Enhanced scroll animations with stagger effect
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('animated');
                }, index * 100);
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
    
    // Product quick view functionality
    $('.action-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const icon = $(this).find('i');
        if (icon.hasClass('fa-eye')) {
            showNotification('info', 'Quick view feature coming soon!');
        } else if (icon.hasClass('fa-balance-scale')) {
            showNotification('info', 'Product added to comparison!');
        }
    });
    
    // Enhanced add to cart with animation
    $('.add-to-cart-btn').on('click', function() {
        const button = $(this);
        const originalText = button.html();
        
        button.prop('disabled', true)
              .html('<i class="fas fa-spinner fa-spin me-2"></i>Adding...');
        
        // Simulate API call
        setTimeout(() => {
            button.html('<i class="fas fa-check me-2"></i>Added!')
                  .removeClass('btn-primary')
                  .addClass('btn-success');
            
            showNotification('success', 'Product added to cart successfully!');
            
            setTimeout(() => {
                button.prop('disabled', false)
                      .html(originalText)
                      .removeClass('btn-success')
                      .addClass('btn-primary');
            }, 2000);
        }, 1500);
    });
});
</script>
@endpush
