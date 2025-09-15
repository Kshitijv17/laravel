@extends('layouts.app')

@section('title', 'Search: ' . e($query))

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Search</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3">
        <div>
            <h2 class="mb-1">Search results for: “{{ $query }}”</h2>
            <p class="text-muted mb-0">{{ number_format($products->total()) }} result{{ $products->total() == 1 ? '' : 's' }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <label class="form-label mb-0">Sort by:</label>
            <select class="form-select" style="width:auto" onchange="updateSort(this.value)">
                <option value="relevance" {{ request('sort')=='relevance' ? 'selected' : '' }}>Relevance</option>
                <option value="newest" {{ request('sort')=='newest' ? 'selected' : '' }}>Newest</option>
                <option value="price_low" {{ request('sort')=='price_low' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_high" {{ request('sort')=='price_high' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="rating" {{ request('sort')=='rating' ? 'selected' : '' }}>Rating</option>
            </select>
        </div>
    </div>

    @if($products->count())
    <div class="row g-4">
        @foreach($products as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100">
                <div class="position-relative">
                    <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}">
                    @if(!empty($product->discount_percentage))
                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">-{{ $product->discount_percentage }}%</span>
                    @endif
                    <div class="position-absolute top-0 end-0 m-2">
                        <button class="btn btn-sm btn-light rounded-circle wishlist-btn" data-product-id="{{ $product->id }}" title="Add to Wishlist">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="card-title">
                        <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">{{ Str::limit($product->name, 60) }}</a>
                    </h6>

                    @if($product->reviews_avg_rating)
                    <div class="rating mb-1">
                        @for($i=1;$i<=5;$i++)
                            <i class="fas fa-star{{ $i <= $product->reviews_avg_rating ? '' : '-o' }}"></i>
                        @endfor
                        <small class="text-muted ms-1">({{ $product->reviews_count }})</small>
                    </div>
                    @endif

                    <div class="price-section">
                        @if($product->sale_price && $product->sale_price < $product->price)
                        <span class="price">${{ number_format($product->sale_price,2) }}</span>
                        <span class="original-price ms-2">${{ number_format($product->price,2) }}</span>
                        @else
                        <span class="price">${{ number_format($product->price,2) }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary add-to-cart-btn" data-product-id="{{ $product->id }}">
                            <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                        </button>
                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-secondary btn-sm">View Details</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $products->appends(request()->query())->links() }}
    </div>
    @else
    <div class="text-center py-5">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h4>No results found</h4>
        <p class="text-muted">Try a different term or explore categories</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary">Browse All Products</a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function updateSort(sortValue){
    const url = new URL(window.location);
    if(sortValue==='relevance'){ url.searchParams.delete('sort'); }
    else { url.searchParams.set('sort', sortValue); }
    window.location = url;
}
</script>
@endpush
