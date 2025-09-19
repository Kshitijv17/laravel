@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0"><i class="fas fa-user me-2"></i>Admin Details</h4>
          <div>
            <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-warning btn-sm">
              <i class="fas fa-edit me-1"></i>Edit
            </a>
            <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary btn-sm">
              <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 text-center mb-4">
              <div class="bg-{{ $admin->role_badge_color }} text-white rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto"
                   style="width: 80px; height: 80px; font-size: 32px;">
                {{ strtoupper(substr($admin->name, 0, 1)) }}
              </div>
              <h5 class="mt-3">{{ $admin->name }}</h5>
              <span class="badge bg-{{ $admin->role_badge_color }} fs-6">
                <i class="fas fa-crown me-1"></i>{{ $admin->role_display }}
              </span>
            </div>
            <div class="col-md-8">
              <table class="table table-borderless">
                <tr>
                  <th width="35%">Full Name:</th>
                  <td>{{ $admin->name }}</td>
                </tr>
                <tr>
                  <th>Email:</th>
                  <td>{{ $admin->email }}</td>
                </tr>
                <tr>
                  <th>Role:</th>
                  <td>
                    <span class="badge bg-{{ $admin->role_badge_color }}">
                      <i class="fas fa-crown me-1"></i>{{ $admin->role_display }}
                    </span>
                  </td>
                </tr>
                <tr>
                  <th>Created:</th>
                  <td>{{ $admin->created_at->format('M d, Y \a\t h:i A') }}</td>
                </tr>
                <tr>
                  <th>Last Updated:</th>
                  <td>{{ $admin->updated_at->format('M d, Y \a\t h:i A') }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
