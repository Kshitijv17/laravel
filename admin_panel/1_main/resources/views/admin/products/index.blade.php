@extends('admin.layout')

@section('content')
<div class="container py-4">
  <h2>Products</h2>
  <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Add Product</a>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <table class="table table-bordered">
    <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($products as $product)
        <tr>
          <td>{{ $product->name }}</td>
          <td>{{ $product->category->name ?? '—' }}</td>
          <td>₹{{ $product->price }}</td>
          <td>
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>
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
