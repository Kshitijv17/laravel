@extends('customer.layout')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Discover Amazing Products</h1>
                <p class="lead mb-4">Shop from thousands of verified sellers and find exactly what you're looking for at the best prices.</p>
                <div class="d-flex gap-3 mb-4">
                    <div class="text-center">
                        <h3 class="fw-bold">{{ $stats['total_products'] }}+</h3>
                        <small>Products</small>
                    </div>
                    <div class="text-center">
                        <h3 class="fw-bold">{{ $stats['total_shops'] }}+</h3>
                        <small>Shops</small>
                    </div>
                    <div class="text-center">
                        <h3 class="fw-bold">{{ $stats['total_categories'] }}+</h3>
                        <small>Categories</small>
                    </div>
                </div>
                <a href="#products" class="btn btn-light btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                </a>
            </div>
            <div class="col-lg-6 text-center">
                <i class="fas fa-shopping-cart" style="font-size: 200px; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<section class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold">Featured Products</h2>
                <p class="text-muted">Hand-picked products from our best sellers</p>
            </div>
        </div>
        <div class="row">
            @foreach($featuredProducts as $product)
                <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
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
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold">All Products</h2>
                <p class="text-muted">Browse our complete collection</p>
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
        <div class="row">
            @if($products->count() > 0)
                @foreach($products as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
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
@endsection
