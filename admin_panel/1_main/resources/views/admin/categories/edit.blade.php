@extends('admin.layout')

@section('content')
<div class="container py-4">
  <h2>Edit Category</h2>
  <form action="{{ route('admin.categories.update', $category) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
    </div>
    <button class="btn btn-primary">Update</button>
  </form>
</div>
@endsection
