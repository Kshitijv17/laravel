@extends('layouts.app')

@section('title','Change Password')

@push('styles')
<style>
 .modern-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.25rem 0 2rem; }
 .profile-card { background: rgba(255,255,255,.95); backdrop-filter: blur(20px); border: none; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,.1); overflow: hidden; max-width: 720px; margin: 0 auto; }
 .profile-header { background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); color:#fff; padding: 1.5rem 2rem; text-align:center; }
 .profile-name { margin:0; font-weight:700; font-size:1.4rem; }
 .profile-email { margin:.2rem 0 0; opacity:.85; }
 .profile-body { padding: 1.25rem 1.25rem 1.5rem; }
</style>
@endpush

@section('content')
<div class="modern-container">
  <div class="container">
    <div class="profile-card">
      <div class="profile-header">
        <h2 class="profile-name">Change Password</h2>
        <p class="profile-email">Keep your account secure with a strong password</p>
      </div>
      <div class="profile-body">
        @if ($errors->any())
          <div class="alert alert-danger mb-3">
            <strong>Please fix the errors below.</strong>
            <ul class="mb-0 mt-2">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        @if(session('success'))
          <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif
        <form action="{{ route('user.change-password.update') }}" method="POST" class="row g-3">
          @csrf
          @method('PUT')
          <div class="col-12">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
          </div>
          <div class="col-md-6">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" required autocomplete="new-password" minlength="8">
          </div>
          <div class="col-md-6">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password" minlength="8">
          </div>
          <div class="col-12 d-flex justify-content-end gap-2">
            <a href="{{ route('user.profile') }}" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Password</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
