@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus-circle me-2"></i>Add New Product</h2>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left me-1"></i>Back to Products
    </a>
  </div>

  <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

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
              <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
              <div class="form-text">Enter a descriptive title for the product</div>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label fw-bold">Product Description</label>
              <textarea name="description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
              <div class="form-text">Provide a detailed description of the product</div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="price" class="form-label fw-bold">Original Price (₹) <span class="text-danger">*</span></label>
                  <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" step="0.01" min="0" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="selling_price" class="form-label fw-bold">Selling Price (₹)</label>
                  <input type="number" name="selling_price" id="selling_price" class="form-control" value="{{ old('selling_price') }}" step="0.01" min="0">
                  <div class="form-text">Leave empty if same as original price</div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="category_id" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
              <select name="category_id" id="category_id" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->title }}
                  </option>
                @endforeach
              </select>
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
              <textarea name="features" id="features" class="form-control rich-editor" rows="6">{{ old('features') }}</textarea>
              <div class="form-text">List the key features and benefits of the product</div>
            </div>

            <div class="mb-3">
              <label for="specifications" class="form-label fw-bold">Specifications</label>
              <textarea name="specifications" id="specifications" class="form-control rich-editor" rows="6">{{ old('specifications') }}</textarea>
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
              <div id="image-preview" class="mt-2" style="display: none;">
                <img id="preview-img" src="" alt="Preview" class="img-fluid rounded" style="max-width: 200px;">
              </div>
            </div>

            <div class="mb-3">
              <label for="images" class="form-label fw-bold">Gallery Images</label>
              <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple>
              <div class="form-text">Upload multiple product images (JPEG, PNG, JPG, GIF, max 2MB, max 10 images)</div>
            </div>
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
              <input type="text" name="discount_tag" id="discount_tag" class="form-control" value="{{ old('discount_tag') }}" maxlength="50">
              <div class="form-text">e.g., "20% OFF", "SALE", "HOT DEAL"</div>
            </div>

            <div class="mb-3">
              <label for="discount_color" class="form-label fw-bold">Tag Color</label>
              <div class="input-group">
                <span class="input-group-text">
                  <input type="color" id="color-picker" value="#FF0000">
                </span>
                <input type="text" name="discount_color" id="discount_color" class="form-control" value="{{ old('discount_color', '#FF0000') }}" pattern="^#[a-fA-F0-9]{6}$" required>
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
              <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', 0) }}" min="0" required>
            </div>

            <div class="mb-3">
              <label for="stock_status" class="form-label fw-bold">Stock Status <span class="text-danger">*</span></label>
              <select name="stock_status" id="stock_status" class="form-control" required>
                <option value="in_stock" {{ old('stock_status', 'in_stock') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="out_of_stock" {{ old('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Product Status <span class="text-danger">*</span></label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="is_active" id="active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="active">
                  <i class="fas fa-check-circle text-success me-1"></i>Active
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="is_active" id="inactive" value="0" {{ old('is_active') === '0' || old('is_active') === 0 ? 'checked' : '' }}>
                <label class="form-check-label" for="inactive">
                  <i class="fas fa-times-circle text-danger me-1"></i>Inactive
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid">
          <button type="submit" class="btn btn-success btn-lg">
            <i class="fas fa-save me-2"></i>Create Product
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
