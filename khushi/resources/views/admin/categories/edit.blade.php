@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Edit Category</h1>
            <p class="page-subtitle">Update category information</p>
        </div>
        <div>
            <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-2"></i>View Category
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Categories
            </a>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.categories.show', $category->id) }}">{{ $category->name }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Category Information</h5>
            </div>
            <div class="card-body">
                <form id="categoryEditForm" action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Optional description for the category</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                            <option value="1" {{ old('status', $category->status) == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $category->status) == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Current Image</h5>
            </div>
            <div class="card-body text-center">
                <img id="currentImage" 
                     src="{{ $category->image_url }}" 
                     class="img-fluid rounded mb-3" style="max-height: 200px;" alt="{{ $category->name }}">
                
                <div class="mb-3">
                    <label class="form-label">Upload New Image</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                           name="image" accept="image/*" onchange="previewImage(this)" form="categoryEditForm">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Max file size: 2MB. Leave empty to keep current image.</small>
                </div>
                
                <div class="text-center">
                    <img id="imagePreview" src="" class="img-fluid rounded" style="max-height: 200px; display: none;" alt="New Image Preview">
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">SEO Settings</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Meta Title</label>
                    <input type="text" class="form-control" name="meta_title" 
                           value="{{ old('meta_title', $category->meta_title ?? '') }}" form="categoryEditForm">
                    <small class="form-text text-muted">Leave empty to use category name</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Meta Description</label>
                    <textarea class="form-control" name="meta_description" rows="3" form="categoryEditForm">{{ old('meta_description', $category->meta_description ?? '') }}</textarea>
                    <small class="form-text text-muted">Recommended: 150-160 characters</small>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Category Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="h5 text-primary">{{ $category->products->count() ?? 0 }}</div>
                        <div class="text-muted small">Total Products</div>
                    </div>
                    <div class="col-6">
                        <div class="h5 text-success">{{ $category->products->where('status', true)->count() ?? 0 }}</div>
                        <div class="text-muted small">Active Products</div>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="h5 text-warning">{{ $category->products->where('stock', '>', 0)->count() ?? 0 }}</div>
                        <div class="text-muted small">In Stock</div>
                    </div>
                    <div class="col-6">
                        <div class="h5 text-info">${{ number_format($category->products->avg('price') ?? 0, 2) }}</div>
                        <div class="text-muted small">Avg Price</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for file upload and SEO fields -->
<form id="updateForm" action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" style="display: none;">
    @csrf
    @method('PUT')
</form>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result).show();
            $('#currentImage').hide();
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Sync forms when main form is submitted
$('form:not(#updateForm)').on('submit', function(e) {
    e.preventDefault();
    
    // Copy image and SEO fields to main form
    const imageInput = $('input[name="image"]');
    const metaTitle = $('input[name="meta_title"]');
    const metaDescription = $('textarea[name="meta_description"]');
    
    if (imageInput.val()) {
        $(this).append(imageInput.clone());
    }
    if (metaTitle.val()) {
        $(this).append($('<input>').attr({type: 'hidden', name: 'meta_title', value: metaTitle.val()}));
    }
    if (metaDescription.val()) {
        $(this).append($('<input>').attr({type: 'hidden', name: 'meta_description', value: metaDescription.val()}));
    }
    
    this.submit();
});

// Auto-generate slug from name
$('input[name="name"]').on('input', function() {
    var name = $(this).val();
    var slug = name.toLowerCase()
        .replace(/[^\w\s-]/g, '') // Remove special characters
        .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
        .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
    
    // You can add a slug field if needed
    // $('input[name="slug"]').val(slug);
});
</script>
@endpush
