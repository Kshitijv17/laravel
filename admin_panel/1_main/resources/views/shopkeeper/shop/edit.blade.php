@extends('shopkeeper.layout')

@section('title', 'Edit Shop')
@section('subtitle', 'Update your shop information')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0"><i class="fas fa-store me-2"></i>Edit Shop: {{ $shop->name }}</h4>
        </div>
        <div class="card-body">
          <form action="{{ route('shopkeeper.shop.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="name" class="form-label">Shop Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                         value="{{ old('name', $shop->name) }}" placeholder="Enter shop name" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="email" class="form-label">Shop Email</label>
                  <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                         value="{{ old('email', $shop->email) }}" placeholder="shop@example.com">
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Shop Description</label>
              <textarea name="description" id="description" rows="4" 
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Describe your shop and what you sell...">{{ old('description', $shop->description) }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                         value="{{ old('phone', $shop->phone) }}" placeholder="+1 (555) 123-4567">
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="website" class="form-label">Website URL</label>
                  <input type="url" name="website" id="website" class="form-control @error('website') is-invalid @enderror"
                         value="{{ old('website', $shop->website) }}" placeholder="https://yourshop.com">
                  @error('website')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="address" class="form-label">Shop Address</label>
              <textarea name="address" id="address" rows="3" 
                        class="form-control @error('address') is-invalid @enderror"
                        placeholder="Enter your shop's physical address...">{{ old('address', $shop->address) }}</textarea>
              @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="logo" class="form-label">Shop Logo</label>
                  <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror"
                         accept="image/*">
                  @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  @if($shop->logo)
                    <div class="mt-2">
                      <small class="text-muted">Current logo:</small><br>
                      <img src="{{ asset('storage/' . $shop->logo) }}" alt="Current Logo" 
                           class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                    </div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="banner" class="form-label">Shop Banner</label>
                  <input type="file" name="banner" id="banner" class="form-control @error('banner') is-invalid @enderror"
                         accept="image/*">
                  @error('banner')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  @if($shop->banner)
                    <div class="mt-2">
                      <small class="text-muted">Current banner:</small><br>
                      <img src="{{ asset('storage/' . $shop->banner) }}" alt="Current Banner" 
                           class="img-thumbnail" style="max-width: 200px; max-height: 100px;">
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="commission_rate" class="form-label">Commission Rate (%)</label>
                  <input type="number" name="commission_rate" id="commission_rate" 
                         class="form-control @error('commission_rate') is-invalid @enderror"
                         value="{{ old('commission_rate', $shop->commission_rate) }}" 
                         min="0" max="100" step="0.01" readonly>
                  <small class="text-muted">This rate is set by the marketplace administrator.</small>
                  @error('commission_rate')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="is_active" class="form-label">Shop Status</label>
                  <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror">
                    <option value="1" {{ old('is_active', $shop->is_active) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $shop->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                  </select>
                  @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Update Shop
              </button>
              <a href="{{ route('shopkeeper.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Cancel
              </a>
            </div>
          </form>
        </div>
      </div>

      <!-- Shop Statistics -->
      <div class="card mt-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Shop Statistics</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3 text-center">
              <h4 class="text-primary">{{ $shop->stats['total_products'] }}</h4>
              <small class="text-muted">Total Products</small>
            </div>
            <div class="col-md-3 text-center">
              <h4 class="text-success">{{ $shop->stats['active_products'] }}</h4>
              <small class="text-muted">Active Products</small>
            </div>
            <div class="col-md-3 text-center">
              <h4 class="text-info">{{ $shop->stats['total_orders'] }}</h4>
              <small class="text-muted">Total Orders</small>
            </div>
            <div class="col-md-3 text-center">
              <h4 class="text-warning">${{ number_format($shop->stats['total_revenue'], 2) }}</h4>
              <small class="text-muted">Total Revenue</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
