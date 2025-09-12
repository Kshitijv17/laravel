@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="row g-4">
    <div class="col-md-6">
      <div class="card card-hover">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-box-open me-2"></i> Products</h5>
          <p class="card-text">Manage all products listed in your store.</p>
          <a href="{{ route('admin.products.index') }}" class="btn btn-primary">View Products</a>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card card-hover">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-tags me-2"></i> Categories</h5>
          <p class="card-text">Organize products into categories for better browsing.</p>
          <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">View Categories</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
