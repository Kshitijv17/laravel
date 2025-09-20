@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users-cog me-2"></i>Admin Management</h2>
        <a href="{{ route('super-admin.admin.admins.create') }}" class="btn btn-danger">
          <i class="fas fa-plus me-1"></i>Add New Admin
        </a>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-dark">
                <tr>
                  <th width="8%">Avatar</th>
                  <th width="25%">Name</th>
                  <th width="30%">Email</th>
                  <th width="15%">Role</th>
                  <th width="15%">Created</th>
                  <th width="7%">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($admins as $admin)
                  <tr>
                    <td>
                      <div class="bg-{{ $admin->role_badge_color }} text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                           style="width: 40px; height: 40px; font-size: 16px;">
                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                      </div>
                    </td>
                    <td>
                      <div class="fw-bold">{{ $admin->name }}</div>
                      @if($admin->id === auth()->user()->id)
                        <small class="text-muted">(You)</small>
                      @endif
                    </td>
                    <td>{{ $admin->email }}</td>
                    <td>
                      <span class="badge bg-{{ $admin->role_badge_color }}">
                        <i class="fas fa-crown me-1"></i>{{ $admin->role_display }}
                      </span>
                    </td>
                    <td>
                      <small class="text-muted">{{ $admin->created_at->format('M d, Y') }}</small>
                    </td>
                    <td>
                      <div class="btn-group" role="group">
                        <a href="{{ route('super-admin.admin.admins.show', $admin) }}" class="btn btn-sm btn-info" title="View Admin">
                          <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('super-admin.admin.admins.edit', $admin) }}" class="btn btn-sm btn-warning" title="Edit Admin">
                          <i class="fas fa-edit"></i>
                        </a>
                        @if($admin->id !== auth()->user()->id && !($admin->isSuperAdmin() && $admins->where('role', 'superadmin')->count() === 1))
                          <form action="{{ route('super-admin.admin.admins.destroy', $admin) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this admin?')" title="Delete Admin">
                              <i class="fas fa-trash"></i>
                            </button>
                          </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      @if($admins->isEmpty())
        <div class="card-body text-center py-5">
          <i class="fas fa-users fa-4x text-muted mb-3"></i>
          <h4 class="text-muted">No Admin Users Found</h4>
          <p class="text-muted">Start by adding your first admin user.</p>
          <a href="{{ route('super-admin.admin.admins.create') }}" class="btn btn-danger">
            <i class="fas fa-plus me-1"></i>Add First Admin
          </a>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
