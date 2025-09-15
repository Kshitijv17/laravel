@extends('layouts.admin')

@section('title', 'Inventory Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Inventory Management</h3>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#stockAdjustmentModal">
                            <i class="fas fa-plus"></i> Stock Adjustment
                        </button>
                        <button type="button" class="btn btn-success" onclick="generateReport()">
                            <i class="fas fa-chart-bar"></i> Generate Report
                        </button>
                        <button type="button" class="btn btn-warning" onclick="checkLowStock()">
                            <i class="fas fa-exclamation-triangle"></i> Low Stock Alert
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="categoryFilter">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="stockFilter">
                                <option value="">All Stock Levels</option>
                                <option value="in_stock">In Stock</option>
                                <option value="low_stock">Low Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchProduct" placeholder="Search products...">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                <i class="fas fa-refresh"></i> Reset
                            </button>
                        </div>
                    </div>

                    <!-- Inventory Table -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Reserved</th>
                                    <th>Available</th>
                                    <th>Reorder Level</th>
                                    <th>Status</th>
                                    <th>Last Movement</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->photo)
                                                <img src="{{ $product->photo }}" alt="{{ $product->title }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <strong>{{ $product->title }}</strong>
                                                <br><small class="text-muted">{{ Str::limit($product->summary, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->sku ?? 'N/A' }}</td>
                                    <td>{{ $product->cat_info->title ?? 'Uncategorized' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $product->stock ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $product->reserved_stock ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ ($product->stock ?? 0) - ($product->reserved_stock ?? 0) }}</span>
                                    </td>
                                    <td>{{ $product->reorder_level ?? 10 }}</td>
                                    <td>
                                        @php
                                            $available = ($product->stock ?? 0) - ($product->reserved_stock ?? 0);
                                            $reorderLevel = $product->reorder_level ?? 10;
                                        @endphp
                                        @if($available <= 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($available <= $reorderLevel)
                                            <span class="badge bg-warning">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">In Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->last_movement)
                                            <small>{{ $product->last_movement->created_at->diffForHumans() }}</small>
                                            <br><small class="text-muted">{{ $product->last_movement->type }}</small>
                                        @else
                                            <small class="text-muted">No movements</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" onclick="viewMovements({{ $product->id }})">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success" onclick="adjustStock({{ $product->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info" onclick="viewForecast({{ $product->id }})">
                                                <i class="fas fa-chart-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_products'] ?? 0 }}</h4>
                            <p class="mb-0">Total Products</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['in_stock'] ?? 0 }}</h4>
                            <p class="mb-0">In Stock</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['low_stock'] ?? 0 }}</h4>
                            <p class="mb-0">Low Stock</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['out_of_stock'] ?? 0 }}</h4>
                            <p class="mb-0">Out of Stock</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockAdjustmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stock Adjustment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockAdjustmentForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <select class="form-select" name="product_id" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->title }} (Current: {{ $product->stock ?? 0 }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adjustment Type</label>
                        <select class="form-select" name="type" required>
                            <option value="adjustment_increase">Increase Stock</option>
                            <option value="adjustment_decrease">Decrease Stock</option>
                            <option value="damage">Damage/Loss</option>
                            <option value="return">Return to Stock</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="Reason for adjustment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Movement History Modal -->
<div class="modal fade" id="movementHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stock Movement History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="movementHistoryContent">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Stock adjustment form submission
document.getElementById('stockAdjustmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("admin.inventory.adjust") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Stock adjustment applied successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while applying the adjustment.');
    });
});

// View movement history
function viewMovements(productId) {
    fetch(`{{ url('admin/inventory/movements') }}/${productId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('movementHistoryContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('movementHistoryModal')).show();
        });
}

// Adjust stock for specific product
function adjustStock(productId) {
    document.querySelector('[name="product_id"]').value = productId;
    new bootstrap.Modal(document.getElementById('stockAdjustmentModal')).show();
}

// View forecast
function viewForecast(productId) {
    window.open(`{{ url('admin/inventory/forecast') }}/${productId}`, '_blank');
}

// Generate report
function generateReport() {
    window.open('{{ route("admin.inventory.report") }}', '_blank');
}

// Check low stock
function checkLowStock() {
    document.getElementById('stockFilter').value = 'low_stock';
    filterTable();
}

// Filter functionality
function filterTable() {
    const category = document.getElementById('categoryFilter').value;
    const stock = document.getElementById('stockFilter').value;
    const search = document.getElementById('searchProduct').value;
    
    const params = new URLSearchParams();
    if (category) params.append('category', category);
    if (stock) params.append('stock', stock);
    if (search) params.append('search', search);
    
    window.location.href = '{{ route("admin.inventory.index") }}?' + params.toString();
}

// Reset filters
function resetFilters() {
    document.getElementById('categoryFilter').value = '';
    document.getElementById('stockFilter').value = '';
    document.getElementById('searchProduct').value = '';
    window.location.href = '{{ route("admin.inventory.index") }}';
}

// Add event listeners
document.getElementById('categoryFilter').addEventListener('change', filterTable);
document.getElementById('stockFilter').addEventListener('change', filterTable);
document.getElementById('searchProduct').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        filterTable();
    }
});
</script>
@endsection
