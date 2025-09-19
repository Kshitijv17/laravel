@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
              <i class="fas fa-user-cog me-2"></i>Manage Permissions for {{ $user->name }}
            </h5>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary btn-sm">
              <i class="fas fa-arrow-left me-1"></i>Back to Permissions
            </a>
          </div>
        </div>

        <div class="card-body">
          <!-- User Info -->
          <div class="row mb-4">
            <div class="col-md-12">
              <div class="d-flex align-items-center p-3 bg-light rounded">
                <div class="bg-{{ $user->role_badge_color }} text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3"
                     style="width: 60px; height: 60px; font-size: 24px;">
                  {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">{{ $user->name }}</h5>
                  <p class="text-muted mb-1">{{ $user->email }}</p>
                  <span class="badge bg-{{ $user->role_badge_color }}">
                    @if($user->isSuperAdmin())
                      <i class="fas fa-crown me-1"></i>Super Admin
                    @else
                      <i class="fas fa-user-shield me-1"></i>Admin
                    @endif
                  </span>
                  @if($user->id === auth()->user()->id)
                    <span class="badge bg-info ms-2">You</span>
                  @endif
                </div>
                <div>
                  <div class="text-end">
                    <h6 class="mb-0">{{ $user->permissions->count() }}</h6>
                    <small class="text-muted">Permissions</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          @if($user->isSuperAdmin())
            <div class="alert alert-info">
              <i class="fas fa-crown me-2"></i>
              <strong>Super Admin Access:</strong> This user has full access to all system features and permissions. 
              Super Admins automatically have all permissions and cannot have their permissions modified.
            </div>
          @else
            <form action="{{ route('admin.permissions.user.update', $user) }}" method="POST" id="permissionsForm">
              @csrf
              @method('PUT')

              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Assign Permissions</h6>
                <div>
                  <button type="button" class="btn btn-sm btn-outline-success me-2" onclick="selectAllPermissions()">
                    <i class="fas fa-check-double me-1"></i>Select All
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllPermissions()">
                    <i class="fas fa-times me-1"></i>Clear All
                  </button>
                </div>
              </div>

              @foreach($permissions as $module => $modulePermissions)
                <div class="card mb-3">
                  <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                      <h6 class="mb-0">
                        <i class="fas fa-folder me-2"></i>{{ ucfirst($module) }} Module
                        <span class="badge bg-secondary ms-2">{{ $modulePermissions->count() }} permissions</span>
                      </h6>
                      <div>
                        <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                onclick="selectModulePermissions('{{ $module }}')">
                          <i class="fas fa-check me-1"></i>All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="clearModulePermissions('{{ $module }}')">
                          <i class="fas fa-times me-1"></i>None
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      @foreach($modulePermissions as $permission)
                        <div class="col-md-6 mb-3">
                          <div class="form-check">
                            <input class="form-check-input permission-checkbox" 
                                   type="checkbox" 
                                   name="permissions[]" 
                                   value="{{ $permission->name }}"
                                   id="perm_{{ $permission->id }}"
                                   data-module="{{ $module }}"
                                   {{ in_array($permission->name, $userPermissions) ? 'checked' : '' }}>
                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                              <div class="d-flex justify-content-between align-items-start">
                                <div>
                                  <strong>{{ $permission->display_name }}</strong>
                                  @if($permission->description)
                                    <br><small class="text-muted">{{ $permission->description }}</small>
                                  @endif
                                  <br><code class="small">{{ $permission->name }}</code>
                                </div>
                              </div>
                            </label>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              @endforeach

              <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                  <span class="text-muted">
                    <span id="selectedCount">{{ count($userPermissions) }}</span> permissions selected
                  </span>
                </div>
                <div>
                  <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i>Cancel
                  </a>
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Update Permissions
                  </button>
                </div>
              </div>
            </form>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Permission Summary</h6>
        </div>
        <div class="card-body">
          @if($user->isSuperAdmin())
            <div class="text-center">
              <i class="fas fa-crown fa-3x text-danger mb-3"></i>
              <h5>Super Admin</h5>
              <p class="text-muted">Full system access</p>
            </div>
          @else
            <div class="row text-center">
              @foreach($permissions as $module => $modulePermissions)
                @php
                  $userModulePermissions = $modulePermissions->filter(function($perm) use ($userPermissions) {
                    return in_array($perm->name, $userPermissions);
                  });
                  $percentage = $modulePermissions->count() > 0 ? round(($userModulePermissions->count() / $modulePermissions->count()) * 100) : 0;
                @endphp
                <div class="col-6 mb-3">
                  <div class="card bg-light">
                    <div class="card-body p-3">
                      <h6 class="card-title">{{ ucfirst($module) }}</h6>
                      <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%"></div>
                      </div>
                      <small class="text-muted">
                        {{ $userModulePermissions->count() }}/{{ $modulePermissions->count() }} 
                        ({{ $percentage }}%)
                      </small>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header">
          <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>User Information</h6>
        </div>
        <div class="card-body">
          <table class="table table-sm">
            <tr>
              <td><strong>ID:</strong></td>
              <td>{{ $user->id }}</td>
            </tr>
            <tr>
              <td><strong>Role:</strong></td>
              <td><span class="badge bg-{{ $user->role_badge_color }}">{{ $user->role_display }}</span></td>
            </tr>
            <tr>
              <td><strong>Email:</strong></td>
              <td>{{ $user->email }}</td>
            </tr>
            <tr>
              <td><strong>Joined:</strong></td>
              <td>{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</td>
            </tr>
            <tr>
              <td><strong>Permissions:</strong></td>
              <td>
                @if($user->isSuperAdmin())
                  <span class="badge bg-success">All</span>
                @else
                  <span class="badge bg-info">{{ $user->permissions->count() }}</span>
                @endif
              </td>
            </tr>
          </table>
        </div>
      </div>

      @if(!$user->isSuperAdmin())
        <div class="card mt-4">
          <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-magic me-2"></i>Quick Actions</h6>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <button class="btn btn-outline-success btn-sm" onclick="selectAllPermissions()">
                <i class="fas fa-check-double me-2"></i>Grant All Permissions
              </button>
              <button class="btn btn-outline-warning btn-sm" onclick="selectCommonPermissions()">
                <i class="fas fa-star me-2"></i>Common Permissions
              </button>
              <button class="btn btn-outline-secondary btn-sm" onclick="clearAllPermissions()">
                <i class="fas fa-times me-2"></i>Remove All Permissions
              </button>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<script>
function selectAllPermissions() {
  const checkboxes = document.querySelectorAll('.permission-checkbox');
  checkboxes.forEach(checkbox => checkbox.checked = true);
  updateSelectedCount();
}

function clearAllPermissions() {
  const checkboxes = document.querySelectorAll('.permission-checkbox');
  checkboxes.forEach(checkbox => checkbox.checked = false);
  updateSelectedCount();
}

function selectModulePermissions(module) {
  const checkboxes = document.querySelectorAll(`input[data-module="${module}"]`);
  checkboxes.forEach(checkbox => checkbox.checked = true);
  updateSelectedCount();
}

function clearModulePermissions(module) {
  const checkboxes = document.querySelectorAll(`input[data-module="${module}"]`);
  checkboxes.forEach(checkbox => checkbox.checked = false);
  updateSelectedCount();
}

function selectCommonPermissions() {
  // Clear all first
  clearAllPermissions();
  
  // Select common permissions
  const commonPermissions = ['products.view', 'products.create', 'products.edit', 'categories.view', 'orders.view'];
  commonPermissions.forEach(permission => {
    const checkbox = document.querySelector(`input[value="${permission}"]`);
    if (checkbox) checkbox.checked = true;
  });
  updateSelectedCount();
}

function updateSelectedCount() {
  const checkedBoxes = document.querySelectorAll('.permission-checkbox:checked');
  document.getElementById('selectedCount').textContent = checkedBoxes.length;
}

// Update count when checkboxes change
document.addEventListener('change', function(e) {
  if (e.target.classList.contains('permission-checkbox')) {
    updateSelectedCount();
  }
});

// Initialize count
document.addEventListener('DOMContentLoaded', function() {
  updateSelectedCount();
});
</script>
@endsection
