@extends('layouts.app')

@section('title', 'Products - E-Commerce Store')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Products</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body collapse d-lg-block" id="filtersCollapse">
                    <form id="filter-form" method="GET">
                        <!-- Search -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                        </div>

                        <!-- Categories -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Categories</label>
                            <div class="form-check-container" style="max-height: 200px; overflow-y: auto;">
                                @foreach($categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]" 
                                           value="{{ $category->id }}" id="cat{{ $category->id }}"
                                           {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cat{{ $category->id }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <label class="form-label fw-bold">Price Range</label>
                            <div class="ms-auto small text-muted">INR</div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="min_price" class="form-control" placeholder="Min" 
                                       value="{{ request('min_price') }}" min="0" step="0.01">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_price" class="form-control" placeholder="Max" 
                                       value="{{ request('max_price') }}" min="0" step="0.01">
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Minimum Rating</label>
                            <select name="rating" class="form-select">
                                <option value="">Any Rating</option>
                                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2+ Stars</option>
                                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1+ Stars</option>
                            </select>
                        </div>

                        <!-- Availability -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="in_stock" value="1" 
                                       id="inStock" {{ request('in_stock') ? 'checked' : '' }}>
                                <label class="form-check-label" for="inStock">
                                    In Stock Only
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Clear All</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9 col-md-8">
            <!-- Header with Sort -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h2 class="mb-1">Products</h2>
                    <p class="text-muted mb-0">{{ $products->total() }} products found</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                        <i class="fas fa-filter me-1"></i> Filters
                    </button>
                    <label class="form-label mb-0">Sort by:</label>
                    <select class="form-select" style="width: auto;" onchange="updateSort(this.value)">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                    </select>
                </div>
            </div>

            <!-- Applied Filters Chips -->
            @php
                $selectedCategories = (array) request('categories', []);
                $categoriesById = $categories->keyBy('id');
                $hasAnyFilter = request()->hasAny(['search','categories','min_price','max_price','rating','in_stock']);
            @endphp
            @if($hasAnyFilter)
            <div class="mb-3 d-flex flex-wrap align-items-center gap-2">
                <span class="text-muted small">Applied filters:</span>
                @if(request('search'))
                    @php $query = request()->except('search'); @endphp
                    <a href="{{ route('products.index', $query) }}" class="badge rounded-pill bg-light text-dark px-3 py-2">
                        <i class="fas fa-search me-1"></i>{{ request('search') }} <i class="fas fa-times ms-2"></i>
                    </a>
                @endif
                @foreach($selectedCategories as $catId)
                    @php
                        $newCats = array_values(array_diff($selectedCategories, [$catId]));
                        $query = request()->except('categories');
                        if(count($newCats)) { $query['categories'] = $newCats; }
                        $catName = $categoriesById[$catId]->name ?? ('Category #'.$catId);
                    @endphp
                    <a href="{{ route('products.index', $query) }}" class="badge rounded-pill bg-light text-dark px-3 py-2">
                        {{ $catName }} <i class="fas fa-times ms-2"></i>
                    </a>
                @endforeach
                @if(request('min_price') || request('max_price'))
                    @php $query = request()->except(['min_price','max_price']); @endphp
                    <a href="{{ route('products.index', $query) }}" class="badge rounded-pill bg-light text-dark px-3 py-2">
                        Price {{ request('min_price') ? '₹'.request('min_price') : '' }}{{ request('min_price') && request('max_price') ? ' - ' : '' }}{{ request('max_price') ? '₹'.request('max_price') : '' }}
                        <i class="fas fa-times ms-2"></i>
                    </a>
                @endif
                @if(request('rating'))
                    @php $query = request()->except('rating'); @endphp
                    <a href="{{ route('products.index', $query) }}" class="badge rounded-pill bg-light text-dark px-3 py-2">
                        {{ request('rating') }}+ Stars<i class="fas fa-times ms-2"></i>
                    </a>
                @endif
                @if(request('in_stock'))
                    @php $query = request()->except('in_stock'); @endphp
                    <a href="{{ route('products.index', $query) }}" class="badge rounded-pill bg-light text-dark px-3 py-2">
                        In Stock<i class="fas fa-times ms-2"></i>
                    </a>
                @endif
                <a href="{{ route('products.index') }}" class="ms-auto small text-decoration-none">Clear all</a>
            </div>
            @endif

            @if($products->count() > 0)
            <!-- Products Grid -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                @foreach($products as $product)
                <div class="col">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="{{ $product->image_url }}" 
                                 class="card-img-top" alt="{{ $product->name }}"
                                 onerror="this.onerror=null;this.src='https://via.placeholder.com/300x250/f8f9fa/6c757d?text=Product';">
                            
                            @if($product->discount_percentage > 0)
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                -{{ $product->discount_percentage }}%
                            </span>
                            @endif

                            @if($product->stock_quantity <= 0)
                            <div class="oos-overlay d-flex align-items-center justify-content-center">
                                <span class="badge bg-dark bg-opacity-75">Out of Stock</span>
                            </div>
                            @endif

                            <div class="position-absolute top-0 end-0 m-2">
                                <button class="btn btn-sm btn-light rounded-circle wishlist-btn" 
                                        data-product-id="{{ $product->id }}"
                                        title="Add to Wishlist">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>

                            <!-- Quick View Button -->
                            <div class="position-absolute bottom-0 end-0 m-2">
                                <button class="btn btn-sm btn-light rounded-circle quick-view-btn" 
                                        data-product-id="{{ $product->id }}"
                                        title="Quick View">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">
                                    {{ Str::limit($product->name, 50) }}
                                </a>
                            </h6>
                            
                            <p class="card-text text-muted small">
                                {{ Str::limit($product->description, 80) }}
                            </p>

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
                            <div class="price-section mb-2">
                                @if($product->is_on_sale)
                                <span class="price">₹{{ number_format($product->final_price, 2) }}</span>
                                <span class="original-price ms-2">₹{{ number_format($product->price, 2) }}</span>
                                @else
                                <span class="price">₹{{ number_format($product->final_price, 2) }}</span>
                                @endif
                            </div>

                            <!-- Stock Status -->
                            @if($product->stock_quantity > 0)
                            <small class="text-success">
                                <i class="fas fa-check-circle me-1"></i>In Stock ({{ $product->stock_quantity }})
                            </small>
                            @else
                            <small class="text-danger">
                                <i class="fas fa-times-circle me-1"></i>Out of Stock
                            </small>
                            @endif
                        </div>

                        <div class="card-footer bg-transparent">
                            <div class="d-grid gap-2">
                                @if($product->stock_quantity > 0)
                                <button class="btn btn-primary add-to-cart-btn" data-product-id="{{ $product->id }}">
                                    <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                                </button>
                                @else
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-ban me-1"></i> Out of Stock
                                </button>
                                @endif
                                <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-secondary btn-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            @else
            <!-- No Products Found -->
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>No products found</h4>
                <p class="text-muted">Try adjusting your filters or search terms</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">Clear Filters</a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickViewModalLabel">Quick View</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="quickViewContent">
                <div class="text-center py-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

    // Quick view functionality
    $('.quick-view-btn').on('click', function() {
        const productId = $(this).data('product-id');
        
        $('#quickViewModal').modal('show');
        $('#quickViewContent').html(`
            <div class="text-center py-4">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        
        $.get(`/products/${productId}/quick-view`, function(data) {
            $('#quickViewContent').html(data);
        }).fail(function() {
            $('#quickViewContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading product details. Please try again.
                </div>
            `);
        });
    });

    // Auto-submit form on filter change
    $('#filter-form input[type="checkbox"], #filter-form select').on('change', function() {
        $('#filter-form').submit();
    });
});

// Sort functionality
function updateSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    window.location = url;
}
</script>
@endpush
