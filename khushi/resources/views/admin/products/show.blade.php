@extends('layouts.admin')

@section('title', 'Product Details')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Product Details</h1>
            <p class="page-subtitle">View product information and statistics</p>
        </div>
        <div>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i>Edit Product
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Product Information -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Product Name</h6>
                        <p class="mb-3">{{ $product->name }}</p>
                        
                        <h6 class="text-muted">SKU</h6>
                        <p class="mb-3">{{ $product->sku ?? 'N/A' }}</p>
                        
                        <h6 class="text-muted">Category</h6>
                        <p class="mb-3">
                            @if($product->category)
                                <span class="badge bg-secondary">{{ $product->category->name }}</span>
                            @else
                                <span class="text-muted">No category assigned</span>
                            @endif
                        </p>
                        
                        <h6 class="text-muted">Status</h6>
                        <p class="mb-3">
                            @if($product->status)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Regular Price</h6>
                        <p class="mb-3">${{ number_format($product->price, 2) }}</p>
                        
                        <h6 class="text-muted">Sale Price</h6>
                        <p class="mb-3">
                            @if($product->discount_price)
                                ${{ number_format($product->discount_price, 2) }}
                                <small class="text-success">({{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% off)</small>
                            @else
                                <span class="text-muted">No sale price</span>
                            @endif
                        </p>
                        
                        <h6 class="text-muted">Stock Quantity</h6>
                        <p class="mb-3">
                            @if($product->stock > 0)
                                <span class="text-success">{{ $product->stock }} units</span>
                            @else
                                <span class="text-danger">Out of stock</span>
                            @endif
                        </p>
                        
                        <h6 class="text-muted">Featured Product</h6>
                        <p class="mb-3">
                            @if($product->is_featured)
                                <span class="badge bg-warning">Yes</span>
                            @else
                                <span class="text-muted">No</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6 class="text-muted">Description</h6>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Statistics -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-primary">{{ $product->orderItems->count() ?? 0 }}</div>
                            <div class="text-muted">Total Orders</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-success">${{ number_format($product->orderItems->sum('total_price') ?? 0, 2) }}</div>
                            <div class="text-muted">Total Revenue</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-info">{{ $product->reviews->count() ?? 0 }}</div>
                            <div class="text-muted">Reviews</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-warning">{{ number_format($product->reviews->avg('rating') ?? 0, 1) }}</div>
                            <div class="text-muted">Avg Rating</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Reviews -->
        @if($product->reviews && $product->reviews->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Reviews</h5>
            </div>
            <div class="card-body">
                @foreach($product->reviews->take(5) as $review)
                <div class="d-flex mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                    <div class="avatar-sm bg-light rounded-circle me-3 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user text-muted"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $review->user->name ?? 'Anonymous' }}</h6>
                                <div class="text-warning mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-0">{{ $review->comment }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Product Image and Actions -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Image</h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x300/e5e7eb/9ca3af?text=No+Image' }}" 
                     class="img-fluid rounded" style="max-height: 300px;" alt="{{ $product->name }}">
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Product
                    </a>
                    
                    @if($product->status)
                        <button class="btn btn-warning" onclick="toggleStatus({{ $product->id }}, 0)">
                            <i class="fas fa-pause me-2"></i>Deactivate Product
                        </button>
                    @else
                        <button class="btn btn-success" onclick="toggleStatus({{ $product->id }}, 1)">
                            <i class="fas fa-play me-2"></i>Activate Product
                        </button>
                    @endif
                    
                    <button class="btn btn-danger" onclick="deleteProduct({{ $product->id }})">
                        <i class="fas fa-trash me-2"></i>Delete Product
                    </button>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Dates</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted">Created</h6>
                    <p class="mb-0">{{ $product->created_at->format('M d, Y \a\t g:i A') }}</p>
                </div>
                <div>
                    <h6 class="text-muted">Last Updated</h6>
                    <p class="mb-0">{{ $product->updated_at->format('M d, Y \a\t g:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleStatus(id, status) {
    const action = status ? 'activate' : 'deactivate';
    if (confirm(`Are you sure you want to ${action} this product?`)) {
        $.ajax({
            url: `/admin/products/${id}/toggle-status`,
            method: 'POST',
            data: {
                status: status,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating status: ' + response.message);
                }
            },
            error: function() {
                alert('Error updating status');
            }
        });
    }
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        $.ajax({
            url: `/admin/products/${id}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = '{{ route("admin.products.index") }}';
                } else {
                    alert('Error deleting product: ' + response.message);
                }
            },
            error: function() {
                alert('Error deleting product');
            }
        });
    }
}
</script>
@endpush
