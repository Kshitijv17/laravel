@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Create New Permission</h5>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary btn-sm">
              <i class="fas fa-arrow-left me-1"></i>Back to Permissions
            </a>
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

          <form action="{{ route('admin.permissions.store') }}" method="POST">
            @csrf

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" id="name" 
                         class="form-control @error('name') is-invalid @enderror"
                         value="{{ old('name') }}" 
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
                         value="{{ old('display_name') }}" 
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
                      <option value="{{ $existingModule }}" {{ old('module') == $existingModule ? 'selected' : '' }}>
                        {{ ucfirst($existingModule) }}
                      </option>
                    @endforeach
                  @endif
                  <option value="products" {{ old('module') == 'products' ? 'selected' : '' }}>Products</option>
                  <option value="categories" {{ old('module') == 'categories' ? 'selected' : '' }}>Categories</option>
                  <option value="orders" {{ old('module') == 'orders' ? 'selected' : '' }}>Orders</option>
                  <option value="users" {{ old('module') == 'users' ? 'selected' : '' }}>Users</option>
                  <option value="reports" {{ old('module') == 'reports' ? 'selected' : '' }}>Reports</option>
                  <option value="settings" {{ old('module') == 'settings' ? 'selected' : '' }}>Settings</option>
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
                        placeholder="Describe what this permission allows users to do...">{{ old('description') }}</textarea>
              <div class="form-text">
                <i class="fas fa-info-circle me-1"></i>
                Optional description to help administrators understand this permission
              </div>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="alert alert-info">
              <i class="fas fa-lightbulb me-2"></i>
              <strong>Permission Naming Convention:</strong>
              <ul class="mb-0 mt-2">
                <li><code>module.action</code> - Standard format (e.g., products.create, users.delete)</li>
                <li><code>module.view</code> - View/read access</li>
                <li><code>module.create</code> - Create new items</li>
                <li><code>module.edit</code> - Edit existing items</li>
                <li><code>module.delete</code> - Delete items</li>
                <li><code>module.manage</code> - Full management access</li>
              </ul>
            </div>

            <div class="d-flex justify-content-between">
              <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Cancel
              </a>
              <button type="submit" class="btn btn-danger">
                <i class="fas fa-save me-1"></i>Create Permission
              </button>
            </div>
          </form>
        </div>
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

// Auto-generate display name from permission name
document.getElementById('name').addEventListener('input', function() {
  const name = this.value;
  const displayNameField = document.getElementById('display_name');
  
  if (!displayNameField.value || displayNameField.dataset.autoGenerated === 'true') {
    // Convert dot notation to readable format
    const displayName = name
      .split('.')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
    
    displayNameField.value = displayName;
    displayNameField.dataset.autoGenerated = 'true';
  }
});

// Stop auto-generation when user manually edits display name
document.getElementById('display_name').addEventListener('input', function() {
  this.dataset.autoGenerated = 'false';
});
</script>
@endsection
