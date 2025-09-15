@extends('layouts.admin')

@section('title', 'Performance Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Performance Dashboard</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Performance</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Cache Hit Rate</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $stats['cache_hits'] }}">{{ $stats['cache_hits'] }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-tachometer text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Memory Usage</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value">{{ $stats['memory_usage'] }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-memory-card text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Response Time</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value">{{ $stats['response_time'] }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-time text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Storage Usage</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value">{{ $stats['storage_usage'] }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-hdd text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cache Management -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Cache Management</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-primary w-100" onclick="clearCache('all')">
                                <i class="bx bx-trash me-1"></i> Clear All Cache
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-success w-100" onclick="warmUpCache()">
                                <i class="bx bx-refresh me-1"></i> Warm Up Cache
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-info w-100" onclick="optimizeDatabase()">
                                <i class="bx bx-data me-1"></i> Optimize Database
                            </button>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row g-2">
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="clearCache('products')">
                                Products Cache
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="clearCache('categories')">
                                Categories Cache
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="clearCache('config')">
                                Config Cache
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="clearCache('views')">
                                Views Cache
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">System Status</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Cache Size</span>
                        <span class="fw-semibold">{{ $stats['cache_size'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Database Queries</span>
                        <span class="fw-semibold">{{ $stats['database_queries'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Memory Peak</span>
                        <span class="fw-semibold">{{ $stats['memory_usage'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Tips -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Performance Optimization Tips</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-semibold">Caching Best Practices</h6>
                            <ul class="text-muted">
                                <li>Use Redis or Memcached for better performance</li>
                                <li>Cache frequently accessed data like products and categories</li>
                                <li>Set appropriate cache TTL values</li>
                                <li>Use cache tags for selective cache clearing</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold">Database Optimization</h6>
                            <ul class="text-muted">
                                <li>Add indexes on frequently queried columns</li>
                                <li>Use database query optimization</li>
                                <li>Implement database connection pooling</li>
                                <li>Regular database maintenance and cleanup</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearCache(type) {
    fetch('{{ route("admin.performance.clear-cache") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ type: type })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred while clearing cache');
    });
}

function warmUpCache() {
    fetch('{{ route("admin.performance.warm-cache") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred while warming up cache');
    });
}

function optimizeDatabase() {
    fetch('{{ route("admin.performance.optimize-db") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred while optimizing database');
    });
}

function showAlert(type, message) {
    // Implement your alert system here
    alert(message);
}

// Auto-refresh metrics every 30 seconds
setInterval(function() {
    fetch('{{ route("admin.performance.metrics") }}')
    .then(response => response.json())
    .then(data => {
        // Update metrics on page
        console.log('Metrics updated:', data);
    });
}, 30000);
</script>
@endsection
