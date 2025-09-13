@extends('layouts.admin')

@section('title', 'Category Details')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Category Details</h1>
            <p class="page-subtitle">View category information and products</p>
        </div>
        <div>
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i>Edit Category
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Categories
            </a>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Category Information -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Category Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Category Name</h6>
                        <p class="mb-3">{{ $category->name }}</p>
                        
                        <h6 class="text-muted">Slug</h6>
                        <p class="mb-3">{{ $category->slug ?? 'N/A' }}</p>
                        
                        <h6 class="text-muted">Status</h6>
                        <p class="mb-3">
                            @if($category->status)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Total Products</h6>
                        <p class="mb-3">{{ $category->products->count() ?? 0 }} products</p>
                        
                        <h6 class="text-muted">Active Products</h6>
                        <p class="mb-3">{{ $category->products->where('status', true)->count() ?? 0 }} products</p>
                        
                        <h6 class="text-muted">Created Date</h6>
                        <p class="mb-3">{{ $category->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                
                @if($category->description)
                <div class="mt-4">
                    <h6 class="text-muted">Description</h6>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($category->description)) !!}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Category Statistics -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Category Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-primary">{{ $category->products->count() ?? 0 }}</div>
                            <div class="text-muted">Total Products</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-success">{{ $category->products->where('status', true)->count() ?? 0 }}</div>
                            <div class="text-muted">Active Products</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-warning">{{ $category->products->where('stock', '>', 0)->count() ?? 0 }}</div>
                            <div class="text-muted">In Stock</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="h4 text-info">${{ number_format($category->products->avg('price') ?? 0, 2) }}</div>
                            <div class="text-muted">Avg Price</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products in Category -->
        <div class="card mt-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Products in Category</h5>
                    <a href="{{ route('admin.products.create') }}?category={{ $category->id }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>Add Product
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($category->products && $category->products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->products->take(10) as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/40x40/e5e7eb/9ca3af?text=No+Image' }}" 
                                             class="rounded me-2" width="40" height="40" alt="{{ $product->name }}">
                                        <div>
                                            <div class="fw-bold">{{ $product->name }}</div>
                                            <small class="text-muted">{{ $product->sku ?? 'No SKU' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>${{ number_format($product->price, 2) }}</div>
                                    @if($product->discount_price)
                                        <small class="text-success">${{ number_format($product->discount_price, 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($product->stock > 0)
                                        <span class="text-success">{{ $product->stock }}</span>
                                    @else
                                        <span class="text-danger">Out of stock</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($category->products->count() > 10)
                <div class="text-center mt-3">
                    <a href="{{ route('admin.products.index') }}?category={{ $category->id }}" class="btn btn-outline-primary">
                        View All Products ({{ $category->products->count() }})
                    </a>
                </div>
                @endif
                @else
                <div class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-box-open fa-3x mb-3"></i>
                        <p>No products in this category yet</p>
                        <a href="{{ route('admin.products.create') }}?category={{ $category->id }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add First Product
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Category Image and Actions -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Category Image</h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ $category->image ? asset('storage/' . $category->image) : 'https://via.placeholder.com/300x200/e5e7eb/9ca3af?text=No+Image' }}" 
                     class="img-fluid rounded" style="max-height: 200px;" alt="{{ $category->name }}">
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Category
                    </a>
                    
                    @if($category->status)
                        <button class="btn btn-warning" onclick="toggleStatus({{ $category->id }}, 0)">
                            <i class="fas fa-pause me-2"></i>Deactivate Category
                        </button>
                    @else
                        <button class="btn btn-success" onclick="toggleStatus({{ $category->id }}, 1)">
                            <i class="fas fa-play me-2"></i>Activate Category
                        </button>
                    @endif
                    
                    <button class="btn btn-danger" onclick="deleteCategory({{ $category->id }})">
                        <i class="fas fa-trash me-2"></i>Delete Category
                    </button>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Category Dates</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted">Created</h6>
                    <p class="mb-0">{{ $category->created_at->format('M d, Y \a\t g:i A') }}</p>
                </div>
                <div>
                    <h6 class="text-muted">Last Updated</h6>
                    <p class="mb-0">{{ $category->updated_at->format('M d, Y \a\t g:i A') }}</p>
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
    if (confirm(`Are you sure you want to ${action} this category?`)) {
        $.ajax({
            url: `/admin/categories/${id}/toggle-status`,
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

function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone and will affect all products in this category.')) {
        $.ajax({
            url: `/admin/categories/${id}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = '{{ route("admin.categories.index") }}';
                } else {
                    alert('Error deleting category: ' + response.message);
                }
            },
            error: function() {
                alert('Error deleting category');
            }
        });
    }
}
</script>
@endpush
