@extends('shopkeeper.layout')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-upload me-2"></i>Bulk Product Upload</h2>
    <div>
      <a href="{{ route('shopkeeper.products.csv-template') }}" class="btn btn-info me-2">
        <i class="fas fa-download me-1"></i>Download CSV Template
      </a>
      <a href="{{ route('shopkeeper.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Products
      </a>
    </div>
  </div>

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <h6><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors:</h6>
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row">
    <!-- Instructions -->
    <div class="col-lg-4">
      <div class="card mb-4">
        <div class="card-header bg-info text-white">
          <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Instructions</h6>
        </div>
        <div class="card-body">
          <h6>How to use bulk upload:</h6>
          <ol class="small">
            <li>Download the CSV template using the button above</li>
            <li>Open the template in Excel or any spreadsheet software</li>
            <li>Fill in your product data following the format</li>
            <li>Save the file as CSV (Comma Separated Values)</li>
            <li>Upload the CSV file using the form</li>
            <li>Review the results and fix any errors if needed</li>
          </ol>

          <hr>
          <h6>Important Notes:</h6>
          <ul class="small text-muted">
            <li>Maximum file size: 2MB</li>
            <li>Supported formats: .csv, .txt</li>
            <li>First row is treated as headers and will be skipped</li>
            <li>Categories will be auto-created if they don't exist</li>
            <li>Empty rows will be automatically skipped</li>
          </ul>
        </div>
      </div>

      <!-- CSV Format Guide -->
      <div class="card">
        <div class="card-header bg-warning text-dark">
          <h6 class="mb-0"><i class="fas fa-table me-2"></i>CSV Format</h6>
        </div>
        <div class="card-body">
          <h6>Required Columns:</h6>
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <thead class="table-light">
                <tr>
                  <th>Column</th>
                  <th>Required</th>
                  <th>Example</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Title</td>
                  <td><i class="fas fa-check text-success"></i></td>
                  <td>Wireless Headphones</td>
                </tr>
                <tr>
                  <td>Description</td>
                  <td><i class="fas fa-times text-muted"></i></td>
                  <td>High-quality wireless headphones</td>
                </tr>
                <tr>
                  <td>Price</td>
                  <td><i class="fas fa-check text-success"></i></td>
                  <td>299.99</td>
                </tr>
                <tr>
                  <td>Quantity</td>
                  <td><i class="fas fa-check text-success"></i></td>
                  <td>50</td>
                </tr>
                <tr>
                  <td>Stock Status</td>
                  <td><i class="fas fa-check text-success"></i></td>
                  <td>in_stock</td>
                </tr>
                <tr>
                  <td>Status</td>
                  <td><i class="fas fa-check text-success"></i></td>
                  <td>active</td>
                </tr>
                <tr>
                  <td>Category Name</td>
                  <td><i class="fas fa-check text-success"></i></td>
                  <td>Electronics</td>
                </tr>
              </tbody>
            </table>
          </div>

          <h6 class="mt-3">Optional Columns:</h6>
          <ul class="small">
            <li>Features - Product features (comma-separated)</li>
            <li>Specifications - Technical specs</li>
            <li>Selling Price - Discounted price</li>
            <li>Discount Tag - e.g., "20% OFF"</li>
            <li>Discount Color - Hex color code</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Upload Form -->
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="fas fa-file-upload me-2"></i>Upload CSV File</h5>
        </div>
        <div class="card-body">
          <form action="{{ route('shopkeeper.products.bulk-upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
              <label for="csv_file" class="form-label fw-bold">Select CSV File <span class="text-danger">*</span></label>
              <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv,.txt" required>
              <div class="form-text">
                Choose a CSV file containing your product data. Download the template if you need help with the format.
              </div>
            </div>

            <div class="mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="skip_duplicates" name="skip_duplicates" checked>
                <label class="form-check-label" for="skip_duplicates">
                  Skip duplicate products (based on title)
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="create_categories" name="create_categories" checked>
                <label class="form-check-label" for="create_categories">
                  Auto-create categories if they don't exist
                </label>
              </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <a href="{{ route('shopkeeper.products.csv-template') }}" class="btn btn-outline-info">
                <i class="fas fa-download me-1"></i>Download Template
              </a>
              <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-upload me-2"></i>Upload Products
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Upload History/Results -->
      @if(session('bulk_errors'))
        <div class="card mt-4">
          <div class="card-header bg-danger text-white">
            <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Upload Results</h6>
          </div>
          <div class="card-body">
            @if(session('success'))
              <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
              </div>
            @endif

            <h6>Errors Found:</h6>
            <div class="table-responsive">
              <table class="table table-sm table-bordered">
                <thead class="table-light">
                  <tr>
                    <th>Row</th>
                    <th>Error Message</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach(session('bulk_errors') as $error)
                    <tr>
                      <td class="text-danger fw-bold">{{ explode(': ', $error)[0] }}</td>
                      <td>{{ substr($error, strpos($error, ': ') + 2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="mt-3">
              <a href="{{ route('shopkeeper.products.bulk-upload-form') }}" class="btn btn-warning">
                <i class="fas fa-redo me-1"></i>Try Again
              </a>
            </div>
          </div>
        </div>
      @endif

      <!-- Recent Uploads -->
      <div class="card mt-4">
        <div class="card-header bg-success text-white">
          <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Uploads</h6>
        </div>
        <div class="card-body">
          <p class="text-muted small mb-0">
            Your recent bulk uploads will appear here with success/failure statistics.
          </p>
          <!-- This could be enhanced later with a database table to track uploads -->
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.card-header {
  border-bottom: 2px solid rgba(0,0,0,0.125);
}

.form-check-label {
  cursor: pointer;
}

.table th {
  font-size: 0.875em;
  font-weight: 600;
}

.table td {
  font-size: 0.875em;
}
</style>

<script>
// File validation
document.getElementById('csv_file').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    // Check file size (2MB limit)
    if (file.size > 2 * 1024 * 1024) {
      alert('File size must be less than 2MB');
      e.target.value = '';
      return;
    }

    // Check file extension
    const allowedExtensions = ['csv', 'txt'];
    const fileExtension = file.name.split('.').pop().toLowerCase();
    if (!allowedExtensions.includes(fileExtension)) {
      alert('Please select a CSV or TXT file');
      e.target.value = '';
      return;
    }
  }
});

// Form submission feedback
document.querySelector('form').addEventListener('submit', function(e) {
  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;

  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
  submitBtn.disabled = true;

  // Re-enable after 30 seconds as fallback
  setTimeout(() => {
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
  }, 30000);
});
</script>
@endsection
