@extends('layouts.admin')

@section('title', 'Banners Management')
@section('subtitle', 'Manage promotional banners and advertisements')

@section('content')
<div class="d-flex justify-content-end align-items-center mb-3">
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Banner
    </a>
    </div>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Banners</li>
    </ol>
</nav>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Banners</h5>
                        <h3 class="mb-0">{{ $totalBanners ?? 0 }}</h3>
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
                        <h3 class="mb-0">{{ $activeBanners ?? 0 }}</h3>
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
                        <h3 class="mb-0">{{ $homepageBanners ?? 0 }}</h3>
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
                        <h5 class="card-title">Inactive Banners</h5>
                        <h3 class="mb-0">{{ $inactiveBanners ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-times-circle fa-2x"></i>
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
                    @forelse($banners as $banner)
                    <tr>
                        <td>
                            <img src="{{ $banner->image_url }}" 
                                 class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;" alt="{{ $banner->title }}">
                        </td>
                        <td>
                            <div>
                                <div class="fw-bold">{{ $banner->title }}</div>
                                @if($banner->description)
                                <small class="text-muted">{{ Str::limit($banner->description, 50) }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ ucfirst($banner->position) }}</span>
                        </td>
                        <td>
                            <span class="badge bg-info">Banner</span>
                        </td>
                        <td>
                            <span class="badge bg-warning">{{ $loop->iteration }}</span>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-success">0</div>
                                <small class="text-muted">clicks</small>
                            </div>
                        </td>
                        <td>
                            @if($banner->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $banner->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $banner->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.banners.show', $banner->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus({{ $banner->id }})">
                                    <i class="fas fa-toggle-{{ $banner->is_active ? 'on' : 'off' }}"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteBanner({{ $banner->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-image fa-3x mb-3"></i>
                                <p>No banners found</p>
                                <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Create First Banner
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
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
// Force remove any modal backdrops immediately
function clearModalBackdrops() {
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('padding-right', '').css('overflow', '');
}

// Run immediately when script loads
clearModalBackdrops();

// Run when DOM is ready
$(document).ready(function() {
    clearModalBackdrops();
    
    // Set up periodic cleanup every 500ms for the first 3 seconds
    let cleanupInterval = setInterval(clearModalBackdrops, 500);
    setTimeout(() => clearInterval(cleanupInterval), 3000);
    
    $('#bannersTable').DataTable({
        responsive: true,
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
                $('#statusModal').modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
                
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating banner status');
                }
            },
            error: function() {
                $('#statusModal').modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
                alert('Error updating banner status');
            }
        });
    } else {
        $('#statusModal').modal('hide');
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
    }
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
                $('#deleteModal').modal('hide');
                if (response.success) {
                    // Remove modal backdrops
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('padding-right', '');
                    
                    // Show success message and reload
                    alert('Banner deleted successfully!');
                    location.reload();
                } else {
                    alert('Error deleting banner');
                }
            },
            error: function(xhr) {
                $('#deleteModal').modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
                
                console.error('Delete error:', xhr);
                alert('Error deleting banner. Please try again.');
            }
        });
    } else {
        $('#deleteModal').modal('hide');
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
    }
});
</script>
@endpush
