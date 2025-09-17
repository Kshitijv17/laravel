@extends('admin.layout')

@section('content')
<div class="container py-4">
  <h2>{{ isset($product) ? 'Edit' : 'Add' }} Product</h2>
  <form action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($product)) @method('PUT') @endif

    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
    </div>

    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    <div class="mb-3">
      <label>Price</label>
      <input type="number" name="price" class="form-control" value="{{ old('price', $product->price ?? '') }}" required>
    </div>

    <div class="mb-3">
      <label>Image</label>
      <input type="file" name="image" class="form-control" accept="image/*">
      @if(isset($product) && $product->image)
        <div class="mt-2">
          <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 200px;">
          <small class="text-muted d-block">Leave empty to keep current image</small>
        </div>
      @endif
    </div>

    <div class="mb-3">
      <label>Additional Images</label>
      <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
      <small class="text-muted">You can select multiple images</small>
      @if(isset($product) && $product->images->count() > 0)
        <div class="mt-2">
          <h6>Current Additional Images:</h6>
          <div class="d-flex flex-wrap gap-2">
            @foreach($product->images as $image)
              <div class="position-relative">
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product image" class="img-thumbnail" style="max-width: 100px;">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeImage({{ $image->id }})" style="font-size: 10px; padding: 2px 4px;">×</button>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>

    <div class="mb-3">
      <label>Category</label>
      <select name="category_id" class="form-control" required>
        <option value="">Select Category</option>
        @foreach($categories as $category)
          <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
            {{ $category->name }}
          </option>
        @endforeach
      </select>
    </div>

    <button class="btn btn-success">{{ isset($product) ? 'Update' : 'Save' }}</button>
  </form>
</div>

<script>
function removeImage(imageId) {
    if (confirm('Are you sure you want to delete this image?')) {
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
@endsection
