@extends('admin.layout')

@section('content')
<div class="container py-4">
  <h2>Categories</h2>
  <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">Add Category</a>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <table class="table table-bordered">
    <thead>
      <tr><th>Name</th><th>Actions</th></tr>
    </thead>
    <tbody>
      @foreach($categories as $category)
        <tr>
          <td>{{ $category->name }}</td>
          <td>
            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning">Edit</a>
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
