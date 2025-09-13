@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="d-sm-flex align-items-center">
            <span class="text-muted me-3">Last updated: {{ now()->format('M d, Y g:i A') }}</span>
            <button class="btn btn-primary btn-sm" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Dashboard Stats Row -->
    <div class="row">
        <!-- Total Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_orders'] }}</div>
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['total_revenue'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_products'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Sales Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#">Last 7 days</a>
                            <a class="dropdown-item" href="#">Last 30 days</a>
                            <a class="dropdown-item" href="#">Last 90 days</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Order Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Pending
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Completed
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Processing
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Cancelled
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders and Top Products Row -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    <a href="{{ route('admin.orders') }}" class="btn btn-primary btn-sm">View All</a>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-decoration-none">
                                            #{{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>
                                        <span class="badge badge-{{ 
                                            $order->status === 'completed' ? 'success' : 
                                            ($order->status === 'cancelled' ? 'danger' : 'warning') 
                                        }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">No recent orders</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                    <a href="{{ route('admin.products') }}" class="btn btn-primary btn-sm">View All</a>
                </div>
                <div class="card-body">
                    @if($topProducts->count() > 0)
                    @foreach($topProducts as $product)
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $product->images->first()->url ?? 'https://via.placeholder.com/50x50/f8f9fa/6c757d?text=Product' }}" 
                             alt="{{ $product->name }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ Str::limit($product->name, 30) }}</h6>
                            <small class="text-muted">{{ $product->orders_count }} orders</small>
                        </div>
                        <div class="text-end">
                            <span class="font-weight-bold">${{ number_format($product->price, 2) }}</span>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-box fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">No product data available</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions and Alerts Row -->
    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Product
                        </a>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Category
                        </a>
                        <a href="{{ route('admin.users') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-users me-1"></i>Manage Users
                        </a>
                        <a href="{{ route('admin.orders') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-shopping-cart me-1"></i>View Orders
                        </a>
                        <a href="{{ route('admin.analytics') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-chart-bar me-1"></i>Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Alerts -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Alerts</h6>
                </div>
                <div class="card-body">
                    <!-- Low Stock Alert -->
                    @if($lowStockProducts->count() > 0)
                    <div class="alert alert-warning" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert</h6>
                        <p class="mb-2">{{ $lowStockProducts->count() }} products are running low on stock:</p>
                        <ul class="mb-0">
                            @foreach($lowStockProducts->take(3) as $product)
                            <li>{{ $product->name }} ({{ $product->stock_quantity }} remaining)</li>
                            @endforeach
                            @if($lowStockProducts->count() > 3)
                            <li>... and {{ $lowStockProducts->count() - 3 }} more</li>
                            @endif
                        </ul>
                    </div>
                    @endif

                    <!-- Pending Orders Alert -->
                    @if($pendingOrdersCount > 0)
                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-clock me-2"></i>Pending Orders</h6>
                        <p class="mb-0">You have {{ $pendingOrdersCount }} pending orders that need attention.</p>
                    </div>
                    @endif

                    <!-- Support Tickets Alert -->
                    @if($openTicketsCount > 0)
                    <div class="alert alert-danger" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-headset me-2"></i>Open Support Tickets</h6>
                        <p class="mb-0">{{ $openTicketsCount }} support tickets are awaiting response.</p>
                    </div>
                    @endif

                    @if($lowStockProducts->count() == 0 && $pendingOrdersCount == 0 && $openTicketsCount == 0)
                    <div class="alert alert-success" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-check-circle me-2"></i>All Good!</h6>
                        <p class="mb-0">No urgent alerts at this time. Your store is running smoothly.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($salesData['labels']) !!},
        datasets: [{
            label: 'Sales',
            data: {!! json_encode($salesData['data']) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value;
                    }
                }
            }
        }
    }
});

// Order Status Chart
const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
const orderStatusChart = new Chart(orderStatusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($orderStatusData['labels']) !!},
        datasets: [{
            data: {!! json_encode($orderStatusData['data']) !!},
            backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#36b9cc',
                '#e74a3b'
            ],
            hoverBackgroundColor: [
                '#2e59d9',
                '#17a673',
                '#2c9faf',
                '#c0392b'
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        cutout: '80%'
    }
});
</script>
@endpush

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-xs {
    font-size: 0.7rem;
}

.font-weight-bold {
    font-weight: 700 !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.chart-area {
    position: relative;
    height: 320px;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 245px;
    width: 100%;
}

.badge-success {
    background-color: #1cc88a;
}

.badge-warning {
    background-color: #f6c23e;
}

.badge-danger {
    background-color: #e74a3b;
}
</style>
@endpush
