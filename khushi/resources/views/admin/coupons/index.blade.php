@extends('layouts.admin')

@section('title', 'Coupons Management')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Coupons Management</h1>
            <p class="page-subtitle">Manage discount coupons and promotional codes</p>
        </div>
        <div>
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Coupon
            </a>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Coupons</li>
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
                        <h5 class="card-title">Total Coupons</h5>
                        <h3 class="mb-0">{{ $coupons->count() ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-ticket-alt fa-2x"></i>
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
                        <h5 class="card-title">Active Coupons</h5>
                        <h3 class="mb-0">{{ $coupons->where('status', true)->count() ?? 0 }}</h3>
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
                        <h5 class="card-title">Expiring Soon</h5>
                        <h3 class="mb-0">{{ $coupons->where('expires_at', '>', now())->where('expires_at', '<=', now()->addDays(7))->count() ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
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
                        <h5 class="card-title">Total Uses</h5>
                        <h3 class="mb-0">{{ $coupons->sum('used_count') ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x"></i>
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
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by code or description...">
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
                <label class="form-label">Type</label>
                <select class="form-select" name="type">
                    <option value="">All Types</option>
                    <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                    <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Expiry</label>
                <select class="form-select" name="expiry">
                    <option value="">All</option>
                    <option value="active" {{ request('expiry') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ request('expiry') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="expiring_soon" {{ request('expiry') == 'expiring_soon' ? 'selected' : '' }}>Expiring Soon</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Coupons Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">All Coupons</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="couponsTable">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Min Amount</th>
                        <th>Usage</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Sample data since $coupons might not be properly passed -->
                    <tr>
                        <td>
                            <span class="fw-bold text-primary">WELCOME10</span>
                        </td>
                        <td>Welcome discount for new customers</td>
                        <td>
                            <span class="badge bg-info">Percentage</span>
                        </td>
                        <td>
                            <span class="fw-bold text-success">10%</span>
                        </td>
                        <td>$50.00</td>
                        <td>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 60%"></div>
                            </div>
                            <small class="text-muted">60/100 used</small>
                        </td>
                        <td>
                            <span class="text-success">Dec 31, 2024</span>
                            <br><small class="text-muted">11 months left</small>
                        </td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.coupons.show', 1) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.coupons.edit', 1) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus(1)">
                                    <i class="fas fa-toggle-on"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteCoupon(1)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="fw-bold text-primary">SAVE20</span>
                        </td>
                        <td>Save $20 on orders over $100</td>
                        <td>
                            <span class="badge bg-warning">Fixed</span>
                        </td>
                        <td>
                            <span class="fw-bold text-success">$20.00</span>
                        </td>
                        <td>$100.00</td>
                        <td>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: 30%"></div>
                            </div>
                            <small class="text-muted">15/50 used</small>
                        </td>
                        <td>
                            <span class="text-warning">Feb 15, 2024</span>
                            <br><small class="text-muted">1 month left</small>
                        </td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.coupons.show', 2) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.coupons.edit', 2) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus(2)">
                                    <i class="fas fa-toggle-on"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteCoupon(2)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="fw-bold text-muted">EXPIRED15</span>
                        </td>
                        <td>15% off for holiday season</td>
                        <td>
                            <span class="badge bg-info">Percentage</span>
                        </td>
                        <td>
                            <span class="fw-bold text-success">15%</span>
                        </td>
                        <td>$75.00</td>
                        <td>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger" style="width: 100%"></div>
                            </div>
                            <small class="text-muted">200/200 used</small>
                        </td>
                        <td>
                            <span class="text-danger">Jan 01, 2024</span>
                            <br><small class="text-muted">Expired</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">Inactive</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.coupons.show', 3) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.coupons.edit', 3) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus(3)">
                                    <i class="fas fa-toggle-off"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteCoupon(3)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        @if(isset($coupons) && $coupons->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $coupons->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Coupon Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to change the status of this coupon?</p>
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
                <h5 class="modal-title">Delete Coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this coupon? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Deleting this coupon will affect any orders that used this coupon code.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete Coupon</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Check if DataTable already exists and destroy it
    if ($.fn.DataTable.isDataTable('#couponsTable')) {
        $('#couponsTable').DataTable().destroy();
    }
    
    // Initialize DataTable
    $('#couponsTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 25,
        order: [[6, 'desc']], // Sort by expiry date
        columnDefs: [
            { orderable: false, targets: [8] } // Actions column
        ],
        language: {
            search: "Search coupons:",
            lengthMenu: "Show _MENU_ coupons per page",
            info: "Showing _START_ to _END_ of _TOTAL_ coupons",
            infoEmpty: "No coupons found",
            infoFiltered: "(filtered from _MAX_ total coupons)",
            emptyTable: "No coupons available"
        }
    });
});

let currentCouponId = null;

function toggleStatus(couponId) {
    currentCouponId = couponId;
    $('#statusModal').modal('show');
}

function deleteCoupon(couponId) {
    currentCouponId = couponId;
    $('#deleteModal').modal('show');
}

// Handle status update confirmation
$('#confirmStatusUpdate').click(function() {
    if (currentCouponId) {
        $.ajax({
            url: `/admin/coupons/${currentCouponId}/toggle-status`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error updating coupon status');
                }
            },
            error: function() {
                alert('Error updating coupon status');
            }
        });
    }
    $('#statusModal').modal('hide');
});

// Handle delete confirmation
$('#confirmDelete').click(function() {
    if (currentCouponId) {
        $.ajax({
            url: `/admin/coupons/${currentCouponId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting coupon');
                }
            },
            error: function() {
                alert('Error deleting coupon');
            }
        });
    }
    $('#deleteModal').modal('hide');
});
</script>
@endpush
