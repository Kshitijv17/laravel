@extends('layouts.admin')

@section('title','Admin Profile')
@section('subtitle','Manage your admin account details')

@section('content')
 

<div class="row g-3">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-id-badge me-2 text-primary"></i>Account</h5>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-3">
          @if($admin->avatar)
            <img src="{{ asset('storage/' . $admin->avatar) }}" alt="Avatar" class="rounded-circle" style="width:72px;height:72px;object-fit:cover;">
          @else
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:72px;height:72px;">
              <i class="fas fa-user text-white fa-lg"></i>
            </div>
          @endif
          <div>
            <div class="fw-bold fs-5">{{ $admin->name }}</div>
            <div class="text-muted small">{{ $admin->email }}</div>
          </div>
        </div>
        <div class="row g-2 small text-muted">
          <div class="col-6">
            <div>Role</div>
            <div class="fw-semibold text-dark">Admin</div>
          </div>
          <div class="col-6">
            <div>Joined</div>
            <div class="fw-semibold text-dark">{{ optional($admin->created_at)->format('M d, Y') }}</div>
          </div>
        </div>
        <div class="mt-3 d-grid">
          <a href="{{ route('admin.change-password') }}" class="btn btn-light"><i class="fas fa-key me-2"></i>Change Password</a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user-gear me-2 text-primary"></i>Edit Profile</h5>
        <small class="text-muted">Update your name, email and phone</small>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.profile.update') }}" method="POST" class="row g-3">
          @csrf
          @method('PUT')
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ old('name', $admin->name) }}" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $admin->phone) }}" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $admin->email) }}" class="form-control" required>
          </div>
          <div class="col-12 d-flex justify-content-end gap-2">
            <button type="reset" class="btn btn-light">Reset</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
