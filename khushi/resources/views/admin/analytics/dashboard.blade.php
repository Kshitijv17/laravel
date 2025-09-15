@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Analytics Dashboard</h4>
                <div class="page-title-right">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm {{ $period === 'today' ? 'active' : '' }}" 
                                onclick="changePeriod('today')">Today</button>
                        <button type="button" class="btn btn-outline-primary btn-sm {{ $period === 'week' ? 'active' : '' }}" 
                                onclick="changePeriod('week')">Week</button>
                        <button type="button" class="btn btn-outline-primary btn-sm {{ $period === 'month' ? 'active' : '' }}" 
                                onclick="changePeriod('month')">Month</button>
                        <button type="button" class="btn btn-outline-primary btn-sm {{ $period === 'year' ? 'active' : '' }}" 
                                onclick="changePeriod('year')">Year</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Revenue</p>
                        </div>
                        <div class="flex-shrink-0">
                            <h5 class="text-{{ $stats['revenue']['growth'] >= 0 ? 'success' : 'danger' }} fs-14 mb-0">
                                <i class="ri-arrow-{{ $stats['revenue']['growth'] >= 0 ? 'up' : 'down' }}-line fs-13 align-middle"></i>
                                {{ abs($stats['revenue']['growth']) }}%
                            </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value" data-target="{{ $stats['revenue']['current'] }}">{{ number_format($stats['revenue']['current'], 2) }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-dollar-circle text-success"></i>
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
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Orders</p>
                        </div>
                        <div class="flex-shrink-0">
                            <h5 class="text-{{ $stats['orders']['growth'] >= 0 ? 'success' : 'danger' }} fs-14 mb-0">
                                <i class="ri-arrow-{{ $stats['orders']['growth'] >= 0 ? 'up' : 'down' }}-line fs-13 align-middle"></i>
                                {{ abs($stats['orders']['growth']) }}%
                            </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $stats['orders']['current'] }}">{{ $stats['orders']['current'] }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-shopping-bag text-info"></i>
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
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Unique Visitors</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $stats['traffic']['unique_visitors'] }}">{{ $stats['traffic']['unique_visitors'] }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-user-voice text-warning"></i>
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
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Conversion Rate</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $stats['conversion']['conversion_rate'] }}">{{ $stats['conversion']['conversion_rate'] }}</span>%
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-trending-up text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Revenue Trend</h4>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Conversion Funnel</h4>
                </div>
                <div class="card-body">
                    <div class="funnel-chart">
                        @php
                            $funnel = $stats['conversion']['funnel_data'];
                            $maxValue = max($funnel);
                        @endphp
                        
                        <div class="funnel-step" style="width: 100%">
                            <div class="funnel-label">Visitors</div>
                            <div class="funnel-value">{{ number_format($funnel['visitors']) }}</div>
                        </div>
                        
                        <div class="funnel-step" style="width: {{ $maxValue > 0 ? ($funnel['product_views'] / $maxValue) * 100 : 0 }}%">
                            <div class="funnel-label">Product Views</div>
                            <div class="funnel-value">{{ number_format($funnel['product_views']) }}</div>
                        </div>
                        
                        <div class="funnel-step" style="width: {{ $maxValue > 0 ? ($funnel['add_to_cart'] / $maxValue) * 100 : 0 }}%">
                            <div class="funnel-label">Add to Cart</div>
                            <div class="funnel-value">{{ number_format($funnel['add_to_cart']) }}</div>
                        </div>
                        
                        <div class="funnel-step" style="width: {{ $maxValue > 0 ? ($funnel['purchases'] / $maxValue) * 100 : 0 }}%">
                            <div class="funnel-label">Purchases</div>
                            <div class="funnel-value">{{ number_format($funnel['purchases']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products and Pages -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Top Selling Products</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['products']['top_selling'] as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td><span class="badge badge-soft-success">{{ $product->total_sold }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Top Pages</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap">
                            <thead>
                                <tr>
                                    <th scope="col">Page</th>
                                    <th scope="col">Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['traffic']['top_pages'] as $page)
                                <tr>
                                    <td>{{ Str::limit($page->url, 50) }}</td>
                                    <td><span class="badge badge-soft-info">{{ $page->views }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">User Statistics</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>New Users</span>
                        <span class="fw-semibold">{{ number_format($stats['users']['new_users']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Active Users</span>
                        <span class="fw-semibold">{{ number_format($stats['users']['active_users']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Returning Users</span>
                        <span class="fw-semibold">{{ number_format($stats['users']['returning_users']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Retention Rate</span>
                        <span class="fw-semibold text-success">{{ $stats['users']['retention_rate'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Traffic Overview</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Page Views</span>
                        <span class="fw-semibold">{{ number_format($stats['traffic']['page_views']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Unique Visitors</span>
                        <span class="fw-semibold">{{ number_format($stats['traffic']['unique_visitors']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Bounce Rate</span>
                        <span class="fw-semibold text-warning">{{ $stats['traffic']['bounce_rate'] }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Avg. Session Duration</span>
                        <span class="fw-semibold">{{ gmdate('i:s', $stats['traffic']['avg_session_duration']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.analytics.revenue') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-line-chart me-1"></i> Revenue Report
                        </a>
                        <a href="{{ route('admin.analytics.products') }}" class="btn btn-outline-info btn-sm">
                            <i class="bx bx-package me-1"></i> Product Analytics
                        </a>
                        <a href="{{ route('admin.analytics.traffic') }}" class="btn btn-outline-warning btn-sm">
                            <i class="bx bx-trending-up me-1"></i> Traffic Analysis
                        </a>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="exportData()">
                            <i class="bx bx-download me-1"></i> Export Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.funnel-chart {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.funnel-step {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    text-align: center;
    border-radius: 5px;
    position: relative;
    min-width: 200px;
}

.funnel-step:nth-child(2) { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.funnel-step:nth-child(3) { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.funnel-step:nth-child(4) { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

.funnel-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.funnel-value {
    font-size: 18px;
    font-weight: bold;
    margin-top: 5px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function changePeriod(period) {
    window.location.href = '{{ route("admin.analytics.dashboard") }}?period=' + period;
}

function exportData() {
    window.location.href = '{{ route("admin.analytics.export") }}?type=dashboard&period={{ $period }}&format=csv';
}

// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($stats['revenue']['chart_data']->pluck('date')) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($stats['revenue']['chart_data']->pluck('revenue')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1,
            fill: true
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
                        return '$' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: $' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

// Auto-refresh data every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endsection
