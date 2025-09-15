@extends('layouts.admin')

@section('title', 'View Coupon')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Coupon Details</h1>
            <p class="page-subtitle">View coupon information and usage statistics</p>
        </div>
        <div>
            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i>Edit Coupon
            </a>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Coupons
            </a>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
            <li class="breadcrumb-item active">{{ $coupon->code }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Coupon Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Coupon Code</label>
                            <div class="form-control-plaintext">{{ $coupon->code }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div class="form-control-plaintext">
                                @if($coupon->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <div class="form-control-plaintext">{{ $coupon->description ?: 'No description provided' }}</div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Discount Type</label>
                            <div class="form-control-plaintext">
                                @if($coupon->type === 'percent')
                                    <span class="badge bg-info">Percentage</span>
                                @else
                                    <span class="badge bg-warning">Fixed Amount</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Discount Value</label>
                            <div class="form-control-plaintext">
                                @if($coupon->type === 'percent')
                                    {{ $coupon->value }}%
                                @else
                                    ${{ number_format($coupon->value, 2) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Minimum Cart Value</label>
                            <div class="form-control-plaintext">
                                @if($coupon->min_cart_value)
                                    ${{ number_format($coupon->min_cart_value, 2) }}
                                @else
                                    No minimum
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Usage Limit</label>
                            <div class="form-control-plaintext">
                                {{ $coupon->usage_limit ?: 'Unlimited' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Start Date</label>
                            <div class="form-control-plaintext">
                                @if($coupon->start_date)
                                    {{ $coupon->start_date->format('M d, Y g:i A') }}
                                @else
                                    Immediate
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Expiry Date</label>
                            <div class="form-control-plaintext">
                                @if($coupon->end_date)
                                    {{ $coupon->end_date->format('M d, Y g:i A') }}
                                    @if($coupon->end_date->isPast())
                                        <span class="badge bg-danger ms-2">Expired</span>
                                    @elseif($coupon->end_date->diffInDays() <= 7)
                                        <span class="badge bg-warning ms-2">Expires Soon</span>
                                    @endif
                                @else
                                    Never expires
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Created</label>
                            <div class="form-control-plaintext">{{ $coupon->created_at->format('M d, Y g:i A') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <div class="form-control-plaintext">{{ $coupon->updated_at->format('M d, Y g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Coupon Preview</h5>
            </div>
            <div class="card-body">
                <div class="coupon-preview p-4 border rounded bg-gradient text-white text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h3 class="mb-2">{{ $coupon->code }}</h3>
                    <p class="mb-3 opacity-75">{{ $coupon->description ?: 'Special Discount' }}</p>
                    <div class="display-6 fw-bold mb-3">
                        @if($coupon->type === 'percent')
                            {{ $coupon->value }}% OFF
                        @else
                            ${{ number_format($coupon->value, 0) }} OFF
                        @endif
                    </div>
                    <div class="small opacity-75">
                        @if($coupon->min_cart_value)
                            Min. order ${{ number_format($coupon->min_cart_value, 0) }}
                        @endif
                        @if($coupon->end_date)
                            <br>Valid until {{ $coupon->end_date->format('M d, Y') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Usage Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-1">0</h4>
                            <small class="text-muted">Times Used</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-1">
                            @if($coupon->usage_limit)
                                {{ $coupon->usage_limit }}
                            @else
                                ∞
                            @endif
                        </h4>
                        <small class="text-muted">Remaining</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <h5 class="text-info mb-1">$0.00</h5>
                    <small class="text-muted">Total Savings Generated</small>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Coupon
                    </a>
                    @if($coupon->is_active)
                        <button class="btn btn-warning" onclick="toggleStatus({{ $coupon->id }})">
                            <i class="fas fa-pause me-2"></i>Deactivate
                        </button>
                    @else
                        <button class="btn btn-success" onclick="toggleStatus({{ $coupon->id }})">
                            <i class="fas fa-play me-2"></i>Activate
                        </button>
                    @endif
                    <button class="btn btn-outline-danger" onclick="deleteCoupon({{ $coupon->id }})">
                        <i class="fas fa-trash me-2"></i>Delete Coupon
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleStatus(couponId) {
    if (confirm('Are you sure you want to change the status of this coupon?')) {
        // Add AJAX call to toggle status
        fetch(`/admin/coupons/${couponId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating coupon status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating coupon status');
        });
    }
}

function deleteCoupon(couponId) {
    if (confirm('Are you sure you want to delete this coupon? This action cannot be undone.')) {
        fetch(`/admin/coupons/${couponId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/admin/coupons';
            } else {
                alert('Error deleting coupon');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting coupon');
        });
    }
}
</script>
@endpush
