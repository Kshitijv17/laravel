@extends('admin.layout')

@section('content')
<div class="container py-4">
  <h2>Products</h2>
  <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Add Product</a>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <table class="table table-bordered">
    <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($products as $product)
        <tr>
          <td>
            @if($product->image)
              <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 60px;">
            @elseif($product->images->count() > 0)
              <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 60px;">
            @else
              <span class="text-muted">No image</span>
            @endif
            @if($product->images->count() > 0)
              <br><small class="text-muted">+{{ $product->images->count() }} more</small>
            @endif
          </td>
          <td>{{ $product->name }}</td>
          <td>{{ $product->category->name ?? '—' }}</td>
          <td>₹{{ $product->price }}</td>
          <td>
            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-info me-1">
              <i class="fas fa-eye"></i> View
            </a>
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning me-1">Edit</a>
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
