@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-key me-2"></i>Permission Management</h2>
        <div>
          <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
            <i class="fas fa-users me-1"></i>Bulk Assign
          </button>
          <a href="{{ route('admin.permissions.create') }}" class="btn btn-danger">
            <i class="fas fa-plus me-1"></i>Add Permission
          </a>
        </div>
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
        <div class="card-header">
          <h5 class="mb-0">Admin Users & Permissions</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-dark">
                <tr>
                  <th width="20%">User</th>
                  <th width="15%">Role</th>
                  @foreach($permissions as $module => $modulePermissions)
                    <th width="{{ 65 / $permissions->count() }}%" class="text-center">
                      {{ ucfirst($module) }}
                      <br><small class="text-muted">{{ $modulePermissions->count() }} permissions</small>
                    </th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach($admins as $admin)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="bg-{{ $admin->role_badge_color }} text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3"
                             style="width: 40px; height: 40px; font-size: 16px;">
                          {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </div>
                        <div>
                          <div class="fw-bold">{{ $admin->name }}</div>
                          <small class="text-muted">{{ $admin->email }}</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="badge bg-{{ $admin->role_badge_color }}">
                        {{ $admin->role_display }}
                      </span>
                    </td>

                    @foreach($permissions as $module => $modulePermissions)
                      <td class="text-center">
                        @if($admin->isSuperAdmin())
                          <span class="badge bg-success">
                            <i class="fas fa-crown"></i> All
                          </span>
                        @else
                          <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="selectAllModule('{{ $admin->id }}', '{{ $module }}')">
                              All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="clearModule('{{ $admin->id }}', '{{ $module }}')">
                              None
                            </button>
                          </div>
                          <div class="mt-2">
                            @foreach($modulePermissions as $permission)
                              <div class="form-check form-check-inline">
                                <input class="form-check-input permission-checkbox"
                                       type="checkbox"
                                       id="perm_{{ $admin->id }}_{{ $permission->name }}"
                                       data-user="{{ $admin->id }}"
                                       data-permission="{{ $permission->name }}"
                                       {{ $admin->hasPermission($permission->name) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="perm_{{ $admin->id }}_{{ $permission->name }}">
                                  {{ $permission->display_name }}
                                </label>
                              </div>
                            @endforeach
                          </div>
                        @endif
                      </td>
                    @endforeach
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Permission Information</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <h6>Role Hierarchy:</h6>
                <ul class="list-unstyled">
                  <li><span class="badge bg-danger">Super Admin</span> - Full system access</li>
                  <li><span class="badge bg-warning">Admin</span> - Standard admin access</li>
                  <li><span class="badge bg-primary">Customer</span> - Regular user access</li>
                  <li><span class="badge bg-secondary">Guest</span> - Limited access</li>
                </ul>
              </div>
              <div class="col-md-6">
                <h6>Permission Modules:</h6>
                <ul class="list-unstyled">
                  @foreach($permissions as $module => $modulePermissions)
                    <li><strong>{{ ucfirst($module) }}:</strong> {{ $modulePermissions->count() }} permissions</li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function selectAllModule(userId, module) {
  const checkboxes = document.querySelectorAll(`input[data-user="${userId}"][id*="perm_${userId}_${module}"]`);
  checkboxes.forEach(checkbox => {
    checkbox.checked = true;
  });
}

function clearModule(userId, module) {
  const checkboxes = document.querySelectorAll(`input[data-user="${userId}"][id*="perm_${userId}_"]`);
  checkboxes.forEach(checkbox => {
    if (checkbox.id.includes(module)) {
      checkbox.checked = false;
    }
  });
}

async function bulkUpdatePermissions() {
  const updates = [];
  const checkboxes = document.querySelectorAll('.permission-checkbox');

  // Group permissions by user
  const userPermissions = {};
  checkboxes.forEach(checkbox => {
    const userId = checkbox.dataset.user;
    const permission = checkbox.dataset.permission;

    if (!userPermissions[userId]) {
      userPermissions[userId] = [];
    }

    if (checkbox.checked) {
      userPermissions[userId].push(permission);
    }
  });

  // Send bulk updates
  try {
    for (const [userId, permissions] of Object.entries(userPermissions)) {
      const response = await fetch('{{ route("admin.permissions.bulk-update") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          user_id: userId,
          permissions: permissions
        })
      });

      if (!response.ok) {
        throw new Error('Failed to update permissions');
      }
    }

    // Show success message
    alert('All permissions updated successfully!');
    location.reload();
  } catch (error) {
    alert('Error updating permissions: ' + error.message);
  }
}

// Auto-save individual permission changes
document.addEventListener('change', async function(e) {
  if (e.target.classList.contains('permission-checkbox')) {
    const userId = e.target.dataset.user;
    const permission = e.target.dataset.permission;
    const isChecked = e.target.checked;

    try {
      const permissions = [];
      const userCheckboxes = document.querySelectorAll(`input[data-user="${userId}"]`);
      userCheckboxes.forEach(cb => {
        if (cb.checked) {
          permissions.push(cb.dataset.permission);
        }
      });

      const response = await fetch('{{ route("admin.permissions.bulk-update") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          user_id: userId,
          permissions: permissions
        })
      });

      if (response.ok) {
        // Visual feedback
        e.target.parentElement.style.opacity = '0.5';
        setTimeout(() => {
          e.target.parentElement.style.opacity = '1';
        }, 300);
      }
    } catch (error) {
      console.error('Error updating permission:', error);
      // Revert checkbox state on error
      e.target.checked = !isChecked;
    }
  }
});

// Delete permission function
async function deletePermission(permissionId, permissionName) {
  if (!confirm(`Are you sure you want to delete the permission "${permissionName}"?\n\nThis action cannot be undone and will remove the permission from all users.`)) {
    return;
  }

  try {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/permissions/${permissionId}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
  } catch (error) {
    alert('Error deleting permission: ' + error.message);
  }
}

// View user permissions function
function viewUserPermissions(userId, userName) {
  // You could implement a modal here or redirect to user permissions page
  window.location.href = `/admin/permissions/users/${userId}`;
}
</script>

<!-- Add required meta tag for CSRF -->
@if(!isset($__env->getShared()['__csrf_token']))
<meta name="csrf-token" content="{{ csrf_token() }}">
@endif

@endsection
