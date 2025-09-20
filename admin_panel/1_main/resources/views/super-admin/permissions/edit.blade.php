@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Permission</h5>
            <div>
              <a href="{{ route('admin.permissions.show', $permission->id) }}" class="btn btn-info btn-sm me-2">
                <i class="fas fa-eye me-1"></i>View Details
              </a>
              <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Back to Permissions
              </a>
            </div>
          </div>
        </div>

        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li><i class="fas fa-exclamation-triangle me-1"></i>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" id="name" 
                         class="form-control @error('name') is-invalid @enderror"
                         value="{{ old('name', $permission->name) }}" 
                         placeholder="e.g., products.create"
                         required>
                  <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Use dot notation (module.action). Example: products.create, users.edit
                  </div>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                  <input type="text" name="display_name" id="display_name" 
                         class="form-control @error('display_name') is-invalid @enderror"
                         value="{{ old('display_name', $permission->display_name) }}" 
                         placeholder="e.g., Create Products"
                         required>
                  <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Human-readable name shown in the interface
                  </div>
                  @error('display_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="module" class="form-label">Module <span class="text-danger">*</span></label>
              <div class="input-group">
                <select name="module" id="module" class="form-control @error('module') is-invalid @enderror" required>
                  <option value="">Select Module</option>
                  @if(!empty($modules))
                    @foreach($modules as $existingModule)
                      <option value="{{ $existingModule }}" {{ old('module', $permission->module) == $existingModule ? 'selected' : '' }}>
                        {{ ucfirst($existingModule) }}
                      </option>
                    @endforeach
                  @endif
                  <option value="products" {{ old('module', $permission->module) == 'products' ? 'selected' : '' }}>Products</option>
                  <option value="categories" {{ old('module', $permission->module) == 'categories' ? 'selected' : '' }}>Categories</option>
                  <option value="orders" {{ old('module', $permission->module) == 'orders' ? 'selected' : '' }}>Orders</option>
                  <option value="users" {{ old('module', $permission->module) == 'users' ? 'selected' : '' }}>Users</option>
                  <option value="reports" {{ old('module', $permission->module) == 'reports' ? 'selected' : '' }}>Reports</option>
                  <option value="settings" {{ old('module', $permission->module) == 'settings' ? 'selected' : '' }}>Settings</option>
                </select>
                <button class="btn btn-outline-secondary" type="button" onclick="toggleCustomModule()">
                  <i class="fas fa-plus"></i> Custom
                </button>
              </div>
              <input type="text" id="custom_module" class="form-control mt-2 d-none" 
                     placeholder="Enter custom module name">
              <div class="form-text">
                <i class="fas fa-info-circle me-1"></i>
                Group related permissions together
              </div>
              @error('module')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-4">
              <label for="description" class="form-label">Description</label>
              <textarea name="description" id="description" rows="3" 
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Describe what this permission allows users to do...">{{ old('description', $permission->description) }}</textarea>
              <div class="form-text">
                <i class="fas fa-info-circle me-1"></i>
                Optional description to help administrators understand this permission
              </div>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="alert alert-warning">
              <i class="fas fa-exclamation-triangle me-2"></i>
              <strong>Warning:</strong> Changing the permission name will affect all users who currently have this permission assigned. 
              Make sure to update any hardcoded permission checks in your application code.
            </div>

            <div class="card mb-4">
              <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-users me-2"></i>Users with this Permission</h6>
              </div>
              <div class="card-body">
                @if($permission->users->count() > 0)
                  <div class="row">
                    @foreach($permission->users as $user)
                      <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                          <div class="bg-{{ $user->role_badge_color }} text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-2"
                               style="width: 30px; height: 30px; font-size: 12px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                          </div>
                          <div>
                            <div class="fw-bold">{{ $user->name }}</div>
                            <small class="text-muted">{{ $user->email }}</small>
                          </div>
                          <span class="badge bg-{{ $user->role_badge_color }} ms-auto">{{ $user->role_display }}</span>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div class="text-center text-muted">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <p>No users currently have this permission assigned.</p>
                  </div>
                @endif
              </div>
            </div>

            <div class="d-flex justify-content-between">
              <div>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                  <i class="fas fa-times me-1"></i>Cancel
                </a>
                <button type="button" class="btn btn-danger ms-2" onclick="deletePermission()">
                  <i class="fas fa-trash me-1"></i>Delete Permission
                </button>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Update Permission
              </button>
            </div>
          </form>
        </div>
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
        @if($permission->users->count() > 0)
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Warning:</strong> This permission is currently assigned to {{ $permission->users->count() }} user(s). 
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
function toggleCustomModule() {
  const select = document.getElementById('module');
  const customInput = document.getElementById('custom_module');
  
  if (customInput.classList.contains('d-none')) {
    customInput.classList.remove('d-none');
    customInput.focus();
    customInput.addEventListener('input', function() {
      select.value = this.value;
    });
  } else {
    customInput.classList.add('d-none');
    customInput.value = '';
  }
}

function deletePermission() {
  const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
  modal.show();
}
</script>
@endsection
