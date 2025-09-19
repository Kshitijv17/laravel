@extends('layouts.admin')

@section('title', 'Analytics & Reports')
@section('subtitle', 'Monitor your business performance and insights')

@section('content')
<div class="d-flex justify-content-end align-items-center mb-3">
    <div class="btn-group">
        <button class="btn btn-primary" onclick="exportReport()">
            <i class="fas fa-download me-2"></i>Export Report
        </button>
        <button class="btn btn-success" onclick="scheduleReport()">
            <i class="fas fa-clock me-2"></i>Schedule Report
        </button>
        <button class="btn btn-info" onclick="refreshAnalytics()">
            <i class="fas fa-sync me-2"></i>Refresh Data
        </button>
    </div>
</div>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Analytics</li>
    </ol>
</nav>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-3">
                <label class="form-label">Date Range</label>
                <select class="form-select" id="dateRange">
                    <option value="7">Last 7 Days</option>
                    <option value="30" selected>Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="365">Last Year</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" id="startDate">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" id="endDate">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button class="btn btn-primary" onclick="updateAnalytics()">
                        <i class="fas fa-refresh me-2"></i>Update
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Key Metrics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalRevenue">$12,345</div>
                        <div class="text-xs text-success">
                            <i class="fas fa-arrow-up"></i> 12.5% vs last period
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalOrders">1,234</div>
                        <div class="text-xs text-success">
                            <i class="fas fa-arrow-up"></i> 8.2% vs last period
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Order Value</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgOrderValue">$89.50</div>
                        <div class="text-xs text-danger">
                            <i class="fas fa-arrow-down"></i> 2.1% vs last period
                        </div>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Conversion Rate</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="conversionRate">3.45%</div>
                        <div class="text-xs text-success">
                            <i class="fas fa-arrow-up"></i> 0.8% vs last period
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Revenue Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Revenue Trend</h5>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Orders by Status -->
    <div class="col-xl-4 col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Orders by Status</h5>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="ordersStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products and Categories -->
<div class="row mb-4">
    <!-- Top Products -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Selling Products</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Sales</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="topProductsTable">
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded me-2" style="width: 40px; height: 40px;"></div>
                                        <div>
                                            <div class="fw-bold">Wireless Headphones</div>
                                            <small class="text-muted">Electronics</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">156</span></td>
                                <td><strong>$15,600</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded me-2" style="width: 40px; height: 40px;"></div>
                                        <div>
                                            <div class="fw-bold">Smart Watch</div>
                                            <small class="text-muted">Electronics</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">142</span></td>
                                <td><strong>$14,200</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded me-2" style="width: 40px; height: 40px;"></div>
                                        <div>
                                            <div class="fw-bold">Running Shoes</div>
                                            <small class="text-muted">Sports</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">128</span></td>
                                <td><strong>$12,800</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Categories -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Categories</h5>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Analytics -->
<div class="row mb-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Customer Acquisition</h5>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="customerAcquisitionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Traffic Sources</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <div class="h4 text-primary">45%</div>
                            <div class="text-muted">Direct</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <div class="h4 text-success">28%</div>
                            <div class="text-muted">Organic Search</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <div class="h4 text-info">15%</div>
                            <div class="text-muted">Social Media</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <div class="h4 text-warning">12%</div>
                            <div class="text-muted">Referral</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    initializeCharts();
    setDefaultDates();
});

function setDefaultDates() {
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - 30);
    
    $('#endDate').val(endDate.toISOString().split('T')[0]);
    $('#startDate').val(startDate.toISOString().split('T')[0]);
}

function initializeCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue',
                data: [12000, 15000, 18000, 14000, 22000, 25000, 28000, 24000, 30000, 32000, 35000, 38000],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Orders Status Chart
    const ordersStatusCtx = document.getElementById('ordersStatusChart').getContext('2d');
    new Chart(ordersStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Processing', 'Pending', 'Cancelled'],
            datasets: [{
                data: [65, 20, 10, 5],
                backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Categories Chart
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    new Chart(categoriesCtx, {
        type: 'bar',
        data: {
            labels: ['Electronics', 'Clothing', 'Sports', 'Books', 'Home'],
            datasets: [{
                label: 'Sales',
                data: [450, 320, 280, 150, 200],
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Customer Acquisition Chart
    const customerCtx = document.getElementById('customerAcquisitionChart').getContext('2d');
    new Chart(customerCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'New Customers',
                data: [45, 52, 48, 61],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateAnalytics() {
    const dateRange = $('#dateRange').val();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    
    // Show loading state
    $('.card-body').addClass('loading');
    
    // Simulate API call
    setTimeout(function() {
        $('.card-body').removeClass('loading');
        alert('Analytics updated for the selected date range');
    }, 1000);
}

function exportReport() {
    const dateRange = $('#dateRange').val();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    
    // Simulate report generation
    alert('Generating report... This may take a few moments.');
    
    // In a real implementation, this would trigger a download
    setTimeout(function() {
        alert('Report generated successfully!');
    }, 2000);
}

function scheduleReport() {
    alert('Schedule Report functionality would be implemented here - allowing users to set up automated reports');
}

function refreshAnalytics() {
    // Show loading state
    const refreshBtn = event.target.closest('button');
    const originalText = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
    refreshBtn.disabled = true;
    
    // Simulate data refresh
    setTimeout(function() {
        refreshBtn.innerHTML = originalText;
        refreshBtn.disabled = false;
        alert('Analytics data refreshed successfully!');
        updateAnalytics();
    }, 2000);
}

$('#dateRange').on('change', function() {
    if ($(this).val() === 'custom') {
        $('#startDate, #endDate').prop('disabled', false);
    } else {
        $('#startDate, #endDate').prop('disabled', true);
        
        const endDate = new Date();
        const startDate = new Date();
        const days = parseInt($(this).val());
        startDate.setDate(startDate.getDate() - days);
        
        $('#endDate').val(endDate.toISOString().split('T')[0]);
        $('#startDate').val(startDate.toISOString().split('T')[0]);
    }
});
</script>
@endpush
