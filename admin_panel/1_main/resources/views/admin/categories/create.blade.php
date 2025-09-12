@extends('admin.layout')

@section('content')
<div class="container py-4">
  <h2>Add Category</h2>
  <form action="{{ route('admin.categories.store') }}" method="POST">
    @csrf
    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <button class="btn btn-success">Save</button>
  </form>
</div>
@endsection
