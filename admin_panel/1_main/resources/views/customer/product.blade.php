@extends('customer.layout')

@section('title', $product->name)

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
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body p-0">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid w-100" alt="{{ $product->name }}" style="height: 500px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 500px;">
                            <i class="fas fa-image fa-5x text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-3">{{ $product->name }}</h1>
                    
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
@endsection
