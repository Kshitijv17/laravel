@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Category</h4>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="row">
              <!-- Category Title -->
              <div class="col-md-6 mb-3">
                <label for="title" class="form-label">
                  <i class="fas fa-tag me-1"></i>Category Title <span class="text-danger">*</span>
                </label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $category->title) }}" placeholder="Enter category title" required>
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Category Image -->
              <div class="col-md-6 mb-3">
                <label for="image" class="form-label">
                  <i class="fas fa-image me-1"></i>Category Image
                </label>
                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror"
                       accept="image/*">
                <div class="form-text">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB</div>
                @error('image')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <!-- Current Image Display -->
                @if($category->image)
                  <div class="mt-2">
                    <label class="form-label">Current Image:</label>
                    <div>
                      <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->title }}"
                           class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                    </div>
                  </div>
                @endif

                <!-- New Image Preview -->
                <div id="image-preview" class="mt-2" style="display: none;">
                  <label class="form-label">New Image Preview:</label>
                  <img id="preview-img" src="" alt="Image Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                </div>
              </div>
            </div>

            <div class="row">
              <!-- Active Status -->
              <div class="col-md-6 mb-3">
                <label for="active" class="form-label">
                  <i class="fas fa-toggle-on me-1"></i>Status <span class="text-danger">*</span>
                </label>
                <select name="active" id="active" class="form-select @error('active') is-invalid @enderror" required>
                  <option value="active" {{ old('active', $category->active) == 'active' ? 'selected' : '' }}>
                    <i class="fas fa-check-circle me-1"></i>Active
                  </option>
                  <option value="inactive" {{ old('active', $category->active) == 'inactive' ? 'selected' : '' }}>
                    <i class="fas fa-times-circle me-1"></i>Inactive
                  </option>
                </select>
                @error('active')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Show on Home Page -->
              <div class="col-md-6 mb-3">
                <label for="show_on_home" class="form-label">
                  <i class="fas fa-home me-1"></i>Display on Home Page <span class="text-danger">*</span>
                </label>
                <select name="show_on_home" id="show_on_home" class="form-select @error('show_on_home') is-invalid @enderror" required>
                  <option value="show" {{ old('show_on_home', $category->show_on_home) == 'show' ? 'selected' : '' }}>
                    <i class="fas fa-eye me-1"></i>Show
                  </option>
                  <option value="hide" {{ old('show_on_home', $category->show_on_home) == 'hide' ? 'selected' : '' }}>
                    <i class="fas fa-eye-slash me-1"></i>Hide
                  </option>
                </select>
                <div class="form-text">Choose whether to display this category on the home page</div>
                @error('show_on_home')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex gap-2 pt-3 border-top">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Update Category
              </button>
              <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Cancel
              </a>
              <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-info">
                <i class="fas fa-eye me-1"></i>View Details
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Image preview functionality
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});
</script>

<style>
.form-label {
  font-weight: 600;
  color: #495057;
}

.card {
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
}

.img-thumbnail {
  border: 2px solid #dee2e6;
}

.btn {
  border-radius: 0.375rem;
}
</style>
@endsection
