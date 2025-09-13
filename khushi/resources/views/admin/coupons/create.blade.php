@extends('layouts.admin')

@section('title', 'Create Coupon')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Create New Coupon</h1>
            <p class="page-subtitle">Add a new discount coupon for customers</p>
        </div>
        <div>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Coupons
            </a>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
            <li class="breadcrumb-item active">Create</li>
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
                <form action="{{ route('admin.coupons.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Coupon Code *</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       name="code" value="{{ old('code') }}" required placeholder="e.g., SAVE20">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Unique code that customers will use</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="3" placeholder="Brief description of the coupon">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Optional description for internal reference</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Discount Type *</label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" required onchange="updateValueLabel()">
                                    <option value="">Select Type</option>
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" id="valueLabel">Discount Value *</label>
                                <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                       name="value" value="{{ old('value') }}" required min="0" step="0.01" placeholder="0.00">
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted" id="valueHelp">Enter the discount amount</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Minimum Order Amount</label>
                                <input type="number" class="form-control @error('min_amount') is-invalid @enderror" 
                                       name="min_amount" value="{{ old('min_amount') }}" min="0" step="0.01" placeholder="0.00">
                                @error('min_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Minimum cart value required (optional)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Maximum Uses</label>
                                <input type="number" class="form-control @error('max_uses') is-invalid @enderror" 
                                       name="max_uses" value="{{ old('max_uses') }}" min="1" placeholder="Unlimited">
                                @error('max_uses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Total number of times coupon can be used (optional)</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                               name="expires_at" value="{{ old('expires_at') }}">
                        @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Leave empty for no expiry date</small>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Coupon
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Coupon Guidelines</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Best Practices</h6>
                    <ul class="mb-0 small">
                        <li>Use clear, memorable coupon codes</li>
                        <li>Set appropriate minimum order amounts</li>
                        <li>Consider limiting usage to prevent abuse</li>
                        <li>Set expiry dates for time-sensitive offers</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Important Notes</h6>
                    <ul class="mb-0 small">
                        <li>Coupon codes are case-insensitive</li>
                        <li>Percentage discounts are capped at 100%</li>
                        <li>Fixed amount discounts cannot exceed cart total</li>
                        <li>Inactive coupons cannot be used by customers</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Coupon Types</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary"><i class="fas fa-percentage me-2"></i>Percentage Discount</h6>
                    <p class="small text-muted mb-0">
                        Reduces the cart total by a percentage (e.g., 20% off).
                        Value should be between 1-100.
                    </p>
                </div>
                <hr>
                <div class="mb-0">
                    <h6 class="text-success"><i class="fas fa-dollar-sign me-2"></i>Fixed Amount Discount</h6>
                    <p class="small text-muted mb-0">
                        Reduces the cart total by a fixed amount (e.g., $10 off).
                        Value should be in your store's currency.
                    </p>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="generateCode()">
                        <i class="fas fa-random me-2"></i>Generate Random Code
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="previewCoupon()">
                        <i class="fas fa-eye me-2"></i>Preview Coupon
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Coupon Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h4 class="text-primary mb-3" id="previewCode">COUPON CODE</h4>
                        <p class="mb-2" id="previewDescription">Coupon Description</p>
                        <h5 class="text-success mb-3" id="previewDiscount">Discount Amount</h5>
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted">Min Order</small>
                                <div id="previewMinAmount">$0.00</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Max Uses</small>
                                <div id="previewMaxUses">Unlimited</div>
                            </div>
                        </div>
                        <hr>
                        <small class="text-muted" id="previewExpiry">No expiry date</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateValueLabel() {
    const type = document.querySelector('select[name="type"]').value;
    const label = document.getElementById('valueLabel');
    const help = document.getElementById('valueHelp');
    
    if (type === 'percentage') {
        label.textContent = 'Percentage (%) *';
        help.textContent = 'Enter percentage value (1-100)';
    } else if (type === 'fixed') {
        label.textContent = 'Fixed Amount ($) *';
        help.textContent = 'Enter fixed discount amount';
    } else {
        label.textContent = 'Discount Value *';
        help.textContent = 'Enter the discount amount';
    }
}

function generateCode() {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let result = '';
    for (let i = 0; i < 8; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    document.querySelector('input[name="code"]').value = result;
}

function previewCoupon() {
    const code = document.querySelector('input[name="code"]').value || 'COUPON CODE';
    const description = document.querySelector('textarea[name="description"]').value || 'No description';
    const type = document.querySelector('select[name="type"]').value;
    const value = document.querySelector('input[name="value"]').value || '0';
    const minAmount = document.querySelector('input[name="min_amount"]').value || '0';
    const maxUses = document.querySelector('input[name="max_uses"]').value || '';
    const expiresAt = document.querySelector('input[name="expires_at"]').value;
    
    document.getElementById('previewCode').textContent = code;
    document.getElementById('previewDescription').textContent = description;
    
    let discountText = '';
    if (type === 'percentage') {
        discountText = `${value}% OFF`;
    } else if (type === 'fixed') {
        discountText = `$${value} OFF`;
    } else {
        discountText = 'Discount Amount';
    }
    document.getElementById('previewDiscount').textContent = discountText;
    
    document.getElementById('previewMinAmount').textContent = minAmount ? `$${minAmount}` : '$0.00';
    document.getElementById('previewMaxUses').textContent = maxUses || 'Unlimited';
    
    if (expiresAt) {
        const date = new Date(expiresAt);
        document.getElementById('previewExpiry').textContent = `Expires: ${date.toLocaleDateString()}`;
    } else {
        document.getElementById('previewExpiry').textContent = 'No expiry date';
    }
    
    $('#previewModal').modal('show');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateValueLabel();
});
</script>
@endpush
