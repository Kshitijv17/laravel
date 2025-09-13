@extends('layouts.admin')

@section('title', 'Banners Management')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Banners Management</h1>
            <p class="page-subtitle">Manage promotional banners and advertisements</p>
        </div>
        <div>
            <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Banner
            </a>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Banners</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Banners</h5>
                        <h3 class="mb-0">{{ $banners->count() ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-image fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Active Banners</h5>
                        <h3 class="mb-0">{{ $banners->where('status', true)->count() ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Homepage Banners</h5>
                        <h3 class="mb-0">{{ $banners->where('position', 'homepage')->count() ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-home fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Clicks</h5>
                        <h3 class="mb-0">{{ $banners->sum('click_count') ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-mouse-pointer fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by title or description...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Position</label>
                <select class="form-select" name="position">
                    <option value="">All Positions</option>
                    <option value="homepage" {{ request('position') == 'homepage' ? 'selected' : '' }}>Homepage</option>
                    <option value="category" {{ request('position') == 'category' ? 'selected' : '' }}>Category</option>
                    <option value="product" {{ request('position') == 'product' ? 'selected' : '' }}>Product</option>
                    <option value="sidebar" {{ request('position') == 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select class="form-select" name="type">
                    <option value="">All Types</option>
                    <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Image</option>
                    <option value="carousel" {{ request('type') == 'carousel' ? 'selected' : '' }}>Carousel</option>
                    <option value="popup" {{ request('type') == 'popup' ? 'selected' : '' }}>Popup</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Banners Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">All Banners</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="bannersTable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Position</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Clicks</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Sample data since $banners might not be properly passed -->
                    <tr>
                        <td>
                            <img src="https://via.placeholder.com/80x50/007bff/ffffff?text=Banner+1" 
                                 class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;" alt="Banner">
                        </td>
                        <td>
                            <div>
                                <div class="fw-bold">Summer Sale 2024</div>
                                <small class="text-muted">Up to 50% off on all summer items</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary">Homepage</span>
                        </td>
                        <td>
                            <span class="badge bg-info">Carousel</span>
                        </td>
                        <td>
                            <span class="badge bg-warning">1</span>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-success">1,234</div>
                                <small class="text-muted">clicks</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td>
                            <div>Jan 15, 2024</div>
                            <small class="text-muted">2 weeks ago</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.banners.show', 1) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.banners.edit', 1) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus(1)">
                                    <i class="fas fa-toggle-on"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteBanner(1)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img src="https://via.placeholder.com/80x50/28a745/ffffff?text=Banner+2" 
                                 class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;" alt="Banner">
                        </td>
                        <td>
                            <div>
                                <div class="fw-bold">New Arrivals</div>
                                <small class="text-muted">Check out our latest collection</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">Category</span>
                        </td>
                        <td>
                            <span class="badge bg-success">Image</span>
                        </td>
                        <td>
                            <span class="badge bg-warning">2</span>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-success">856</div>
                                <small class="text-muted">clicks</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td>
                            <div>Jan 10, 2024</div>
                            <small class="text-muted">3 weeks ago</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.banners.show', 2) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.banners.edit', 2) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus(2)">
                                    <i class="fas fa-toggle-on"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteBanner(2)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img src="https://via.placeholder.com/80x50/dc3545/ffffff?text=Banner+3" 
                                 class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;" alt="Banner">
                        </td>
                        <td>
                            <div>
                                <div class="fw-bold">Flash Sale</div>
                                <small class="text-muted">Limited time offer - 24 hours only</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-danger">Popup</span>
                        </td>
                        <td>
                            <span class="badge bg-danger">Popup</span>
                        </td>
                        <td>
                            <span class="badge bg-warning">3</span>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-success">2,156</div>
                                <small class="text-muted">clicks</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">Inactive</span>
                        </td>
                        <td>
                            <div>Dec 25, 2023</div>
                            <small class="text-muted">1 month ago</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.banners.show', 3) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.banners.edit', 3) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus(3)">
                                    <i class="fas fa-toggle-off"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteBanner(3)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        @if(isset($banners) && $banners->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $banners->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Banner Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to change the status of this banner?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusUpdate">Update Status</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this banner? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Deleting this banner will remove it from all pages where it's currently displayed.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete Banner</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Check if DataTable already exists and destroy it
    if ($.fn.DataTable.isDataTable('#bannersTable')) {
        $('#bannersTable').DataTable().destroy();
    }
    
    // Initialize DataTable
    $('#bannersTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 25,
        order: [[7, 'desc']], // Sort by created date
        columnDefs: [
            { orderable: false, targets: [0, 8] } // Image and Actions columns
        ],
        language: {
            search: "Search banners:",
            lengthMenu: "Show _MENU_ banners per page",
            info: "Showing _START_ to _END_ of _TOTAL_ banners",
            infoEmpty: "No banners found",
            infoFiltered: "(filtered from _MAX_ total banners)",
            emptyTable: "No banners available"
        }
    });
});

let currentBannerId = null;

function toggleStatus(bannerId) {
    currentBannerId = bannerId;
    $('#statusModal').modal('show');
}

function deleteBanner(bannerId) {
    currentBannerId = bannerId;
    $('#deleteModal').modal('show');
}

// Handle status update confirmation
$('#confirmStatusUpdate').click(function() {
    if (currentBannerId) {
        $.ajax({
            url: `/admin/banners/${currentBannerId}/toggle-status`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating banner status');
                }
            },
            error: function() {
                alert('Error updating banner status');
            }
        });
    }
    $('#statusModal').modal('hide');
});

// Handle delete confirmation
$('#confirmDelete').click(function() {
    if (currentBannerId) {
        $.ajax({
            url: `/admin/banners/${currentBannerId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting banner');
                }
            },
            error: function() {
                alert('Error deleting banner');
            }
        });
    }
    $('#deleteModal').modal('hide');
});
</script>
@endpush
