@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Edit Product</h1>
            <p class="page-subtitle">Update product information</p>
        </div>
        <div>
            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-2"></i>View Product
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.show', $product->id) }}">{{ $product->name }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Product Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">SKU</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       name="sku" value="{{ old('sku', $product->sku) }}">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="5" required>{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Regular Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                           name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Sale Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('discount_price') is-invalid @enderror" 
                                           name="discount_price" value="{{ old('discount_price', $product->discount_price) }}" step="0.01" min="0">
                                </div>
                                @error('discount_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Stock Quantity *</label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                       name="stock" value="{{ old('stock', $product->stock) }}" min="0" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    <option value="1" {{ old('status', $product->status) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $product->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" 
                                   id="isFeatured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isFeatured">
                                Featured Product
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Product
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
                     src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x300/e5e7eb/9ca3af?text=No+Image' }}" 
                     class="img-fluid rounded mb-3" style="max-height: 200px;" alt="{{ $product->name }}">
                
                <div class="mb-3">
                    <label class="form-label">Upload New Image</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                           name="image" accept="image/*" onchange="previewImage(this)" form="updateForm">
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
                           value="{{ old('meta_title', $product->meta_title ?? '') }}" form="updateForm">
                    <small class="form-text text-muted">Leave empty to use product name</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Meta Description</label>
                    <textarea class="form-control" name="meta_description" rows="3" form="updateForm">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                    <small class="form-text text-muted">Recommended: 150-160 characters</small>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="h5 text-primary">{{ $product->orderItems->count() ?? 0 }}</div>
                        <div class="text-muted small">Orders</div>
                    </div>
                    <div class="col-6">
                        <div class="h5 text-success">${{ number_format($product->orderItems->sum('total_price') ?? 0, 2) }}</div>
                        <div class="text-muted small">Revenue</div>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="h5 text-info">{{ $product->reviews->count() ?? 0 }}</div>
                        <div class="text-muted small">Reviews</div>
                    </div>
                    <div class="col-6">
                        <div class="h5 text-warning">{{ number_format($product->reviews->avg('rating') ?? 0, 1) }}</div>
                        <div class="text-muted small">Rating</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for file upload and SEO fields -->
<form id="updateForm" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" style="display: none;">
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
