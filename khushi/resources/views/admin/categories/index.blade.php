@extends('layouts.admin')

@section('title', 'Categories Management')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Categories Management</h1>
            <p class="page-subtitle">Organize your product categories</p>
        </div>
        <div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Category
            </a>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Categories</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_categories'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Categories</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_categories'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Products Assigned</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['products_with_categories'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">All Categories</h5>
            <div class="d-flex gap-2">
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
            <table class="table table-hover data-table" id="categoriesTable">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Slug</th>
                        <th>Products Count</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if($category->image)
                                    <img src="{{ $category->image_url }}" 
                                         class="rounded" width="40" height="40" alt="{{ $category->name }}">
                                    @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-tag text-muted"></i>
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $category->name }}</div>
                                    @if($category->description)
                                    <small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <code>{{ $category->slug }}</code>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $category->products_count ?? 0 }}</span>
                        </td>
                        <td>
                            @if($category->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $category->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $category->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.categories.show', $category->id) }}" 
                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                   class="btn btn-sm btn-outline-secondary" title="Edit Category">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteCategory({{ $category->id }})" title="Delete Category">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-tags fa-3x mb-3"></i>
                                <p>No categories found</p>
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Create Your First Category
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
    if ($.fn.DataTable.isDataTable('#categoriesTable')) {
        $('#categoriesTable').DataTable().destroy();
    }
    
    // Initialize DataTable
    $('#categoriesTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 25,
        order: [[4, 'desc']],
        columnDefs: [
            { orderable: false, targets: [5] }
        ]
    });
    
    // Status filter
    $('#statusFilter').on('change', function() {
        var status = $(this).val();
        if (status === '1') {
            $('#categoriesTable').DataTable().column(3).search('Active').draw();
        } else if (status === '0') {
            $('#categoriesTable').DataTable().column(3).search('Inactive').draw();
        } else {
            $('#categoriesTable').DataTable().column(3).search('').draw();
        }
    });
});

function deleteCategory(categoryId) {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone and will affect all products in this category.')) {
        $.ajax({
            url: `/admin/categories/${categoryId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting category: ' + (response.message || 'Unknown error'));
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
