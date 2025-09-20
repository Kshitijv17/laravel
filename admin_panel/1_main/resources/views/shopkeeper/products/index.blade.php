@extends('shopkeeper.layout')

@section('title', 'My Products')
@section('subtitle', 'Manage your shop products')

@section('content')
<div class="container-fluid py-4">
  <!-- Stats Cards -->
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-primary text-white h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Total Products</h6>
              <h3 class="mb-0">{{ $stats['total_products'] }}</h3>
            </div>
            <i class="fas fa-box fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-success text-white h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Active Products</h6>
              <h3 class="mb-0">{{ $stats['active_products'] }}</h3>
            </div>
            <i class="fas fa-check-circle fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-warning text-white h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Low Stock</h6>
              <h3 class="mb-0">{{ $stats['low_stock'] }}</h3>
            </div>
            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-danger text-white h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Out of Stock</h6>
              <h3 class="mb-0">{{ $stats['out_of_stock'] }}</h3>
            </div>
            <i class="fas fa-times-circle fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters and Actions -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-md-6">
              <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                <select name="category" class="form-select">
                  <option value="">All Categories</option>
                  @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                      {{ $category->title }}
                    </option>
                  @endforeach
                </select>
                <select name="stock" class="form-select">
                  <option value="">All Stock</option>
                  <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>Low Stock</option>
                  <option value="out" {{ request('stock') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                </select>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-search"></i>
                </button>
              </form>
            </div>
            <div class="col-md-6 text-end">
              <a href="{{ route('shopkeeper.products.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i>Add New Product
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Products Table -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="fas fa-box-open me-2"></i>Products
            @if($products->total() > 0)
              <span class="badge bg-primary ms-2">{{ $products->total() }} items</span>
            @endif
          </h5>
        </div>
        <div class="card-body p-0">
          @if($products->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($products as $product)
                    <tr>
                      <td>
                        @if($product->image)
                          <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                               class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                          <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                               style="width: 50px; height: 50px;">
                            <i class="fas fa-image text-muted"></i>
                          </div>
                        @endif
                      </td>
                      <td>
                        <div>
                          <strong>{{ $product->name }}</strong>
                          @if($product->description)
                            <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                          @endif
                        </div>
                      </td>
                      <td>
                        @if($product->category)
                          <span class="badge bg-info">{{ $product->category->title }}</span>
                        @else
                          <span class="text-muted">No Category</span>
                        @endif
                      </td>
                      <td>
                        <strong>${{ number_format($product->price, 2) }}</strong>
                        @if($product->discount_price)
                          <br><small class="text-muted"><s>${{ number_format($product->discount_price, 2) }}</s></small>
                        @endif
                      </td>
                      <td>
                        @if($product->quantity <= 0)
                          <span class="badge bg-danger">Out of Stock</span>
                        @elseif($product->quantity <= 10)
                          <span class="badge bg-warning">{{ $product->quantity }} left</span>
                        @else
                          <span class="badge bg-success">{{ $product->quantity }} in stock</span>
                        @endif
                      </td>
                      <td>
                        @if($product->is_active)
                          <span class="badge bg-success">Active</span>
                        @else
                          <span class="badge bg-secondary">Inactive</span>
                        @endif
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="{{ route('shopkeeper.products.show', $product) }}" 
                             class="btn btn-sm btn-info" title="View">
                            <i class="fas fa-eye"></i>
                          </a>
                          <a href="{{ route('shopkeeper.products.edit', $product) }}" 
                             class="btn btn-sm btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                          </a>
                          <form action="{{ route('shopkeeper.products.destroy', $product) }}" 
                                method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this product?')" 
                                    title="Delete">
                              <i class="fas fa-trash"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            
            <!-- Pagination -->
            @if($products->hasPages())
              <div class="card-footer">
                {{ $products->appends(request()->query())->links() }}
              </div>
            @endif
          @else
            <div class="text-center py-5">
              <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
              <h4 class="text-muted">No Products Found</h4>
              @if(request()->hasAny(['search', 'category', 'stock']))
                <p class="text-muted">Try adjusting your search criteria.</p>
                <a href="{{ route('shopkeeper.products.index') }}" class="btn btn-secondary">
                  <i class="fas fa-times me-1"></i>Clear Filters
                </a>
              @else
                <p class="text-muted">Start by adding your first product to your shop.</p>
                <a href="{{ route('shopkeeper.products.create') }}" class="btn btn-success">
                  <i class="fas fa-plus me-1"></i>Add First Product
                </a>
              @endif
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
