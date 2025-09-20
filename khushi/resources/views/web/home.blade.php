@extends('layouts.app')

@section('title', 'Home - E-Commerce Store')

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

    /* Promo strip */
    .promo-strip {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 10px 16px;
        margin-bottom: 18px;
    }
    .promo-strip .item { display:flex; align-items:center; gap:10px; color: var(--fashion-dark); font-weight: 600; }
    .promo-strip .item small{ display:block; color: var(--fashion-muted); font-weight: 500; }

    /* Hero */
    .hero-section {
        background: linear-gradient(120deg, var(--fashion-primary), var(--fashion-secondary));
        color: #fff;
        padding: 80px 0;
        border-radius: 18px;
        overflow: hidden;
    }
    .hero-section h1 { letter-spacing: .5px; }
    .hero-cta .btn-light{ color: var(--fashion-primary); background: #fff; border: none; }
    .hero-cta .btn-outline-light{ border-color: #fff; color:#fff; }
    .hero-cta .btn-outline-light:hover{ background:#fff; color: var(--fashion-primary); }

    /* Category chips */
    .category-chips { margin: 28px 0 8px; }
    .chips-row { display:flex; gap:10px; overflow-x:auto; padding-bottom:6px; scroll-snap-type:x mandatory; }
    .chips-row::-webkit-scrollbar{ height:6px; }
    .chips-row::-webkit-scrollbar-thumb{ background:#e5e7eb; border-radius:8px; }
    .chip { scroll-snap-align:start; display:inline-flex; align-items:center; gap:10px; padding:10px 14px; border-radius:999px; border:1px solid #e5e7eb; background:#fff; white-space:nowrap; transition: all .2s ease; }
    .chip:hover { border-color: var(--fashion-primary); box-shadow: 0 4px 14px rgba(255,63,108,.15); transform: translateY(-1px); }
    .chip .avatar { width:28px; height:28px; border-radius:50%; overflow:hidden; background:#f3f4f6; display:flex; align-items:center; justify-content:center; font-weight:700; color:#9ca3af; }
    .chip img{ width:100%; height:100%; object-fit:cover; }

    /* Trending scroller */
    .trending-wrap{ position:relative; }
    .trending-scroller{ display:flex; gap:16px; overflow-x:auto; padding-bottom:10px; scroll-snap-type:x mandatory; }
    .trending-scroller .trend-card{ min-width:220px; max-width:220px; scroll-snap-align:start; }
    .trend-nav{ position:absolute; top:-56px; right:0; display:flex; gap:8px; }
    .trend-btn{ width:36px; height:36px; border-radius:50%; border:1px solid #e5e7eb; background:#fff; display:flex; align-items:center; justify-content:center; }

    /* Product card tweaks */
    .product-card .card-img-top{ height:260px; object-fit:cover; }
    .product-card .wishlist-btn{ background:#fff; border:1px solid #e5e7eb; }
    .badge-deal { background: var(--fashion-accent); color:#111827; font-weight:700; }

    /* Section headers */
    .section-title { font-weight:800; letter-spacing:.3px; }
    .section-sub{ color: var(--fashion-muted); }

    /* Navbar overrides (homepage only) */
    .navbar-brand { color: var(--fashion-primary) !important; }
    .navbar .nav-link:hover { color: var(--fashion-primary); }
    .btn-primary { background-color: var(--fashion-primary); border-color: var(--fashion-primary); }
    .btn-primary:hover { background-color: #e7335c; border-color: #e7335c; }

    /* Collections grid */
    .collections .collection-card{
        position:relative; border-radius:14px; overflow:hidden; height: 220px; background:#f3f4f6;
    }
    .collections .collection-card img{ width:100%; height:100%; object-fit:cover; transition: transform .35s ease; }
    .collections .collection-card:hover img{ transform: scale(1.06); }
    .collections .collection-card .label{ position:absolute; left:14px; bottom:12px; background: rgba(17,24,39,.7); color:#fff; padding:8px 12px; border-radius:999px; font-weight:700; }

    /* Myntra-like full-bleed hero slider */
    .hero-slider .container-fluid { padding-left: 0; padding-right: 0; }
    .hero-slider .carousel-item img { width: 100%; height: clamp(240px, 40vw, 520px); object-fit: cover; display: block; }
    .hero-slider .carousel-indicators [data-bs-target] { width:8px; height:8px; border-radius:50%; background:#fff; opacity:.6; }
    .hero-slider .carousel-indicators .active { opacity: 1; }

    /* Brand strip */
    .brands-strip { background:#fff; padding: 18px 0; border-top:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9; }
    .brands-strip .logos { display:flex; gap:22px; overflow-x:auto; }
    .brands-strip .logo { flex:0 0 auto; width:110px; height:56px; border:1px solid #eee; border-radius:10px; display:flex; align-items:center; justify-content:center; background:#fff; font-weight:800; color:#374151; }

    /* Category mosaic */
    .category-tiles .tile { position: relative; border-radius:12px; overflow: hidden; }
    .category-tiles .tile .ratio { width:100%; aspect-ratio: 1/1; background:#f3f4f6; }
    .category-tiles .tile img { width:100%; height:100%; object-fit: cover; transition: transform .35s ease; display:block; }
    .category-tiles .tile:hover img { transform: scale(1.06); }
    .category-tiles .tile .label { position:absolute; left:10px; bottom:10px; background: rgba(17,24,39,.7); color:#fff; padding:6px 10px; border-radius:999px; font-weight:700; font-size:.9rem; }

    /* Product rails */
    .rail { position:relative; }
    .rail .rail-scroller { display:flex; gap:16px; overflow-x:auto; scroll-snap-type:x mandatory; padding-bottom:10px; }
    .rail .rail-item { min-width:220px; max-width:220px; scroll-snap-align:start; }
    .rail .nav { position:absolute; top:-56px; right:0; display:flex; gap:8px; }
    .rail .nav-btn { width:36px; height:36px; border-radius:50%; border:1px solid #e5e7eb; background:#fff; display:flex; align-items:center; justify-content:center; }
</style>
@endpush

@section('content')
<!-- Hero Slider (full-bleed) -->
<section class="hero-slider">
    <div class="container-fluid px-0">
        @if(isset($heroBanners) && $heroBanners->count() > 0)
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($heroBanners as $index => $banner)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}">
                    @if(!empty($banner->title) || !empty($banner->description) || !empty($banner->link_url))
                    <div class="carousel-caption text-start d-none d-md-block">
                        @if(!empty($banner->title))
                        <h2 class="fw-bold">{{ $banner->title }}</h2>
                        @endif
                        @if(!empty($banner->description))
                        <p class="mb-2">{{ $banner->description }}</p>
                        @endif
                        @if(!empty($banner->link_url))
                        <a href="{{ $banner->link_url }}" class="btn btn-primary btn-sm">Shop Now</a>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @if($heroBanners->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
            <div class="carousel-indicators">
                @foreach($heroBanners as $i => $b)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}" aria-label="Slide {{ $i + 1 }}"></button>
                @endforeach
            </div>
            @endif
        </div>
        @else
        <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?q=80&w=1600&auto=format&fit=crop" alt="Hero" class="w-100" style="height: clamp(240px, 40vw, 520px); object-fit: cover;">
        @endif
    </div>
</section>

<!-- Brand Strip -->
<section class="brands-strip">
    <div class="container">
        <div class="logos">
            @if(isset($brands) && $brands->count() > 0)
                @foreach($brands as $brand)
                <div class="logo">
                    @if($brand->logo)
                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" style="max-height: 40px; max-width: 100px;">
                    @else
                        {{ strtoupper($brand->name) }}
                    @endif
                </div>
                @endforeach
            @else
                <!-- Fallback static brands if no brands in database -->
                <div class="logo">NIKE</div>
                <div class="logo">ADIDAS</div>
                <div class="logo">PUMA</div>
                <div class="logo">LEVIS</div>
                <div class="logo">H&amp;M</div>
                <div class="logo">ZARA</div>
                <div class="logo">ROADSTER</div>
                <div class="logo">U.S. POLO</div>
            @endif
        </div>
    </div>
</section>

<!-- Category Mosaic -->
<section class="py-4 category-tiles">
    <div class="container">
        <div class="d-flex align-items-end justify-content-between mb-3">
            <div>
                <h2 class="section-title mb-1">Shop by Category</h2>
                <p class="section-sub mb-0">Curated picks for you</p>
            </div>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm">View All</a>
        </div>
        <div class="row g-3">
            @foreach($categories as $category)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('categories.show', $category->slug) }}" class="text-decoration-none">
                    <div class="tile">
                        <div class="ratio">
                            @if(!empty($category->image))
                                <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}">
                            @else
                                <img src="https://images.unsplash.com/photo-1520975922284-5f5733bbedc0?q=80&w=800&auto=format&fit=crop" alt="{{ $category->name }}">
                            @endif
                        </div>
                        <span class="label">{{ $category->name }}</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Deals of the Day -->
<section class="py-4 rail" id="deals-rail">
    <div class="container">
        <div class="d-flex align-items-end justify-content-between mb-3">
            <div>
                <h2 class="section-title mb-1">Deals of the Day</h2>
                <p class="section-sub mb-0">Ends in <span id="dealsCountdown">--:--:--</span></p>
            </div>
            <div class="nav">
                <button class="nav-btn deals-btn" type="button" data-scroll="-1" aria-label="Scroll left"><i class="fas fa-chevron-left"></i></button>
                <button class="nav-btn deals-btn" type="button" data-scroll="1" aria-label="Scroll right"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
        <div class="rail-scroller" id="dealsScroller">
            @foreach($featuredProducts as $product)
            <div class="card product-card rail-item h-100">
                <div class="position-relative">
                    <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}">
                    @if($product->discount_percentage > 0)
                    <span class="badge badge-deal position-absolute top-0 start-0 m-2">-{{ $product->discount_percentage }}%</span>
                    @endif
                </div>
                <div class="card-body">
                    <h6 class="card-title mb-1">{{ Str::limit($product->name, 48) }}</h6>
                    <div class="price-section">
                        @if($product->discount_price && $product->discount_price < $product->price)
                        <span class="price">${{ number_format($product->discount_price, 2) }}</span>
                        <span class="original-price ms-2">${{ number_format($product->price, 2) }}</span>
                        @else
                        <span class="price">${{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-secondary btn-sm w-100">View</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Trending Now (horizontal) -->
<section class="py-5">
    <div class="container trending-wrap">
        <div class="d-flex align-items-end justify-content-between mb-3">
            <div>
                <h2 class="section-title mb-1">Trending Now</h2>
                <p class="section-sub mb-0">Most-loved styles this week</p>
            </div>
            <div class="trend-nav">
                <button class="trend-btn" type="button" data-scroll="-1" aria-label="Scroll left"><i class="fas fa-chevron-left"></i></button>
                <button class="trend-btn" type="button" data-scroll="1" aria-label="Scroll right"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
        <div class="trending-scroller" id="trendingScroller">
            @foreach($featuredProducts as $product)
                <div class="card product-card trend-card h-100">
                    <div class="position-relative">
                        <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}">
                        @if($product->discount_percentage > 0)
                        <span class="badge badge-deal position-absolute top-0 start-0 m-2">-{{ $product->discount_percentage }}%</span>
                        @endif
                        <div class="position-absolute top-0 end-0 m-2">
                            <button class="btn btn-sm rounded-circle wishlist-btn" data-product-id="{{ $product->id }}">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title mb-1">{{ Str::limit($product->name, 48) }}</h6>
                        <div class="price-section">
                            @if($product->discount_price && $product->discount_price < $product->price)
                            <span class="price">${{ number_format($product->discount_price, 2) }}</span>
                            <span class="original-price ms-2">${{ number_format($product->price, 2) }}</span>
                            @else
                            <span class="price">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
 </section>

<!-- Mid-page Banners (dynamic) -->
@if(isset($midBanners) && $midBanners->count() > 0)
<section class="py-4">
    <div class="container">
        <div class="row g-3">
            @foreach($midBanners as $banner)
            <div class="col-12 col-md-6 col-lg-4">
                <a href="{{ $banner->link_url ?? '#' }}" class="d-block text-decoration-none">
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="img-fluid rounded shadow-sm w-100" />
                </a>
            </div>
            @endforeach
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
                        <img src="{{ $product->image_url }}" 
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
                            @if($product->discount_price && $product->discount_price < $product->price)
                            <span class="price">${{ number_format($product->discount_price, 2) }}</span>
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
                        <img src="{{ $product->image_url }}" 
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
    // Trending scroller nav
    const scroller = document.getElementById('trendingScroller');
    document.querySelectorAll('.trend-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!scroller) return;
            const dir = parseInt(btn.getAttribute('data-scroll') || '1', 10);
            const amount = Math.max(220, Math.round(scroller.clientWidth * 0.8));
            scroller.scrollBy({ left: amount * dir, behavior: 'smooth' });
        });
    });

    // Deals scroller nav
    const dealsScroller = document.getElementById('dealsScroller');
    document.querySelectorAll('.deals-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!dealsScroller) return;
            const dir = parseInt(btn.getAttribute('data-scroll') || '1', 10);
            const amount = Math.max(220, Math.round(dealsScroller.clientWidth * 0.8));
            dealsScroller.scrollBy({ left: amount * dir, behavior: 'smooth' });
        });
    });

    // Deals countdown to end of day (local time)
    const countdownEl = document.getElementById('dealsCountdown');
    function pad(n){ return n.toString().padStart(2,'0'); }
    function updateCountdown(){
        if (!countdownEl) return;
        const now = new Date();
        const end = new Date();
        end.setHours(23,59,59,999);
        let diff = Math.max(0, end - now);
        const hrs = Math.floor(diff / 3600000);
        diff -= hrs * 3600000;
        const mins = Math.floor(diff / 60000);
        diff -= mins * 60000;
        const secs = Math.floor(diff / 1000);
        countdownEl.textContent = `${pad(hrs)}:${pad(mins)}:${pad(secs)}`;
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);

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
