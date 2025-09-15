@extends('layouts.admin')

@section('title', 'Edit Coupon')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Edit Coupon</h1>
            <p class="page-subtitle">Update coupon details and settings</p>
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
            <li class="breadcrumb-item active">Edit</li>
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
                <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Coupon Code *</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       name="code" value="{{ old('code', $coupon->code) }}" required placeholder="e.g., SAVE20">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Unique code that customers will use</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status *</label>
                                <select class="form-select @error('is_active') is-invalid @enderror" name="is_active" required>
                                    <option value="1" {{ old('is_active', $coupon->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $coupon->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="3" placeholder="Brief description of the coupon">{{ old('description', $coupon->description) }}</textarea>
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
                                    <option value="percent" {{ old('type', $coupon->type) == 'percent' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
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
                                       name="value" value="{{ old('value', $coupon->value) }}" required min="0" step="0.01" placeholder="0.00">
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
                                <label class="form-label">Minimum Cart Value</label>
                                <input type="number" class="form-control @error('min_cart_value') is-invalid @enderror" 
                                       name="min_cart_value" value="{{ old('min_cart_value', $coupon->min_cart_value) }}" min="0" step="0.01" placeholder="0.00">
                                @error('min_cart_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Minimum order amount to apply coupon</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Usage Limit</label>
                                <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                       name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1" placeholder="Unlimited">
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Maximum number of times this coupon can be used</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                       name="start_date" value="{{ old('start_date', $coupon->start_date ? $coupon->start_date->format('Y-m-d\TH:i') : '') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">When the coupon becomes active</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Expiry Date</label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                       name="end_date" value="{{ old('end_date', $coupon->end_date ? $coupon->end_date->format('Y-m-d\TH:i') : '') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">When the coupon expires (optional)</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Coupon
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Coupon Preview</h5>
            </div>
            <div class="card-body">
                <div class="coupon-preview p-3 border rounded bg-light">
                    <div class="text-center">
                        <h4 class="text-primary mb-2" id="previewCode">{{ $coupon->code }}</h4>
                        <p class="text-muted mb-2" id="previewDescription">{{ $coupon->description ?: 'No description' }}</p>
                        <div class="badge bg-success fs-6 mb-2" id="previewDiscount">
                            @if($coupon->type === 'percent')
                                {{ $coupon->value }}% OFF
                            @else
                                ${{ $coupon->value }} OFF
                            @endif
                        </div>
                        <div class="small text-muted">
                            <div>Min. Order: <span id="previewMinAmount">${{ $coupon->min_cart_value ?: '0.00' }}</span></div>
                            <div>Max Uses: <span id="previewMaxUses">{{ $coupon->usage_limit ?: 'Unlimited' }}</span></div>
                            @if($coupon->end_date)
                            <div>Expires: {{ $coupon->end_date->format('M d, Y') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="previewCoupon()">
                        <i class="fas fa-eye me-2"></i>Update Preview
                    </button>
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
                            <h5 class="text-primary mb-1">0</h5>
                            <small class="text-muted">Times Used</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="text-success mb-1">${{ $coupon->usage_limit ?: '∞' }}</h5>
                        <small class="text-muted">Remaining</small>
                    </div>
                </div>
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
    
    if (type === 'percent') {
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

function previewCoupon() {
    const code = document.querySelector('input[name="code"]').value || 'COUPON CODE';
    const description = document.querySelector('textarea[name="description"]').value || 'No description';
    const type = document.querySelector('select[name="type"]').value;
    const value = document.querySelector('input[name="value"]').value || '0';
    const minAmount = document.querySelector('input[name="min_cart_value"]').value || '0';
    const maxUses = document.querySelector('input[name="usage_limit"]').value || '';
    
    document.getElementById('previewCode').textContent = code;
    document.getElementById('previewDescription').textContent = description;
    
    let discountText = '';
    if (type === 'percent') {
        discountText = `${value}% OFF`;
    } else if (type === 'fixed') {
        discountText = `$${value} OFF`;
    } else {
        discountText = 'Discount Amount';
    }
    document.getElementById('previewDiscount').textContent = discountText;
    
    document.getElementById('previewMinAmount').textContent = minAmount ? `$${minAmount}` : '$0.00';
    document.getElementById('previewMaxUses').textContent = maxUses || 'Unlimited';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateValueLabel();
});
</script>
@endpush
