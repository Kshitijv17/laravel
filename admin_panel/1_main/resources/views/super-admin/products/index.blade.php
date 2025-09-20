@extends('super-admin.layout')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-box-open me-2"></i>Products</h2>
    <div>
      <a href="{{ route('super-admin.products.bulk-upload-form') }}" class="btn btn-success me-2">
        <i class="fas fa-upload me-1"></i>Bulk Upload
      </a>
      <a href="{{ route('super-admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add Product
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('bulk_errors'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i>
      <strong>Bulk Upload Completed with Issues:</strong> Some products could not be uploaded due to validation errors.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-dark">
            <tr>
              <th width="8%">Image</th>
              <th width="20%">Product Title</th>
              <th width="12%">Category</th>
              <th width="10%">Original Price</th>
              <th width="10%">Selling Price</th>
              <th width="8%">Stock</th>
              <th width="10%">Status</th>
              <th width="12%">Discount</th>
              <th width="10%">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($products as $product)
              <tr>
                <td>
                  @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                  @elseif($product->images->count() > 0)
                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->title }}" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                  @else
                    <div class="text-center">
                      <i class="fas fa-image text-muted" style="font-size: 24px;"></i>
                    </div>
                  @endif
                  @if($product->images->count() > 0)
                    <br><small class="text-muted">+{{ $product->images->count() }} more</small>
                  @endif
                </td>
                <td>
                  <div class="fw-bold">{{ $product->title }}</div>
                  <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                </td>
                <td>
                  @if($product->category)
                    <span class="badge bg-primary">{{ $product->category->title }}</span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  <span class="fw-bold text-primary">₹{{ number_format($product->price, 2) }}</span>
                </td>
                <td>
                  @if($product->selling_price)
                    <span class="fw-bold text-success">₹{{ number_format($product->selling_price, 2) }}</span>
                    <br><small class="text-muted">{{ number_format((($product->price - $product->selling_price) / $product->price) * 100, 1) }}% off</small>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  <span class="badge {{ $product->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                    {{ $product->quantity }}
                  </span>
                  <br><small class="text-muted">{{ $product->stock_status === 'in_stock' ? 'In Stock' : 'Out of Stock' }}</small>
                </td>
                <td>
                  <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                    <i class="fas {{ $product->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  @if($product->discount_tag)
                    <span class="badge" style="background-color: {{ $product->discount_color }}; color: white;">
                      {{ $product->discount_tag }}
                    </span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{ route('super-admin.products.show', $product) }}" class="btn btn-sm btn-info" title="View Product">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('super-admin.products.edit', $product) }}" class="btn btn-sm btn-warning" title="Edit Product">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('super-admin.products.destroy', $product) }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')" title="Delete Product">
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
    </div>

    @if($products->isEmpty())
      <div class="card-body text-center py-5">
        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Products Found</h4>
        <p class="text-muted">Start by adding your first product to the catalog.</p>
        <a href="{{ route('super-admin.products.create') }}" class="btn btn-primary">
          <i class="fas fa-plus me-1"></i>Add Your First Product
        </a>
      </div>
    @endif
  </div>
</div>

<style>
.table th {
  vertical-align: middle;
  font-weight: 600;
}

.table td {
  vertical-align: middle;
}

.btn-group .btn {
  border-radius: 0.25rem !important;
  margin-right: 2px;
}

.badge {
  font-size: 0.75em;
}
</style>
@endsection
