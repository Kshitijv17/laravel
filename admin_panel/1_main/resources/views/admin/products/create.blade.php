@extends('admin.layout')

@section('content')
<div class="container py-4">
  <h2>{{ isset($product) ? 'Edit' : 'Add' }} Product</h2>
  <form action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST">
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
@endsection
