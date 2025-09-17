@extends('layouts.admin')

@section('title', 'Products Management')
@section('subtitle', 'Manage your product catalog')

@section('content')
<div class="d-flex justify-content-end align-items-center mb-3">
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Product
    </a>
    </div>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Products</li>
    </ol>
</nav>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Products</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_products'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Products</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_products'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['low_stock'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Categories</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['categories'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">All Products</h5>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" id="categoryFilter">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <select class="form-select form-select-sm" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table" id="productsTable">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if($product->image)
                                    <img src="{{ $product->image_url }}" 
                                         class="rounded" width="50" height="50" alt="{{ $product->name }}">
                                    @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $product->name }}</div>
                                    <small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $product->category->name ?? 'Uncategorized' }}</span>
                        </td>
                        <td>
                            @if($product->discount_price)
                                <strong>${{ number_format($product->discount_price, 2) }}</strong>
                                <br><small class="text-muted"><s>${{ number_format($product->price, 2) }}</s></small>
                            @else
                                <strong>${{ number_format($product->price, 2) }}</strong>
                            @endif
                        </td>
                        <td>
                            @php($qty = (int)($product->stock ?? 0))
                            @if($qty <= 5)
                                <span class="badge bg-danger">{{ $qty }}</span>
                            @elseif($qty <= 20)
                                <span class="badge bg-warning">{{ $qty }}</span>
                            @else
                                <span class="badge bg-success">{{ $qty }}</span>
                            @endif
                        </td>
                        <td>
                            @if($product->status)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $product->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $product->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.products.show', $product->id) }}" 
                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product->id) }}" 
                                   class="btn btn-sm btn-outline-secondary" title="Edit Product">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteProduct({{ $product->id }})" title="Delete Product">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-box fa-3x mb-3"></i>
                                <p>No products found</p>
                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add Your First Product
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Check if DataTable already exists and destroy it
    if ($.fn.DataTable.isDataTable('#productsTable')) {
        $('#productsTable').DataTable().destroy();
    }
    
    // Initialize DataTable
    $('#productsTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 25,
        order: [[5, 'desc']],
        columnDefs: [
            { orderable: false, targets: [6] }
        ]
    });
    
    // Category filter
    $('#categoryFilter').on('change', function() {
        var category = $(this).val();
        $('#productsTable').DataTable().column(1).search(category).draw();
    });
    
    // Status filter
    $('#statusFilter').on('change', function() {
        var status = $(this).val();
        if (status === '1') {
            $('#productsTable').DataTable().column(4).search('Active').draw();
        } else if (status === '0') {
            $('#productsTable').DataTable().column(4).search('Inactive').draw();
        } else {
            $('#productsTable').DataTable().column(4).search('').draw();
        }
    });
});

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        $.ajax({
            url: `/admin/products/${productId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting product');
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
