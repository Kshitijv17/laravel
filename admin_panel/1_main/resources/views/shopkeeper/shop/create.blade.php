<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Setup Your Shop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
    }
    .setup-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      overflow: hidden;
    }
    .setup-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 2rem;
      text-align: center;
    }
    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    }
  </style>
</head>
<body>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
      <div class="setup-card">
        <div class="setup-header">
          <i class="fas fa-store fa-3x mb-3"></i>
          <h2 class="mb-2">Setup Your Shop</h2>
          <p class="mb-0 opacity-75">Welcome {{ auth()->user()->name }}! Let's create your online shop to start selling.</p>
        </div>
        
        <div class="p-4">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li><i class="fas fa-exclamation-triangle me-1"></i>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('shopkeeper.shop.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
              <!-- Basic Information -->
              <div class="col-md-6">
                <h5 class="mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Basic Information</h5>
                
                <div class="mb-3">
                  <label for="name" class="form-label">Shop Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                         value="{{ old('name') }}" required placeholder="Enter your shop name">
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="description" class="form-label">Shop Description</label>
                  <textarea name="description" id="description" rows="4" 
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Describe what your shop sells...">{{ old('description') }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="address" class="form-label">Shop Address</label>
                  <textarea name="address" id="address" rows="3" 
                            class="form-control @error('address') is-invalid @enderror"
                            placeholder="Enter your shop address...">{{ old('address') }}</textarea>
                  @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <!-- Contact & Media -->
              <div class="col-md-6">
                <h5 class="mb-3"><i class="fas fa-phone me-2 text-primary"></i>Contact Information</h5>
                
                <div class="mb-3">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                         value="{{ old('phone') }}" placeholder="Enter phone number">
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="email" class="form-label">Shop Email</label>
                  <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                         value="{{ old('email') }}" placeholder="shop@example.com">
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="website" class="form-label">Website URL</label>
                  <input type="url" name="website" id="website" class="form-control @error('website') is-invalid @enderror" 
                         value="{{ old('website') }}" placeholder="https://yourwebsite.com">
                  @error('website')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <h5 class="mb-3 mt-4"><i class="fas fa-images me-2 text-primary"></i>Shop Images</h5>
                
                <div class="mb-3">
                  <label for="logo" class="form-label">Shop Logo</label>
                  <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" 
                         accept="image/*">
                  <div class="form-text">Upload a square logo (recommended: 200x200px)</div>
                  @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="banner" class="form-label">Shop Banner</label>
                  <input type="file" name="banner" id="banner" class="form-control @error('banner') is-invalid @enderror" 
                         accept="image/*">
                  <div class="form-text">Upload a banner image (recommended: 1200x400px)</div>
                  @error('banner')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4 pt-3 border-top">
              <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-store me-2"></i>Create My Shop
              </button>
              <div class="mt-2">
                <small class="text-muted">You can update these details later from your dashboard</small>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Preview uploaded images
document.getElementById('logo').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      // You can add image preview functionality here
      console.log('Logo selected:', file.name);
    };
    reader.readAsDataURL(file);
  }
});

document.getElementById('banner').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      // You can add image preview functionality here
      console.log('Banner selected:', file.name);
    };
    reader.readAsDataURL(file);
  }
});
</script>

</body>
</html>
