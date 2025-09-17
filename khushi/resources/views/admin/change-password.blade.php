@extends('layouts.admin')

@section('title','Change Password')
@section('subtitle','Update your admin account password')

@section('content')
 

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-key me-2 text-primary"></i>Security</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.change-password.update') }}" method="POST" class="row g-3">
          @csrf
          @method('PUT')
          <div class="col-12">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
            @error('current_password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" required minlength="8">
            @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="8">
          </div>
          <div class="col-12 d-flex justify-content-end gap-2">
            <a href="{{ route('admin.profile') }}" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Password</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
