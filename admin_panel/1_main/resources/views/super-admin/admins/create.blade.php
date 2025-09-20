@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add New Admin</h4>
        </div>
        <div class="card-body">
          <form action="{{ route('super-admin.admin.admins.store') }}" method="POST">
            @csrf

            <div class="mb-3">
              <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name') }}" placeholder="Enter full name" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
              <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email') }}" placeholder="Enter email address" required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="role" class="form-label">Admin Role <span class="text-danger">*</span></label>
              <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                <option value="">Select Role</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
              </select>
              <div class="form-text">
                <i class="fas fa-info-circle me-1"></i>
                Super Admins have full access to all features including admin management.
              </div>
              @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
              <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                     placeholder="Enter password" required>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
              <input type="password" name="password_confirmation" id="password_confirmation"
                     class="form-control" placeholder="Confirm password" required>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-danger">
                <i class="fas fa-save me-1"></i>Create Admin
              </button>
              <a href="{{ route('super-admin.admin.admins.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
