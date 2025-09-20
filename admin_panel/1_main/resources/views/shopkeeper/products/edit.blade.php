@extends('shopkeeper.layout')

@section('title', 'Edit Product')
@section('subtitle', 'Update product information')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Product: {{ $product->name }}</h4>
        </div>
        <div class="card-body">
          <form action="{{ route('shopkeeper.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="row">
              <div class="col-md-8">
                <div class="mb-3">
                  <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                         value="{{ old('name', $product->name) }}" placeholder="Enter product name" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                  <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                      <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->title }}
                      </option>
                    @endforeach
                  </select>
                  @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Product Description</label>
              <textarea name="description" id="description" rows="4" 
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Describe your product...">{{ old('description', $product->description) }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror"
                           value="{{ old('price', $product->price) }}" placeholder="0.00" step="0.01" min="0" required>
                  </div>
                  @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="discount_price" class="form-label">Discount Price</label>
                  <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="discount_price" id="discount_price" 
                           class="form-control @error('discount_price') is-invalid @enderror"
                           value="{{ old('discount_price', $product->discount_price) }}" placeholder="0.00" step="0.01" min="0">
                  </div>
                  @error('discount_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                  <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror"
                         value="{{ old('quantity', $product->quantity) }}" placeholder="0" min="0" required>
                  @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="sku" class="form-label">SKU (Stock Keeping Unit)</label>
                  <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror"
                         value="{{ old('sku', $product->sku) }}" placeholder="Enter unique SKU">
                  @error('sku')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="weight" class="form-label">Weight (kg)</label>
                  <input type="number" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror"
                         value="{{ old('weight', $product->weight) }}" placeholder="0.00" step="0.01" min="0">
                  @error('weight')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="image" class="form-label">Product Image</label>
              <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror"
                     accept="image/*">
              @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              @if($product->image)
                <div class="mt-2">
                  <small class="text-muted">Current image:</small><br>
                  <img src="{{ asset('storage/' . $product->image) }}" alt="Current Image" 
                       class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                </div>
              @endif
              <small class="text-muted">Upload a new image to replace the current one (JPG, PNG, max 2MB)</small>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="is_active" class="form-label">Product Status</label>
                  <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror">
                    <option value="1" {{ old('is_active', $product->is_active) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $product->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                  </select>
                  @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="is_featured" class="form-label">Featured Product</label>
                  <select name="is_featured" id="is_featured" class="form-select @error('is_featured') is-invalid @enderror">
                    <option value="0" {{ old('is_featured', $product->is_featured) == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('is_featured', $product->is_featured) == 1 ? 'selected' : '' }}>Yes</option>
                  </select>
                  @error('is_featured')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Update Product
              </button>
              <a href="{{ route('shopkeeper.products.show', $product) }}" class="btn btn-info">
                <i class="fas fa-eye me-1"></i>View Product
              </a>
              <a href="{{ route('shopkeeper.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
