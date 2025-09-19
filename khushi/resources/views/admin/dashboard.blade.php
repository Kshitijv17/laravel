@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('subtitle', 'Welcome back, ' . Auth::guard('admin')->user()->name . '!')

@section('content')
<div class="container-fluid">

    <!-- Statistics Cards Row -->
    <div class="row">
        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_users']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_orders']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['total_revenue'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending_orders']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row of Statistics -->
    <div class="row">
        <!-- Total Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Total Products
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_products']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Products
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['active_products']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Open Support Tickets Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Open Tickets
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['open_tickets']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Newsletter Subscribers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Newsletter Subscribers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['newsletter_subscribers']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->user->first_name ?? 'Guest' }} {{ $order->user->last_name ?? '' }}</td>
                                            <td>${{ number_format($order->total_amount, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No recent orders found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Users</h6>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentUsers->count() > 0)
                        @foreach($recentUsers as $user)
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    @if($user->avatar)
                                        <img class="rounded-circle" src="{{ Storage::url($user->avatar) }}" alt="User Avatar" style="width: 40px; height: 40px;">
                                    @else
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                    <div class="text-muted small">{{ $user->email }}</div>
                                    <div class="text-muted small">Joined {{ $user->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No recent users found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics Row -->
    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue</h6>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary active" onclick="updateChart('revenue')">Revenue</button>
                        <button class="btn btn-outline-primary" onclick="updateChart('orders')">Orders</button>
                        <button class="btn btn-outline-primary" onclick="updateChart('customers')">Customers</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Real-time Stats -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Live Statistics</h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshStats()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-sm font-weight-bold">Today's Sales</span>
                            <span class="text-success" id="todaysSales">${{ number_format($stats['todays_sales'] ?? 0, 2) }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ min(($stats['todays_sales'] ?? 0) / 1000 * 100, 100) }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-sm font-weight-bold">Active Users</span>
                            <span class="text-info" id="activeUsers">{{ $stats['active_users'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ min(($stats['active_users'] ?? 0) / 100 * 100, 100) }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-sm font-weight-bold">Conversion Rate</span>
                            <span class="text-warning" id="conversionRate">{{ number_format($stats['conversion_rate'] ?? 0, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: {{ $stats['conversion_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-sm font-weight-bold">Server Load</span>
                            <span class="text-danger" id="serverLoad">{{ number_format($stats['server_load'] ?? 0, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-danger" style="width: {{ $stats['server_load'] ?? 0 }}%"></div>
                        </div>
                    </div>
                    
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">Last updated: <span id="lastUpdated">{{ now()->format('g:i A') }}</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions and Notifications Row -->
    <div class="row">
        <!-- Quick Actions -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.products.create') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-plus mr-2"></i>Add Product
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-shopping-cart mr-2"></i>View Orders
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-user-plus mr-2"></i>Add User
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.coupons.create') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-ticket-alt mr-2"></i>New Coupon
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-tags mr-2"></i>Add Category
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.banners.create') }}" class="btn btn-outline-dark btn-block">
                                <i class="fas fa-image mr-2"></i>New Banner
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-star mr-2"></i>Reviews
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.analytics') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-chart-bar mr-2"></i>Analytics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Notifications and Alerts -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Alerts</h6>
                </div>
                <div class="card-body">
                    <div id="alertsContainer">
                        @if(($stats['pending_orders'] ?? 0) > 0)
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>{{ $stats['pending_orders'] }}</strong> pending orders need attention.
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                        @endif
                        
                        @if(($stats['low_stock_products'] ?? 0) > 0)
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-box mr-2"></i>
                            <strong>{{ $stats['low_stock_products'] }}</strong> products are low in stock.
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                        @endif
                        
                        @if(($stats['pending_reviews'] ?? 0) > 0)
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-star mr-2"></i>
                            <strong>{{ $stats['pending_reviews'] }}</strong> reviews awaiting moderation.
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                        @endif
                        
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            System is running smoothly!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart data
const chartData = {
    revenue: @json($monthlyRevenue ?? collect()),
    orders: @json($monthlyOrders ?? collect()),
    customers: @json($monthlyCustomers ?? collect())
};

// Initialize chart
const ctx = document.getElementById('revenueChart').getContext('2d');
let currentChart = null;

function createChart(type = 'revenue') {
    if (currentChart) {
        currentChart.destroy();
    }
    
    const data = chartData[type] || [];
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    const config = {
        revenue: {
            label: 'Revenue',
            color: 'rgb(75, 192, 192)',
            bgColor: 'rgba(75, 192, 192, 0.1)',
            formatter: (value) => '$' + value.toLocaleString()
        },
        orders: {
            label: 'Orders',
            color: 'rgb(54, 162, 235)',
            bgColor: 'rgba(54, 162, 235, 0.1)',
            formatter: (value) => value.toLocaleString() + ' orders'
        },
        customers: {
            label: 'New Customers',
            color: 'rgb(255, 99, 132)',
            bgColor: 'rgba(255, 99, 132, 0.1)',
            formatter: (value) => value.toLocaleString() + ' customers'
        }
    };
    
    currentChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(item => monthNames[item.month - 1] || 'Unknown'),
            datasets: [{
                label: config[type].label,
                data: data.map(item => item[type] || item.value || 0),
                borderColor: config[type].color,
                backgroundColor: config[type].bgColor,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: config[type].color,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return config[type].formatter(value);
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                }
            },
            plugins: {
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: config[type].color,
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return config[type].label + ': ' + config[type].formatter(context.parsed.y);
                        }
                    }
                },
                legend: {
                    display: false
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

// Initialize with revenue chart
createChart('revenue');

// Chart update function
function updateChart(type) {
    // Update button states
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Create new chart
    createChart(type);
}

// Real-time stats refresh
function refreshStats() {
    const refreshBtn = document.querySelector('#refreshStats i');
    refreshBtn.classList.add('fa-spin');
    
    fetch('/admin/dashboard/live-stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update live stats
                document.getElementById('todaysSales').textContent = '$' + parseFloat(data.todays_sales || 0).toLocaleString();
                document.getElementById('activeUsers').textContent = data.active_users || 0;
                document.getElementById('conversionRate').textContent = parseFloat(data.conversion_rate || 0).toFixed(1) + '%';
                document.getElementById('serverLoad').textContent = parseFloat(data.server_load || 0).toFixed(1) + '%';
                document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString();
                
                // Update progress bars
                document.querySelector('.progress-bar.bg-success').style.width = Math.min((data.todays_sales || 0) / 1000 * 100, 100) + '%';
                document.querySelector('.progress-bar.bg-info').style.width = Math.min((data.active_users || 0) / 100 * 100, 100) + '%';
                document.querySelector('.progress-bar.bg-warning').style.width = (data.conversion_rate || 0) + '%';
                document.querySelector('.progress-bar.bg-danger').style.width = (data.server_load || 0) + '%';
            }
        })
        .catch(error => console.error('Error refreshing stats:', error))
        .finally(() => {
            refreshBtn.classList.remove('fa-spin');
        });
}

// Auto-refresh stats every 30 seconds
setInterval(refreshStats, 30000);

// Real-time notifications
function checkNotifications() {
    fetch('/admin/dashboard/notifications')
        .then(response => response.json())
        .then(data => {
            if (data.notifications && data.notifications.length > 0) {
                // Update notifications
                const alertsContainer = document.getElementById('alertsContainer');
                // Add new notifications logic here
            }
        })
        .catch(error => console.error('Error checking notifications:', error));
}

// Check notifications every 60 seconds
setInterval(checkNotifications, 60000);

</script>

<style>
.chart-area {
    position: relative;
}

.progress {
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.6s ease;
}

.btn-group .btn {
    transition: all 0.3s ease;
}

.btn-group .btn.active {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.alert {
    border-left: 4px solid;
    border-radius: 0.5rem;
}

.alert-warning {
    border-left-color: #ffc107;
}

.alert-danger {
    border-left-color: #dc3545;
}

.alert-info {
    border-left-color: #17a2b8;
}

.alert-success {
    border-left-color: #28a745;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.fa-spin {
    animation: pulse 1s infinite;
}
</style>
@endsection
