@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-key me-2"></i>Permission Details</h5>
            <div>
              <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-warning btn-sm me-2">
                <i class="fas fa-edit me-1"></i>Edit
              </a>
              <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Back to Permissions
              </a>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row mb-4">
            <div class="col-md-6">
              <h6 class="text-muted">Permission Name</h6>
              <p class="h5"><code>{{ $permission->name }}</code></p>
            </div>
            <div class="col-md-6">
              <h6 class="text-muted">Display Name</h6>
              <p class="h5">{{ $permission->display_name }}</p>
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-md-6">
              <h6 class="text-muted">Module</h6>
              <p><span class="badge bg-primary fs-6">{{ ucfirst($permission->module) }}</span></p>
            </div>
            <div class="col-md-6">
              <h6 class="text-muted">Created</h6>
              <p>{{ $permission->created_at ? $permission->created_at->format('M d, Y \a\t g:i A') : 'N/A' }}</p>
            </div>
          </div>

          @if($permission->description)
            <div class="mb-4">
              <h6 class="text-muted">Description</h6>
              <p class="text-muted">{{ $permission->description }}</p>
            </div>
          @endif

          <div class="mb-4">
            <h6 class="text-muted">Usage Statistics</h6>
            <div class="row">
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h4 class="mb-0">{{ $usersWithPermission->count() }}</h4>
                    <small class="text-muted">Users Assigned</small>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <i class="fas fa-user-shield fa-2x text-warning mb-2"></i>
                    <h4 class="mb-0">{{ $usersWithPermission->where('role', 'admin')->count() }}</h4>
                    <small class="text-muted">Admins</small>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <i class="fas fa-crown fa-2x text-danger mb-2"></i>
                    <h4 class="mb-0">{{ $usersWithPermission->where('role', 'superadmin')->count() }}</h4>
                    <small class="text-muted">Super Admins</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Quick Actions</h6>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-outline-warning">
              <i class="fas fa-edit me-2"></i>Edit Permission
            </a>
            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#assignModal">
              <i class="fas fa-user-plus me-2"></i>Assign to Users
            </button>
            <button class="btn btn-outline-danger" onclick="deletePermission()">
              <i class="fas fa-trash me-2"></i>Delete Permission
            </button>
          </div>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header">
          <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Permission Info</h6>
        </div>
        <div class="card-body">
          <table class="table table-sm">
            <tr>
              <td><strong>ID:</strong></td>
              <td>{{ $permission->id }}</td>
            </tr>
            <tr>
              <td><strong>Created:</strong></td>
              <td>{{ $permission->created_at ? $permission->created_at->format('M d, Y') : 'N/A' }}</td>
            </tr>
            <tr>
              <td><strong>Updated:</strong></td>
              <td>{{ $permission->updated_at ? $permission->updated_at->format('M d, Y') : 'N/A' }}</td>
            </tr>
            <tr>
              <td><strong>Module:</strong></td>
              <td><span class="badge bg-secondary">{{ $permission->module }}</span></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Users with Permission -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0"><i class="fas fa-users me-2"></i>Users with this Permission ({{ $usersWithPermission->count() }})</h6>
        </div>
        <div class="card-body">
          @if($usersWithPermission->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Assigned Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($usersWithPermission as $user)
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="bg-{{ $user->role_badge_color }} text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3"
                               style="width: 35px; height: 35px; font-size: 14px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                          </div>
                          <div>
                            <div class="fw-bold">{{ $user->name }}</div>
                            @if($user->id === auth()->user()->id)
                              <small class="text-muted">(You)</small>
                            @endif
                          </div>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-{{ $user->role_badge_color }}">
                          @if($user->isSuperAdmin())
                            <i class="fas fa-crown me-1"></i>
                          @else
                            <i class="fas fa-user-shield me-1"></i>
                          @endif
                          {{ $user->role_display }}
                        </span>
                      </td>
                      <td>{{ $user->email }}</td>
                      <td>
                        <small class="text-muted">
                          {{ $user->pivot->created_at ?? $user->created_at->format('M d, Y') }}
                        </small>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="{{ route('admin.permissions.user.show', $user) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                          </a>
                          @if(!$user->isSuperAdmin())
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="removePermissionFromUser({{ $user->id }}, {{ $permission->id }}, '{{ $user->name }}')">
                              <i class="fas fa-times"></i>
                            </button>
                          @endif
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-4">
              <i class="fas fa-users fa-3x text-muted mb-3"></i>
              <h5 class="text-muted">No Users Assigned</h5>
              <p class="text-muted">This permission is not currently assigned to any users.</p>
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal">
                <i class="fas fa-user-plus me-1"></i>Assign to Users
              </button>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Assign Permission Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Assign Permission to Users</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="assignForm">
          @csrf
          <div class="mb-3">
            <label class="form-label">Select Users:</label>
            <div class="row">
              @foreach(\App\Models\User::whereIn('role', ['admin', 'superadmin'])->get() as $user)
                @if(!$user->isSuperAdmin() && !$usersWithPermission->contains($user->id))
                  <div class="col-md-6 mb-2">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="{{ $user->id }}" 
                             id="user_{{ $user->id }}" name="user_ids[]">
                      <label class="form-check-label" for="user_{{ $user->id }}">
                        <div class="d-flex align-items-center">
                          <div class="bg-{{ $user->role_badge_color }} text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-2"
                               style="width: 25px; height: 25px; font-size: 10px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                          </div>
                          <div>
                            <div class="fw-bold">{{ $user->name }}</div>
                            <small class="text-muted">{{ $user->email }}</small>
                          </div>
                        </div>
                      </label>
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="assignPermission()">
          <i class="fas fa-save me-1"></i>Assign Permission
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Delete Permission</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete the permission <strong>"{{ $permission->display_name }}"</strong>?</p>
        @if($usersWithPermission->count() > 0)
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Warning:</strong> This permission is currently assigned to {{ $usersWithPermission->count() }} user(s). 
            Deleting it will remove the permission from all users.
          </div>
        @endif
        <p class="text-muted">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" class="d-inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash me-1"></i>Delete Permission
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function deletePermission() {
  const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
  modal.show();
}

async function assignPermission() {
  const form = document.getElementById('assignForm');
  const formData = new FormData(form);
  const userIds = formData.getAll('user_ids[]');
  
  if (userIds.length === 0) {
    alert('Please select at least one user.');
    return;
  }

  try {
    const response = await fetch('{{ route("admin.permissions.bulk-assign") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({
        user_ids: userIds,
        permission_id: {{ $permission->id }}
      })
    });

    const result = await response.json();
    
    if (result.success) {
      alert(result.message);
      location.reload();
    } else {
      alert('Error assigning permission');
    }
  } catch (error) {
    alert('Error: ' + error.message);
  }
}

async function removePermissionFromUser(userId, permissionId, userName) {
  if (!confirm(`Remove this permission from ${userName}?`)) {
    return;
  }

  try {
    const response = await fetch('{{ route("admin.permissions.remove-from-user") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({
        user_id: userId,
        permission_id: permissionId
      })
    });

    const result = await response.json();
    
    if (result.success) {
      alert(result.message);
      location.reload();
    } else {
      alert('Error removing permission');
    }
  } catch (error) {
    alert('Error: ' + error.message);
  }
}
</script>
@endsection
