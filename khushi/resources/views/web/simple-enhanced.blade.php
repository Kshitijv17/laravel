@extends('layouts.web')

@section('title', 'Enhanced Home')

@section('content')
<div class="container-fluid p-0">
    <!-- Hero Section -->
    <div class="bg-primary text-white py-5" style="min-height: 70vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;">
        <div class="container">
            <div class="row align-items-center min-vh-50">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Fashion That Defines You</h1>
                    <p class="lead mb-4">Discover premium fashion collections with the latest trends and timeless styles.</p>
                    <a href="#products" class="btn btn-warning btn-lg me-3">Shop Now</a>
                    <a href="#categories" class="btn btn-outline-light btn-lg">Explore</a>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=500&h=400&fit=crop" 
                         alt="Fashion" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="py-5 bg-light" id="categories">
        <div class="container">
            <h2 class="text-center mb-5 fw-bold">Shop by Category</h2>
            <div class="row g-4">
                @if($categories && $categories->count() > 0)
                    @foreach($categories as $category)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    @switch($category->name)
                                        @case('Men')
                                            <i class="fas fa-male fa-3x text-primary"></i>
                                            @break
                                        @case('Women')
                                            <i class="fas fa-female fa-3x text-danger"></i>
                                            @break
                                        @case('Kids')
                                            <i class="fas fa-child fa-3x text-success"></i>
                                            @break
                                        @case('Footwear')
                                            <i class="fas fa-shoe-prints fa-3x text-info"></i>
                                            @break
                                        @case('Accessories')
                                            <i class="fas fa-gem fa-3x text-warning"></i>
                                            @break
                                        @default
                                            <i class="fas fa-tshirt fa-3x text-primary"></i>
                                    @endswitch
                                </div>
                                <h4 class="fw-bold">{{ $category->name }}</h4>
                                <p class="text-muted">{{ $category->products_count ?? 0 }} Products</p>
                                <a href="#" class="btn btn-outline-primary">View Collection</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12 text-center">
                        <p class="text-muted">No categories available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="py-5" id="products">
        <div class="container">
            <h2 class="text-center mb-5 fw-bold">Featured Products</h2>
            <div class="row g-4">
                @if($featuredProducts && $featuredProducts->count() > 0)
                    @foreach($featuredProducts as $product)
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="position-relative">
                                @if($product->image)
                                    <img src="{{ $product->image }}" class="card-img-top" alt="{{ $product->name }}" style="height: 250px; object-fit: cover;">
                                @else
                                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=300&h=250&fit=crop" class="card-img-top" alt="{{ $product->name }}" style="height: 250px; object-fit: cover;">
                                @endif
                                
                                @if($product->discount_price && $product->discount_price < $product->price)
                                <span class="position-absolute top-0 start-0 m-2 badge bg-danger">
                                    {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                                </span>
                                @endif
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-warning"></i>
                                    @endfor
                                    <small class="text-muted">({{ $product->reviews_count ?? rand(10, 50) }})</small>
                                </div>
                                <div class="mb-3">
                                    @if($product->discount_price && $product->discount_price < $product->price)
                                        <span class="h5 text-primary fw-bold">${{ number_format($product->discount_price, 2) }}</span>
                                        <span class="text-muted text-decoration-line-through ms-2">${{ number_format($product->price, 2) }}</span>
                                    @else
                                        <span class="h5 text-primary fw-bold">${{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                <button class="btn btn-primary w-100">
                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12 text-center">
                        <p class="text-muted">No featured products available.</p>
                    </div>
                @endif
            </div>
            
            @if($featuredProducts && $featuredProducts->count() > 0)
            <div class="text-center mt-5">
                <a href="#" class="btn btn-primary btn-lg">View All Products</a>
            </div>
            @endif
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 text-center">
                    <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                    <h5 class="fw-bold">Free Shipping</h5>
                    <p class="text-muted">Free shipping on orders over $99</p>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <i class="fas fa-undo fa-3x text-primary mb-3"></i>
                    <h5 class="fw-bold">Easy Returns</h5>
                    <p class="text-muted">30-day return policy</p>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h5 class="fw-bold">24/7 Support</h5>
                    <p class="text-muted">Round-the-clock assistance</p>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h5 class="fw-bold">Secure Payment</h5>
                    <p class="text-muted">100% secure transactions</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
