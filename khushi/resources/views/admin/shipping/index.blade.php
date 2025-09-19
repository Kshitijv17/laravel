@extends('layouts.admin')

@section('title', 'Shipping Management')
@section('subtitle', 'Manage shipping zones, methods, and rates')

@section('content')
<div class="d-flex justify-content-end align-items-center mb-3">
    <div class="btn-group">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#shippingZoneModal">
            <i class="fas fa-plus me-2"></i>Add Zone
        </button>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#shippingMethodModal">
            <i class="fas fa-truck me-2"></i>Add Method
        </button>
        <button class="btn btn-info" onclick="calculateShipping()">
            <i class="fas fa-calculator me-2"></i>Rate Calculator
        </button>
    </div>
</div>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Shipping</li>
    </ol>
</nav>

<!-- Shipping Overview -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Shipping Zones</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_zones'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-globe fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Methods</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_methods'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-truck fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg. Shipping Cost</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['avg_cost'] ?? 0, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Free Shipping Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['free_shipping'] ?? 0 }}%</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-gift fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Shipping Zones -->
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Shipping Zones</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Zone Name</th>
                                <th>Countries/Regions</th>
                                <th>Methods</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shippingZones ?? [] as $zone)
                            <tr>
                                <td>
                                    <strong>{{ $zone->name }}</strong>
                                    <br><small class="text-muted">{{ $zone->description }}</small>
                                </td>
                                <td>
                                    @if($zone->countries && count($zone->countries) > 0)
                                        <div class="d-flex flex-wrap">
                                            @foreach(array_slice($zone->countries, 0, 3) as $country)
                                                <span class="badge bg-secondary me-1 mb-1">{{ $country }}</span>
                                            @endforeach
                                            @if(count($zone->countries) > 3)
                                                <span class="badge bg-light text-dark">+{{ count($zone->countries) - 3 }} more</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No countries assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $zone->methods_count ?? 0 }} methods</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $zone->is_active ? 'success' : 'secondary' }}">
                                        {{ $zone->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="editZone({{ $zone->id }})" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info" onclick="viewZoneMethods({{ $zone->id }})" title="Methods">
                                            <i class="fas fa-truck"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="toggleZoneStatus({{ $zone->id }})" title="Toggle Status">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteZone({{ $zone->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No shipping zones configured</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#shippingZoneModal">
                                        <i class="fas fa-plus me-2"></i>Add First Zone
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
    
    <div class="col-lg-4">
        <!-- Quick Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Settings</h5>
            </div>
            <div class="card-body">
                <form id="shippingSettingsForm">
                    <div class="mb-3">
                        <label class="form-label">Free Shipping Threshold</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="free_shipping_threshold" 
                                   value="{{ $settings['free_shipping_threshold'] ?? 100 }}" step="0.01">
                        </div>
                        <small class="form-text text-muted">Orders above this amount get free shipping</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Default Shipping Method</label>
                        <select class="form-select" name="default_shipping_method">
                            <option value="">Select default method</option>
                            @foreach($shippingMethods ?? [] as $method)
                                <option value="{{ $method->id }}" 
                                        {{ ($settings['default_shipping_method'] ?? '') == $method->id ? 'selected' : '' }}>
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="enable_shipping_calculator" 
                                   id="enableCalculator" {{ ($settings['enable_shipping_calculator'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="enableCalculator">
                                Enable shipping calculator on cart
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="require_shipping_address" 
                                   id="requireAddress" {{ ($settings['require_shipping_address'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requireAddress">
                                Require shipping address for all orders
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Settings
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Shipping Calculator -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Shipping Calculator</h5>
            </div>
            <div class="card-body">
                <form id="calculatorForm">
                    <div class="mb-3">
                        <label class="form-label">Destination Country</label>
                        <select class="form-select" name="country" required>
                            <option value="">Select country</option>
                            <option value="US">United States</option>
                            <option value="CA">Canada</option>
                            <option value="GB">United Kingdom</option>
                            <option value="AU">Australia</option>
                            <option value="DE">Germany</option>
                            <option value="FR">France</option>
                            <option value="JP">Japan</option>
                            <option value="IN">India</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Postal Code</label>
                        <input type="text" class="form-control" name="postal_code" placeholder="Enter postal code">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Package Weight (kg)</label>
                        <input type="number" class="form-control" name="weight" value="1" step="0.1" min="0.1">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Order Value</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="order_value" value="50" step="0.01">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-info w-100">
                        <i class="fas fa-calculator me-2"></i>Calculate Shipping
                    </button>
                </form>
                
                <div id="calculatorResults" class="mt-3 d-none">
                    <h6>Available Shipping Options:</h6>
                    <div id="shippingOptions"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Shipping Zone Modal -->
<div class="modal fade" id="shippingZoneModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Shipping Zone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="shippingZoneForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Zone Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <input type="number" class="form-control" name="priority" value="0">
                                <small class="form-text text-muted">Higher numbers = higher priority</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Countries/Regions *</label>
                        <select class="form-select" name="countries[]" multiple size="8">
                            <option value="US">United States</option>
                            <option value="CA">Canada</option>
                            <option value="GB">United Kingdom</option>
                            <option value="AU">Australia</option>
                            <option value="DE">Germany</option>
                            <option value="FR">France</option>
                            <option value="JP">Japan</option>
                            <option value="IN">India</option>
                            <option value="BR">Brazil</option>
                            <option value="MX">Mexico</option>
                            <option value="IT">Italy</option>
                            <option value="ES">Spain</option>
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple countries</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="zoneActive" checked>
                            <label class="form-check-label" for="zoneActive">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Zone
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Shipping Method Modal -->
<div class="modal fade" id="shippingMethodModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Shipping Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="shippingMethodForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Method Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Shipping Zone *</label>
                                <select class="form-select" name="zone_id" required>
                                    <option value="">Select zone</option>
                                    @foreach($shippingZones ?? [] as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Calculation Type *</label>
                                <select class="form-select" name="calculation_type" required onchange="toggleCalculationFields()">
                                    <option value="fixed">Fixed Rate</option>
                                    <option value="weight">Per Weight</option>
                                    <option value="percentage">Percentage of Order</option>
                                    <option value="free">Free Shipping</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3" id="costField">
                                <label class="form-label">Cost *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="cost" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Minimum Order Value</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="min_order_value" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estimated Delivery (days)</label>
                                <input type="text" class="form-control" name="estimated_delivery" placeholder="e.g., 3-5">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Maximum Weight (kg)</label>
                                <input type="number" class="form-control" name="max_weight" step="0.1">
                                <small class="form-text text-muted">Leave empty for no limit</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="methodActive" checked>
                            <label class="form-check-label" for="methodActive">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Method
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
document.getElementById('shippingZoneForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/shipping/zones', {
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
        alert('Error saving shipping zone');
    });
});

document.getElementById('shippingMethodForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/shipping/methods', {
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
        alert('Error saving shipping method');
    });
});

document.getElementById('shippingSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/shipping/settings', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Settings saved successfully');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving settings');
    });
});

document.getElementById('calculatorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/shipping/calculate', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayShippingOptions(data.options);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error calculating shipping');
    });
});

// Toggle calculation fields based on type
function toggleCalculationFields() {
    const calculationType = document.querySelector('[name="calculation_type"]').value;
    const costField = document.getElementById('costField');
    const costInput = document.querySelector('[name="cost"]');
    
    if (calculationType === 'free') {
        costField.style.display = 'none';
        costInput.required = false;
    } else {
        costField.style.display = 'block';
        costInput.required = true;
        
        const label = costField.querySelector('label');
        const prefix = costField.querySelector('.input-group-text');
        
        switch (calculationType) {
            case 'weight':
                label.textContent = 'Cost per kg *';
                prefix.textContent = '$';
                break;
            case 'percentage':
                label.textContent = 'Percentage *';
                prefix.textContent = '%';
                break;
            default:
                label.textContent = 'Cost *';
                prefix.textContent = '$';
        }
    }
}

// Display shipping options
function displayShippingOptions(options) {
    const container = document.getElementById('shippingOptions');
    const resultsDiv = document.getElementById('calculatorResults');
    
    if (options.length === 0) {
        container.innerHTML = '<p class="text-muted">No shipping options available for this destination.</p>';
    } else {
        container.innerHTML = options.map(option => `
            <div class="border rounded p-3 mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${option.name}</strong>
                        <br><small class="text-muted">${option.description || 'Standard shipping'}</small>
                        ${option.estimated_delivery ? `<br><small class="text-info">Delivery: ${option.estimated_delivery} days</small>` : ''}
                    </div>
                    <div class="text-end">
                        <strong class="text-primary">$${parseFloat(option.cost).toFixed(2)}</strong>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    resultsDiv.classList.remove('d-none');
}

// Zone management functions
function editZone(zoneId) {
    // Implementation for editing zone
    alert('Edit zone functionality would be implemented here');
}

function viewZoneMethods(zoneId) {
    // Implementation for viewing zone methods
    alert('View zone methods functionality would be implemented here');
}

function toggleZoneStatus(zoneId) {
    fetch(`/admin/shipping/zones/${zoneId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error toggling zone status');
        }
    });
}

function deleteZone(zoneId) {
    if (confirm('Are you sure you want to delete this shipping zone?')) {
        fetch(`/admin/shipping/zones/${zoneId}`, {
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
                alert('Error deleting zone');
            }
        });
    }
}

function calculateShipping() {
    // Focus on calculator form
    document.querySelector('[name="country"]').focus();
}
</script>
@endpush
