@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="row">
    <div class="col-lg-8 mx-auto">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0"><i class="fas fa-tag me-2"></i>Category Details</h4>
          <div>
            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning btn-sm">
              <i class="fas fa-edit me-1"></i>Edit
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
              <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
          </div>
        </div>
        <div class="card-body">
          <!-- Category Image -->
          @if($category->image)
            <div class="text-center mb-4">
              <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->title }}"
                   class="img-fluid rounded shadow" style="max-width: 300px; max-height: 300px;">
            </div>
          @else
            <div class="text-center mb-4">
              <div class="bg-light rounded d-inline-flex align-items-center justify-content-center"
                   style="width: 200px; height: 200px;">
                <i class="fas fa-image fa-3x text-muted"></i>
              </div>
              <p class="text-muted mt-2">No image uploaded</p>
            </div>
          @endif

          <!-- Category Information -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold text-muted">Category Title</label>
                <div class="border rounded p-2 bg-light">
                  <i class="fas fa-tag me-2 text-primary"></i>{{ $category->title }}
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold text-muted">Status</label>
                <div class="border rounded p-2 bg-light">
                  <span class="badge {{ $category->active === 'active' ? 'bg-success' : 'bg-secondary' }} me-2">
                    <i class="fas {{ $category->active === 'active' ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                    {{ ucfirst($category->active) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold text-muted">Display on Home Page</label>
                <div class="border rounded p-2 bg-light">
                  <span class="badge {{ $category->show_on_home === 'show' ? 'bg-info' : 'bg-warning' }}">
                    <i class="fas {{ $category->show_on_home === 'show' ? 'fa-eye' : 'fa-eye-slash' }} me-1"></i>
                    {{ ucfirst($category->show_on_home) }}
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold text-muted">Created Date</label>
                <div class="border rounded p-2 bg-light">
                  <i class="fas fa-calendar me-2 text-info"></i>{{ $category->created_at->format('M d, Y \a\t h:i A') }}
                </div>
              </div>
            </div>
          </div>

          @if($category->updated_at != $category->created_at)
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold text-muted">Last Updated</label>
                <div class="border rounded p-2 bg-light">
                  <i class="fas fa-clock me-2 text-warning"></i>{{ $category->updated_at->format('M d, Y \a\t h:i A') }}
                </div>
              </div>
            </div>
          </div>
          @endif

          <!-- Products in this Category -->
          <div class="mt-4">
            <h5 class="border-bottom pb-2">
              <i class="fas fa-box-open me-2"></i>Products in this Category
              <span class="badge bg-primary ms-2">{{ $category->products->count() }}</span>
            </h5>

            @if($category->products->count() > 0)
              <div class="row">
                @foreach($category->products->take(6) as $product)
                <div class="col-md-4 mb-3">
                  <div class="card h-100">
                    @if($product->image)
                      <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->title }}"
                           style="height: 150px; object-fit: cover;">
                    @else
                      <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                           style="height: 150px;">
                        <i class="fas fa-image fa-2x text-muted"></i>
                      </div>
                    @endif
                    <div class="card-body p-2">
                      <h6 class="card-title text-truncate mb-1">{{ $product->title }}</h6>
                      <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-success">₹{{ number_format($product->price, 2) }}</span>
                        <small class="text-muted">
                          {{ $product->stock_status === 'in_stock' ? 'In Stock' : 'Out of Stock' }}
                        </small>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>

              @if($category->products->count() > 6)
              <div class="text-center mt-3">
                <small class="text-muted">
                  And {{ $category->products->count() - 6 }} more products...
                </small>
              </div>
              @endif

              <div class="text-center mt-3">
                <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-list me-1"></i>View All Products
                </a>
              </div>
            @else
              <div class="text-center py-4">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">No products in this category yet.</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus me-1"></i>Add First Product
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.card {
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
}

.form-label {
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.border {
  border: 1px solid #dee2e6 !important;
}

.bg-light {
  background-color: #f8f9fa !important;
}

.badge {
  font-size: 0.75em;
}

.card h6 {
  font-size: 0.9rem;
  line-height: 1.2;
}
</style>
@endsection
