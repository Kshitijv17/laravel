@extends('layouts.admin')

@section('title', 'Tax Management')
@section('subtitle', 'Manage tax rates, classes, and calculations')

@section('content')
<div class="d-flex justify-content-end align-items-center mb-3">
    <div class="btn-group">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taxRateModal">
            <i class="fas fa-plus me-2"></i>Add Tax Rate
        </button>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#taxClassModal">
            <i class="fas fa-layer-group me-2"></i>Add Tax Class
        </button>
        <button class="btn btn-info" onclick="calculateTax()">
            <i class="fas fa-calculator me-2"></i>Tax Calculator
        </button>
    </div>
</div>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Tax Management</li>
    </ol>
</nav>

<!-- Tax Overview -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tax Rates</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_rates'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tax Classes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_classes'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg. Tax Rate</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['avg_rate'] ?? 0, 2) }}%</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Monthly Tax Collected</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['monthly_tax'] ?? 0, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Tax Rates -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Tax Rates</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Rate Name</th>
                                <th>Location</th>
                                <th>Rate</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($taxRates ?? [] as $rate)
                            <tr>
                                <td>
                                    <strong>{{ $rate->name }}</strong>
                                    <br><small class="text-muted">{{ $rate->description }}</small>
                                </td>
                                <td>
                                    @if($rate->country)
                                        <span class="badge bg-primary">{{ $rate->country }}</span>
                                    @endif
                                    @if($rate->state)
                                        <span class="badge bg-secondary">{{ $rate->state }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ number_format($rate->rate, 2) }}%</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $rate->type === 'inclusive' ? 'success' : 'warning' }}">
                                        {{ ucfirst($rate->type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $rate->is_active ? 'success' : 'secondary' }}">
                                        {{ $rate->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="editTaxRate({{ $rate->id }})" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteTaxRate({{ $rate->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-percentage fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No tax rates configured</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taxRateModal">
                                        <i class="fas fa-plus me-2"></i>Add First Tax Rate
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tax Settings & Calculator -->
    <div class="col-lg-4">
        <!-- Tax Calculator -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tax Calculator</h5>
            </div>
            <div class="card-body">
                <form id="taxCalculatorForm">
                    <div class="mb-3">
                        <label class="form-label">Product Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="price" value="100" step="0.01" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Customer Location</label>
                        <select class="form-select" name="location">
                            <option value="US">United States</option>
                            <option value="CA">Canada</option>
                            <option value="GB">United Kingdom</option>
                            <option value="AU">Australia</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-info w-100">
                        <i class="fas fa-calculator me-2"></i>Calculate Tax
                    </button>
                </form>
                
                <div id="taxCalculationResults" class="mt-3 d-none">
                    <h6>Tax Calculation:</h6>
                    <div class="border rounded p-3">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span id="calcSubtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Tax:</span>
                            <span id="calcTax">$0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong id="calcTotal">$0.00</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tax Rate Modal -->
<div class="modal fade" id="taxRateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tax Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="taxRateForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rate Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tax Rate (%) *</label>
                                <input type="number" class="form-control" name="rate" step="0.001" min="0" max="100" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select class="form-select" name="country">
                                    <option value="">All Countries</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="GB">United Kingdom</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">State/Province</label>
                                <input type="text" class="form-control" name="state" placeholder="e.g., CA, NY">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="rateActive" checked>
                            <label class="form-check-label" for="rateActive">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Tax Rate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tax Class Modal -->
<div class="modal fade" id="taxClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tax Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="taxClassForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="classActive" checked>
                            <label class="form-check-label" for="classActive">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Save Tax Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Form submissions
document.getElementById('taxRateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/taxes/rates', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving tax rate');
    });
});

document.getElementById('taxClassForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/taxes/classes', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving tax class');
    });
});

document.getElementById('taxCalculatorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/taxes/calculate', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayTaxCalculation(data.calculation);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error calculating tax');
    });
});

// Display tax calculation results
function displayTaxCalculation(calculation) {
    document.getElementById('calcSubtotal').textContent = '$' + parseFloat(calculation.subtotal).toFixed(2);
    document.getElementById('calcTax').textContent = '$' + parseFloat(calculation.tax_amount).toFixed(2);
    document.getElementById('calcTotal').textContent = '$' + parseFloat(calculation.total).toFixed(2);
    
    document.getElementById('taxCalculationResults').classList.remove('d-none');
}

// Tax management functions
function editTaxRate(rateId) {
    alert('Edit tax rate functionality would be implemented here');
}

function deleteTaxRate(rateId) {
    if (confirm('Are you sure you want to delete this tax rate?')) {
        fetch(`/admin/taxes/rates/${rateId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting tax rate');
            }
        });
    }
}

function calculateTax() {
    document.querySelector('[name="price"]').focus();
}
</script>
@endpush
