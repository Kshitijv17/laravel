@extends('admin.layout')

@section('content')
<div class="container py-4">
  <!-- Welcome Header with Role Badge -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body py-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h2 class="mb-1">Welcome back, {{ auth()->user()->name }}!</h2>
              <p class="text-muted mb-0">Manage your store from the admin dashboard</p>
            </div>
            <div class="text-end">
              <span class="badge fs-6 px-3 py-2 bg-{{ auth()->user()->isSuperAdmin() ? 'danger' : 'warning' }}">
                <i class="fas {{ auth()->user()->isSuperAdmin() ? 'fa-crown' : 'fa-user-shield' }} me-1"></i>{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-6">
      <div class="card card-hover">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-box-open me-2"></i> Products</h5>
          <p class="card-text">Manage all products listed in your store.</p>
          <a href="{{ route('admin.products.index') }}" class="btn btn-primary">View Products</a>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card card-hover">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-tags me-2"></i> Categories</h5>
          <p class="card-text">Organize products into categories for better browsing.</p>
          <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">View Categories</a>
        </div>
      </div>
    </div>

    <!-- Super Admin Only Section -->
    @if(auth()->check() && auth()->user()->isSuperAdmin())
    <div class="col-md-6">
      <div class="card card-hover border-danger">
        <div class="card-body">
          <h5 class="card-title text-danger"><i class="fas fa-key me-2"></i>Permission Management</h5>
          <p class="card-text">Manage permissions for admin users.</p>
          <a href="{{ route('admin.permissions.index') }}" class="btn btn-danger">Manage Permissions</a>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card card-hover border-warning">
        <div class="card-body">
          <h5 class="card-title text-warning"><i class="fas fa-users-cog me-2"></i>User Management</h5>
          <p class="card-text">Manage admin users and their roles.</p>
          <a href="{{ route('admin.admins.index') }}" class="btn btn-warning">Manage Users</a>
        </div>
      </div>
    </div>
    @endif

    <!-- Bulk Upload Section -->
    <div class="col-md-6">
      <div class="card card-hover">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-upload me-2"></i> Bulk Upload</h5>
          <p class="card-text">Upload multiple products at once using CSV.</p>
          <a href="{{ route('admin.products.bulk-upload-form') }}" class="btn btn-info">Bulk Upload</a>
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-md-6">
      <div class="card card-hover">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-chart-line me-2"></i> Quick Stats</h5>
          <div class="row text-center">
            <div class="col-6">
              <div class="h4 mb-0 text-primary">{{ \App\Models\Product::count() }}</div>
              <small class="text-muted">Products</small>
            </div>
            <div class="col-6">
              <div class="h4 mb-0 text-success">{{ \App\Models\Category::count() }}</div>
              <small class="text-muted">Categories</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
