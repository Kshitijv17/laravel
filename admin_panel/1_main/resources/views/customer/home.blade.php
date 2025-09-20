@extends('customer.layout')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container hero-content">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-6">
                <div class="mb-4">
                    <span class="badge bg-white text-primary px-3 py-2 rounded-pill mb-3" style="font-weight: 600; color: #65a30d !important;">Best products for hair growth</span>
                </div>
                <h1 class="display-4 fw-bold mb-4" style="line-height: 1.1;">Best products for hair growth</h1>
                <p class="lead mb-5" style="font-size: 1.1rem; opacity: 0.9;">Shop from the best hair care products</p>
                <a href="#products" class="btn btn-light btn-lg px-4 py-3" style="font-weight: 600; border-radius: 12px;">
                    Shop now
                </a>
            </div>
            <div class="col-lg-6 text-center">
                <div class="position-relative">
                    <!-- Decorative elements similar to the uploaded image -->
                    <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
                        <div class="position-relative">
                            <!-- Main product showcase area -->
                            <div class="bg-white rounded-4 p-4 shadow-lg" style="width: 300px; height: 200px; display: flex; align-items: center; justify-content: center;">
                                <div class="text-center">
                                    <i class="fas fa-leaf" style="font-size: 3rem; margin-bottom: 1rem; color: #16a34a;"></i>
                                    <h5 class="text-dark mb-0">Natural Products</h5>
                                    <small class="text-muted">Organic & Safe</small>
                                </div>
                            </div>
                            <!-- Floating elements -->
                            <div class="position-absolute" style="top: -20px; right: -30px; width: 60px; height: 60px; background: #84cc16; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div class="position-absolute" style="bottom: -20px; left: -30px; width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; backdrop-filter: blur(10px);"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Row -->
        <div class="row mt-5 pt-4">
            <div class="col-md-3 col-6 text-center mb-3">
                <div class="bg-white bg-opacity-20 rounded-3 p-3 backdrop-blur">
                    <h3 class="fw-bold mb-1">{{ $stats['total_products'] }}+</h3>
                    <small class="opacity-75">Products</small>
                </div>
            </div>
            <div class="col-md-3 col-6 text-center mb-3">
                <div class="bg-white bg-opacity-20 rounded-3 p-3 backdrop-blur">
                    <h3 class="fw-bold mb-1">{{ $stats['total_shops'] }}+</h3>
                    <small class="opacity-75">Shops</small>
                </div>
            </div>
            <div class="col-md-3 col-6 text-center mb-3">
                <div class="bg-white bg-opacity-20 rounded-3 p-3 backdrop-blur">
                    <h3 class="fw-bold mb-1">{{ $stats['total_categories'] }}+</h3>
                    <small class="opacity-75">Categories</small>
                </div>
            </div>
            <div class="col-md-3 col-6 text-center mb-3">
                <div class="bg-white bg-opacity-20 rounded-3 p-3 backdrop-blur">
                    <h3 class="fw-bold mb-1">1000+</h3>
                    <small class="opacity-75">Happy Customers</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<section class="py-5" style="background: #f7fee7;">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <span class="badge px-3 py-2 rounded-pill mb-3" style="font-weight: 600; background: rgba(101, 163, 13, 0.1); color: #65a30d;">Trending Product</span>
                <h2 class="fw-bold mb-3" style="font-size: 2.5rem;">Trending Product</h2>
                <p class="text-muted fs-5">These are very popular products for the last few months</p>
            </div>
        </div>
        <div class="row g-4">
            @foreach($featuredProducts as $product)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="product-image" alt="{{ $product->title }}">
                            @else
                                <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            
                            @if($product->selling_price && $product->selling_price < $product->price)
                                @php
                                    $discount = round((($product->price - $product->selling_price) / $product->price) * 100);
                                @endphp
                                <span class="discount-badge">-{{ $discount }}%</span>
                            @endif
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">{{ Str::limit($product->title, 50) }}</h6>
                            <div class="mb-2">
                                <span class="shop-badge">{{ $product->shop->name }}</span>
                                @if($product->category)
                                    <span class="category-badge">{{ $product->category->title }}</span>
                                @endif
                            </div>
                            
                            <div class="price-section mb-2">
                                @if($product->selling_price && $product->selling_price < $product->price)
                                    <span class="price-tag">${{ number_format($product->selling_price, 2) }}</span>
                                    <span class="original-price ms-2">${{ number_format($product->price, 2) }}</span>
                                @else
                                    <span class="price-tag">${{ number_format($product->selling_price ?? $product->price, 2) }}</span>
                                @endif
                            </div>
                            
                            <div class="mb-2">
                                @if($product->quantity <= 0)
                                    <span class="stock-badge out-of-stock">Out of Stock</span>
                                @elseif($product->quantity <= 10)
                                    <span class="stock-badge low-stock">{{ $product->quantity }} left</span>
                                @else
                                    <span class="stock-badge in-stock">In Stock</span>
                                @endif
                            </div>
                            
                            <div class="mt-auto">
                                <a href="{{ route('customer.product.show', $product) }}" class="btn btn-primary btn-sm w-100 mb-2">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                @if($product->quantity > 0)
                                    <a href="{{ route('customer.buy-now', $product) }}" class="btn btn-buy-now btn-sm w-100">
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
</section>
@endif

