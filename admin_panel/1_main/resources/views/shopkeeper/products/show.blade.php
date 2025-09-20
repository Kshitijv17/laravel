@extends('shopkeeper.layout')

@section('title', 'Product Details')
@section('subtitle', 'View product information')

@section('content')
<div class="container py-4">
  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0"><i class="fas fa-box me-2"></i>{{ $product->name }}</h4>
          <div>
            <a href="{{ route('shopkeeper.products.edit', $product) }}" class="btn btn-warning btn-sm">
              <i class="fas fa-edit me-1"></i>Edit
            </a>
            <a href="{{ route('shopkeeper.products.index') }}" class="btn btn-secondary btn-sm">
              <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 text-center mb-4">
              @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                     class="img-fluid rounded shadow" style="max-height: 300px;">
              @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center shadow" 
                     style="height: 300px;">
                  <i class="fas fa-image fa-4x text-muted"></i>
                </div>
              @endif
            </div>
            <div class="col-md-8">
              <div class="row">
                <div class="col-sm-6 mb-3">
                  <strong>Category:</strong>
                  @if($product->category)
                    <span class="badge bg-info ms-2">{{ $product->category->title }}</span>
                  @else
                    <span class="text-muted ms-2">No Category</span>
                  @endif
                </div>
                <div class="col-sm-6 mb-3">
                  <strong>Status:</strong>
                  @if($product->is_active)
                    <span class="badge bg-success ms-2">Active</span>
                  @else
                    <span class="badge bg-secondary ms-2">Inactive</span>
                  @endif
                </div>
                <div class="col-sm-6 mb-3">
                  <strong>Price:</strong>
                  <span class="ms-2 h5 text-primary">${{ number_format($product->price, 2) }}</span>
                  @if($product->discount_price)
                    <br><small class="text-muted ms-2"><s>${{ number_format($product->discount_price, 2) }}</s></small>
                  @endif
                </div>
                <div class="col-sm-6 mb-3">
                  <strong>Stock:</strong>
                  @if($product->quantity <= 0)
                    <span class="badge bg-danger ms-2">Out of Stock</span>
                  @elseif($product->quantity <= 10)
                    <span class="badge bg-warning ms-2">{{ $product->quantity }} left</span>
                  @else
                    <span class="badge bg-success ms-2">{{ $product->quantity }} in stock</span>
                  @endif
                </div>
                @if($product->sku)
                  <div class="col-sm-6 mb-3">
                    <strong>SKU:</strong>
                    <span class="ms-2 font-monospace">{{ $product->sku }}</span>
                  </div>
                @endif
                @if($product->weight)
                  <div class="col-sm-6 mb-3">
                    <strong>Weight:</strong>
                    <span class="ms-2">{{ $product->weight }} kg</span>
                  </div>
                @endif
                <div class="col-sm-6 mb-3">
                  <strong>Featured:</strong>
                  @if($product->is_featured)
                    <span class="badge bg-warning ms-2">Yes</span>
                  @else
                    <span class="text-muted ms-2">No</span>
                  @endif
                </div>
                <div class="col-sm-6 mb-3">
                  <strong>Created:</strong>
                  <span class="ms-2">{{ $product->created_at->format('M d, Y') }}</span>
                </div>
              </div>
            </div>
          </div>
          
          @if($product->description)
            <hr>
            <div class="mb-3">
              <strong>Description:</strong>
              <div class="mt-2 p-3 bg-light rounded">
                {{ $product->description }}
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
    
    <div class="col-lg-4">
      <!-- Product Statistics -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Product Statistics</h5>
        </div>
        <div class="card-body">
          <div class="row text-center">
            <div class="col-6 mb-3">
              <h4 class="text-primary">0</h4>
              <small class="text-muted">Total Orders</small>
            </div>
            <div class="col-6 mb-3">
              <h4 class="text-success">$0.00</h4>
              <small class="text-muted">Revenue</small>
            </div>
            <div class="col-6">
              <h4 class="text-info">0</h4>
              <small class="text-muted">Views</small>
            </div>
            <div class="col-6">
              <h4 class="text-warning">0</h4>
              <small class="text-muted">In Carts</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="{{ route('shopkeeper.products.edit', $product) }}" class="btn btn-warning">
              <i class="fas fa-edit me-1"></i>Edit Product
            </a>
            
            @if($product->is_active)
              <form action="{{ route('shopkeeper.products.toggle-status', $product) }}" method="POST">
                @csrf @method('PUT')
                <button type="submit" class="btn btn-secondary w-100">
                  <i class="fas fa-eye-slash me-1"></i>Deactivate Product
                </button>
              </form>
            @else
              <form action="{{ route('shopkeeper.products.toggle-status', $product) }}" method="POST">
                @csrf @method('PUT')
                <button type="submit" class="btn btn-success w-100">
                  <i class="fas fa-eye me-1"></i>Activate Product
                </button>
              </form>
            @endif
            
            <hr>
            
            <form action="{{ route('shopkeeper.products.destroy', $product) }}" method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger w-100">
                <i class="fas fa-trash me-1"></i>Delete Product
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
