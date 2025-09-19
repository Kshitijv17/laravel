@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tags me-2"></i>Categories</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i>Add Category
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-dark">
            <tr>
              <th width="8%">Image</th>
              <th width="25%">Title</th>
              <th width="15%">Status</th>
              <th width="15%">Home Display</th>
              <th width="15%">Created</th>
              <th width="12%">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($categories as $category)
              <tr>
                <td>
                  @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->title }}" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                  @else
                    <div class="text-center">
                      <i class="fas fa-image text-muted" style="font-size: 24px;"></i>
                    </div>
                  @endif
                </td>
                <td>
                  <div class="fw-bold">{{ $category->title }}</div>
                </td>
                <td>
                  <span class="badge {{ $category->active === 'active' ? 'bg-success' : 'bg-secondary' }}">
                    <i class="fas {{ $category->active === 'active' ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                    {{ ucfirst($category->active) }}
                  </span>
                </td>
                <td>
                  <span class="badge {{ $category->show_on_home === 'show' ? 'bg-info' : 'bg-warning' }}">
                    <i class="fas {{ $category->show_on_home === 'show' ? 'fa-eye' : 'fa-eye-slash' }} me-1"></i>
                    {{ ucfirst($category->show_on_home) }}
                  </span>
                </td>
                <td>
                  <small class="text-muted">{{ $category->created_at ? $category->created_at->format('M d, Y') : 'N/A' }}</small>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-info" title="View Category">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning" title="Edit Category">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?')" title="Delete Category">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    @if($categories->isEmpty())
      <div class="card-body text-center py-5">
        <i class="fas fa-tags fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Categories Found</h4>
        <p class="text-muted">Start by adding your first category to organize your products.</p>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
          <i class="fas fa-plus me-1"></i>Add Your First Category
        </a>
      </div>
    @endif
  </div>
</div>

<style>
.table th {
  vertical-align: middle;
  font-weight: 600;
}

.table td {
  vertical-align: middle;
}

.btn-group .btn {
  border-radius: 0.25rem !important;
  margin-right: 2px;
}

.badge {
  font-size: 0.75em;
}
</style>
@endsection