<!-- Filters and Products Section -->
<section class="py-5" id="products">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <span class="badge px-3 py-2 rounded-pill mb-3" style="font-weight: 600; background: rgba(132, 204, 22, 0.1); color: #84cc16;">Best Selling Product</span>
                <h2 class="fw-bold mb-3" style="font-size: 2.5rem;">Best Selling Product</h2>
                <p class="text-muted fs-5">These are very popular products for the last few months</p>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card filter-card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="shop" class="form-select">
                                    <option value="">All Shops</option>
                                    @foreach($shops as $shop)
                                        <option value="{{ $shop->id }}" {{ request('shop') == $shop->id ? 'selected' : '' }}>
                                            {{ $shop->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="min_price" class="form-control" placeholder="Min Price" value="{{ request('min_price') }}" min="0" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="max_price" class="form-control" placeholder="Max Price" value="{{ request('max_price') }}" min="0" step="0.01">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                        
                        <!-- Sort Options -->
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <select name="sort" class="form-select" onchange="this.form.submit()">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                    <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                                </select>
                            </div>
                            <div class="col-md-9 text-end">
                                @if(request()->hasAny(['search', 'category', 'shop', 'min_price', 'max_price']))
                                    <a href="{{ route('customer.home') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Clear Filters
                                    </a>
                                @endif
                                <span class="text-muted ms-3">{{ $products->total() }} products found</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="row g-4">
            @if($products->count() > 0)
                @foreach($products as $product)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card product-card h-100">
                            <div class="position-relative">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="product-image" alt="{{ $product->title }}">
                                @else
                                    <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                                
                                @if($product->selling_price && $product->selling_price < $product->price)
                                    @php
                                        $discount = round((($product->price - $product->selling_price) / $product->price) * 100);
                                    @endphp
                                    <span class="discount-badge">-{{ $discount }}%</span>
                                @endif
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title">{{ Str::limit($product->title, 50) }}</h6>
                                <div class="mb-2">
                                    <span class="shop-badge">{{ $product->shop->name }}</span>
                                    @if($product->category)
                                        <span class="category-badge">{{ $product->category->title }}</span>
                                    @endif
                                </div>
                                
                                <div class="price-section mb-2">
                                    @if($product->discount_price && $product->discount_price < $product->price)
                                        <span class="price-tag">${{ number_format($product->discount_price, 2) }}</span>
                                        <span class="original-price ms-2">${{ number_format($product->price, 2) }}</span>
                                    @else
                                        <span class="price-tag">${{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                
                                <div class="mb-2">
                                    @if($product->quantity <= 0)
                                        <span class="stock-badge out-of-stock">Out of Stock</span>
                                    @elseif($product->quantity <= 10)
                                        <span class="stock-badge low-stock">{{ $product->quantity }} left</span>
                                    @else
                                        <span class="stock-badge in-stock">In Stock</span>
                                    @endif
                                </div>
                                
                                <div class="mt-auto">
                                    <a href="{{ route('customer.product.show', $product) }}" class="btn btn-primary btn-sm w-100 mb-2">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                    @if($product->quantity > 0)
                                        <a href="{{ route('customer.buy-now', $product) }}" class="btn btn-buy-now btn-sm w-100">
                                            <i class="fas fa-shopping-cart me-1"></i>Buy Now
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Products Found</h4>
                    <p class="text-muted">Try adjusting your search criteria or browse our categories.</p>
                    <a href="{{ route('customer.home') }}" class="btn btn-primary">
                        <i class="fas fa-home me-1"></i>Back to Home
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Pagination -->
        @if($products->hasPages())
            <div class="row mt-4">
                <div class="col-12 d-flex justify-content-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Kind Words Section -->
<section class="py-5" style="background: #f0fdf4;">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <span class="badge px-3 py-2 rounded-pill mb-3" style="font-weight: 600; background: rgba(22, 163, 74, 0.1); color: #16a34a;">Kind Words</span>
                <h2 class="fw-bold mb-3" style="font-size: 2.5rem;">Kind Words</h2>
                <p class="text-muted fs-5">What our customers say about our products and services</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-6 mb-4">
                <div class="testimonial-card">
                    <div class="d-flex align-items-start mb-3">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face" 
                             alt="Customer" class="testimonial-avatar me-3">
                        <div>
                            <h6 class="mb-1 fw-bold">Sarah Johnson</h6>
                            <small class="text-muted">Verified Customer</small>
                        </div>
                    </div>
                    <p class="text-muted mb-0">"I can't say enough good things about this organic shampoo. My hair has never looked or felt better. The natural ingredients really make a difference, and I love that it's environmentally friendly too."</p>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="testimonial-card">
                    <div class="d-flex align-items-start mb-3">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" 
                             alt="Customer" class="testimonial-avatar me-3">
                        <div>
                            <h6 class="mb-1 fw-bold">Michael Chen</h6>
                            <small class="text-muted">Regular Customer</small>
                        </div>
                    </div>
                    <p class="text-muted mb-0">"Fast shipping and great customer service! I've been using their hair growth serum for 3 months now and I'm seeing real results. The quality is outstanding and the price is very reasonable."</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="#" class="btn btn-outline-primary">View all reviews</a>
        </div>
    </div>
</section>
@endsection
