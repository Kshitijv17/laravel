@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-edit me-2"></i>Edit Product</h2>
    <div>
      <a href="{{ route('shopkeeper.products.show', $product) }}" class="btn btn-info me-2">
        <i class="fas fa-eye me-1"></i>View Product
      </a>
      <a href="{{ route('shopkeeper.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Products
      </a>
    </div>
  </div>

  <form action="{{ route('shopkeeper.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
      <!-- Basic Information -->
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="title" class="form-label fw-bold">Product Title <span class="text-danger">*</span></label>
              <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $product->title) }}" required>
              <div class="form-text">Enter a descriptive title for the product</div>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label fw-bold">Product Description</label>
              <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
              <div class="form-text">Provide a detailed description of the product</div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="price" class="form-label fw-bold">Original Price (₹) <span class="text-danger">*</span></label>
                  <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="selling_price" class="form-label fw-bold">Selling Price (₹)</label>
                  <input type="number" name="selling_price" id="selling_price" class="form-control" value="{{ old('selling_price', $product->selling_price) }}" step="0.01" min="0">
                  <div class="form-text">Leave empty if same as original price</div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="category_id" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
              <select name="category_id" id="category_id" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->title }}
                  </option>
                @endforeach
              </select>
              <div class="form-text">Select the product category</div>
            </div>

            <div class="mb-3">
              <label for="shop_id" class="form-label fw-bold">Shop <span class="text-danger">*</span></label>
              <select name="shop_id" id="shop_id" class="form-control" required>
                <option value="">Select Shop</option>
                @foreach($shops as $shop)
                  <option value="{{ $shop->id }}" {{ old('shop_id', $product->shop_id) == $shop->id ? 'selected' : '' }}>
                    {{ $shop->name }}
                  </option>
                @endforeach
              </select>
              <div class="form-text">Select the shop that will sell this product</div>
            </div>
          </div>
        </div>

        <!-- Rich Text Content -->
        <div class="card mb-4">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Product Details</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="features" class="form-label fw-bold">Features</label>
              <textarea name="features" id="features" class="form-control rich-editor" rows="6">{{ old('features', $product->features) }}</textarea>
              <div class="form-text">List the key features and benefits of the product</div>
            </div>

            <div class="mb-3">
              <label for="specifications" class="form-label fw-bold">Specifications</label>
              <textarea name="specifications" id="specifications" class="form-control rich-editor" rows="6">{{ old('specifications', $product->specifications) }}</textarea>
              <div class="form-text">Technical specifications and product details</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <!-- Images Section -->
        <div class="card mb-4">
          <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="fas fa-images me-2"></i>Product Images</h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="image" class="form-label fw-bold">Main Image</label>
              <input type="file" name="image" id="image" class="form-control" accept="image/*">
              <div class="form-text">Upload a product image (JPEG, PNG, JPG, GIF, max 2MB)</div>
              @if($product->image)
                <div id="current-image-preview" class="mt-2">
                  <img src="{{ asset('storage/' . $product->image) }}" alt="Current main image" class="img-fluid rounded shadow" style="max-width: 200px;">
                  <div class="mt-1">
                    <small class="text-muted">Current main image - will be replaced if you upload a new one</small>
                  </div>
                </div>
              @endif
              <div id="image-preview" class="mt-2" style="display: none;">
                <img id="preview-img" src="" alt="New image preview" class="img-fluid rounded" style="max-width: 200px;">
                <div class="mt-1">
                  <small class="text-success">New image selected</small>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="images" class="form-label fw-bold">Gallery Images</label>
              <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple>
              <div class="form-text">Upload multiple product images (JPEG, PNG, JPG, GIF, max 2MB, max 10 images)</div>
            </div>

            <!-- Current Gallery Images -->
            @if($product->images->count() > 0)
              <div class="mt-3">
                <h6 class="text-muted mb-2">Current Gallery Images:</h6>
                <div class="row g-2">
                  @foreach($product->images as $image)
                    <div class="col-4">
                      <div class="position-relative">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Gallery image" class="img-fluid rounded shadow" style="height: 60px; width: 100%; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeImage({{ $image->id }})" style="font-size: 10px; padding: 1px 3px; border-radius: 50%; width: 20px; height: 20px;">
                          ×
                        </button>
                      </div>
                    </div>
                  @endforeach
                </div>
                <small class="text-muted d-block mt-1">Click × to delete individual images</small>
              </div>
            @endif
          </div>
        </div>

        <!-- Discount Section -->
        <div class="card mb-4">
          <div class="card-header bg-warning text-dark">
            <h6 class="mb-0"><i class="fas fa-tag me-2"></i>Discount Settings</h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="discount_tag" class="form-label fw-bold">Discount Tag</label>
              <input type="text" name="discount_tag" id="discount_tag" class="form-control" value="{{ old('discount_tag', $product->discount_tag) }}" maxlength="50">
              <div class="form-text">e.g., "20% OFF", "SALE", "HOT DEAL"</div>
            </div>

            <div class="mb-3">
              <label for="discount_color" class="form-label fw-bold">Tag Color</label>
              <div class="input-group">
                <span class="input-group-text">
                  <input type="color" id="color-picker" value="{{ old('discount_color', $product->discount_color ?? '#FF0000') }}">
                </span>
                <input type="text" name="discount_color" id="discount_color" class="form-control" value="{{ old('discount_color', $product->discount_color ?? '#FF0000') }}" pattern="^#[a-fA-F0-9]{6}$" required>
              </div>
              <div class="form-text">Hex color code for the discount tag</div>
            </div>
          </div>
        </div>

        <!-- Inventory & Status -->
        <div class="card mb-4">
          <div class="card-header bg-secondary text-white">
            <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Inventory & Status</h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="quantity" class="form-label fw-bold">Stock Quantity <span class="text-danger">*</span></label>
              <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', $product->quantity) }}" min="0" required>
            </div>

            <div class="mb-3">
              <label for="stock_status" class="form-label fw-bold">Stock Status <span class="text-danger">*</span></label>
              <select name="stock_status" id="stock_status" class="form-control" required>
                <option value="in_stock" {{ old('stock_status', $product->stock_status) == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="out_of_stock" {{ old('stock_status', $product->stock_status) == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Product Status <span class="text-danger">*</span></label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="is_active" id="active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="active">
                  <i class="fas fa-check-circle text-success me-1"></i>Active
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="is_active" id="inactive" value="0" {{ old('is_active', $product->is_active) === false || old('is_active') === 0 || old('is_active') === '0' ? 'checked' : '' }}>
                <label class="form-check-label" for="inactive">
                  <i class="fas fa-times-circle text-danger me-1"></i>Inactive
                </label>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Featured Product</label>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_featured">
                  <i class="fas fa-star text-warning me-1"></i>Mark as Featured Product
                </label>
              </div>
              <div class="form-text">Featured products will be highlighted on the homepage</div>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid">
          <button type="submit" class="btn btn-success btn-lg">
            <i class="fas fa-save me-2"></i>Update Product
          </button>
        </div>
      </div>
    </div>
  </form>
</div>

<!-- TinyMCE Script -->
<script src="https://cdn.tiny.cloud/1/kzlev3jad3jf8ax2yw0rdtrvnlycirfux9r48mut4a8kvuu3/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
  selector: '.rich-editor',
  height: 200,
  menubar: false,
  plugins: 'lists link image code',
  toolbar: 'bold italic underline | bullist numlist | link image | code',
  content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
});

// Image preview functionality
document.getElementById('image').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('preview-img').src = e.target.result;
      document.getElementById('image-preview').style.display = 'block';
      document.getElementById('current-image-preview').style.display = 'none';
    };
    reader.readAsDataURL(file);
  }
});

// Color picker synchronization
document.getElementById('color-picker').addEventListener('input', function(e) {
  document.getElementById('discount_color').value = e.target.value;
});

document.getElementById('discount_color').addEventListener('input', function(e) {
  document.getElementById('color-picker').value = e.target.value;
});

// Image removal functionality
function removeImage(imageId) {
    if (confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
        fetch(`/admin/products/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting image');
        });
    }
}
</script>

<style>
.rich-editor {
  border: 1px solid #ced4da;
  border-radius: 0.375rem;
}

.card-header {
  border-bottom: 2px solid rgba(0,0,0,0.125);
}

.form-text {
  font-size: 0.875em;
}
</style>
@endsection
