@extends('admin.layout')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-eye me-2"></i>Product Details</h2>
    <div>
      <a href="{{ route('shopkeeper.products.edit', $product) }}" class="btn btn-warning me-2">
        <i class="fas fa-edit me-1"></i>Edit Product
      </a>
      <a href="{{ route('shopkeeper.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Products
      </a>
    </div>
  </div>

  <div class="row">
    <!-- Product Images Section -->
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-images me-2"></i>Product Images</h5>
        </div>
        <div class="card-body">
          @if($product->image)
            <div class="mb-4">
              <h6 class="text-primary mb-3"><i class="fas fa-star me-1"></i>Main Image:</h6>
              <div class="text-center">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" class="img-fluid rounded shadow cursor-pointer" style="max-height: 300px;" onclick="openImageModal('{{ asset('storage/' . $product->image) }}', 'Main Product Image')">
              </div>
            </div>
          @endif

          @if($product->images->count() > 0)
            <div class="mt-4 border rounded p-3 bg-light">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-success mb-0">
                  <i class="fas fa-images me-2"></i>
                  Additional Images
                </h5>
                <span class="badge bg-success fs-6">{{ $product->images->count() }} images</span>
              </div>
              <div class="row g-3">
                @foreach($product->images as $image)
                  <div class="col-6 col-md-4 col-lg-3">
                    <div class="position-relative">
                      <img src="{{ asset('storage/' . $image->image_path) }}" alt="Additional product image {{ $loop->iteration }}" class="img-fluid rounded shadow cursor-pointer" style="height: 150px; width: 100%; object-fit: cover;" onclick="openImageModal('{{ asset('storage/' . $image->image_path) }}', 'Additional Image {{ $loop->iteration }}')">
                      <div class="position-absolute top-0 end-0 bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 14px; margin: 8px; border: 2px solid white;">
                        {{ $loop->iteration }}
                      </div>
                      <div class="position-absolute bottom-0 start-0 bg-dark bg-opacity-75 text-white px-2 py-1 rounded" style="font-size: 11px;">
                        <i class="fas fa-expand-arrows-alt me-1"></i>Click to view
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="mt-3 text-center">
                <small class="text-muted">
                  <i class="fas fa-info-circle me-1"></i>
                  Click on any image to view it in full size
                </small>
              </div>
            </div>
          @endif

          @if(!$product->image && $product->images->count() == 0)
            <div class="text-center text-muted py-5">
              <i class="fas fa-image fa-4x mb-3 text-secondary"></i>
              <h5>No Images Available</h5>
              <p>This product doesn't have any uploaded images.</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Product Information Section -->
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Product Information</h5>
        </div>
        <div class="card-body">
          <table class="table table-borderless">
            <tr>
              <th width="35%">Title:</th>
              <td class="fw-bold">{{ $product->title }}</td>
            </tr>
            <tr>
              <th>Description:</th>
              <td>
                @if($product->description)
                  {!! nl2br(e($product->description)) !!}
                @else
                  <span class="text-muted">No description</span>
                @endif
              </td>
            </tr>
            <tr>
              <th>Original Price:</th>
              <td class="h5 text-primary fw-bold">₹{{ number_format($product->price, 2) }}</td>
            </tr>
            @if($product->selling_price)
            <tr>
              <th>Selling Price:</th>
              <td class="h5 text-success fw-bold">₹{{ number_format($product->selling_price, 2) }}</td>
            </tr>
            @endif
            <tr>
              <th>Stock Quantity:</th>
              <td>
                <span class="badge {{ $product->quantity > 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                  {{ $product->quantity }} units
                </span>
              </td>
            </tr>
            <tr>
              <th>Stock Status:</th>
              <td>
                <span class="badge {{ $product->stock_status === 'in_stock' ? 'bg-success' : 'bg-danger' }} fs-6">
                  {{ $product->stock_status === 'in_stock' ? 'In Stock' : 'Out of Stock' }}
                </span>
              </td>
            </tr>
            <tr>
              <th>Product Status:</th>
              <td>
                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }} fs-6">
                  <i class="fas {{ $product->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                  {{ $product->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
            </tr>
            <tr>
              <th>Category:</th>
              <td>
                @if($product->category)
                  <span class="badge bg-primary">{{ $product->category->title }}</span>
                @else
                  <span class="text-muted">No category</span>
                @endif
              </td>
            </tr>
            <tr>
              <th>Created:</th>
              <td>{{ $product->created_at->format('M d, Y \a\t h:i A') }}</td>
            </tr>
            <tr>
              <th>Updated:</th>
              <td>{{ $product->updated_at->format('M d, Y \a\t h:i A') }}</td>
            </tr>
          </table>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
        </div>
        <div class="card-body d-flex gap-2">
          <a href="{{ route('shopkeeper.products.edit', $product) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i>Edit
          </a>
          <button class="btn btn-danger" onclick="confirmDelete()">
            <i class="fas fa-trash me-1"></i>Delete
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Features Section -->
  @if($product->features)
  <div class="card mt-4">
    <div class="card-header bg-success text-white">
      <h5 class="mb-0"><i class="fas fa-star me-2"></i>Product Features</h5>
    </div>
    <div class="card-body">
      <div class="features-content">
        {!! $product->features !!}
      </div>
    </div>
  </div>
  @endif

  <!-- Specifications Section -->
  @if($product->specifications)
  <div class="card mt-4">
    <div class="card-header bg-info text-white">
      <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Technical Specifications</h5>
    </div>
    <div class="card-body">
      <div class="specifications-content">
        {!! $product->specifications !!}
      </div>
    </div>
  </div>
  @endif

  <!-- Discount Information -->
  @if($product->discount_tag)
  <div class="card mt-4">
    <div class="card-header bg-warning text-dark">
      <h5 class="mb-0"><i class="fas fa-tag me-2"></i>Discount Information</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <h6>Discount Tag:</h6>
          <div class="d-flex align-items-center">
            <span class="badge fs-6 px-3 py-2 me-3" style="background-color: {{ $product->discount_color }}; color: white; border: 2px solid #000;">
              {{ $product->discount_tag }}
            </span>
            <small class="text-muted">Color: {{ $product->discount_color }}</small>
          </div>
        </div>
        <div class="col-md-6">
          @if($product->selling_price)
          <h6>Pricing Comparison:</h6>
          <div class="pricing-comparison">
            <div class="text-decoration-line-through text-muted">₹{{ number_format($product->price, 2) }}</div>
            <div class="text-success fw-bold fs-5">₹{{ number_format($product->selling_price, 2) }}</div>
            <div class="text-success">
              <small>
                Save ₹{{ number_format($product->price - $product->selling_price, 2) }}
                ({{ number_format((($product->price - $product->selling_price) / $product->price) * 100, 1) }}% off)
              </small>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- All Product Images Gallery -->
  @if($product->images->count() > 0)
    <div class="card mt-4">
      <div class="card-header d-flex justify-content-between align-items-center bg-success text-white">
        <h5 class="mb-0">
          <i class="fas fa-photo-video me-2"></i>
          All Product Images Gallery
        </h5>
        <div>
          <span class="badge bg-light text-dark me-2">{{ $product->images->count() }} additional</span>
          @if($product->image)
            <span class="badge bg-primary">+ 1 main</span>
          @endif
        </div>
      </div>
      <div class="card-body">
        <div class="row g-4">
          <!-- Main Image -->
          @if($product->image)
            <div class="col-12 mb-4">
              <div class="text-center">
                <h6 class="text-primary mb-3">
                  <i class="fas fa-star me-1"></i>
                  Main Product Image
                </h6>
                <img src="{{ asset('storage/' . $product->image) }}" alt="Main product image" class="img-fluid rounded shadow cursor-pointer" style="max-height: 250px;" onclick="openImageModal('{{ asset('storage/' . $product->image) }}', 'Main Product Image')">
              </div>
            </div>
          @endif

          <!-- Additional Images -->
          <div class="col-12">
            <h6 class="text-success mb-3">
              <i class="fas fa-images me-1"></i>
              Additional Product Images ({{ $product->images->count() }})
            </h6>
            <div class="row g-3">
              @foreach($product->images as $image)
                <div class="col-6 col-md-4 col-lg-3">
                  <div class="text-center">
                    <div class="position-relative d-inline-block">
                      <img src="{{ asset('storage/' . $image->image_path) }}" alt="Additional product image {{ $loop->iteration }}" class="img-fluid rounded shadow cursor-pointer mb-2" style="height: 120px; width: 100%; object-fit: cover;" onclick="openImageModal('{{ asset('storage/' . $image->image_path) }}', 'Additional Image {{ $loop->iteration }}')">
                      <div class="position-absolute top-0 end-0 bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 25px; height: 25px; font-size: 12px;">
                        {{ $loop->iteration }}
                      </div>
                    </div>
                    <small class="text-muted d-block">Image {{ $loop->iteration }}</small>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalImage" src="" alt="" class="img-fluid rounded shadow" style="max-height: 70vh;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <a id="downloadBtn" href="" download class="btn btn-primary">
          <i class="fas fa-download me-1"></i>Download
        </a>
      </div>
    </div>
  </div>
</div>

<style>
.cursor-pointer {
  cursor: pointer;
  transition: transform 0.2s;
}
.cursor-pointer:hover {
  transform: scale(1.05);
}

.features-content, .specifications-content {
  font-family: Arial, sans-serif;
  line-height: 1.6;
}

.pricing-comparison {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  border-left: 4px solid #28a745;
}

.card-header {
  border-bottom: 2px solid rgba(0,0,0,0.125);
}
</style>

<script>
function openImageModal(imageSrc, title) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalImage').alt = title;
    document.getElementById('imageModalLabel').textContent = title;
    document.getElementById('downloadBtn').href = imageSrc;

    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

function confirmDelete() {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("shopkeeper.products.destroy", $product) }}';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
