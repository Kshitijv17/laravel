@extends('layouts.admin')

@section('title', 'Advanced Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Advanced Reports</h1>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                        Export Reports
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#" onclick="exportReport('csv')">Export as CSV</a>
                        <a class="dropdown-item" href="#" onclick="exportReport('excel')">Export as Excel</a>
                        <a class="dropdown-item" href="#" onclick="exportReport('pdf')">Export as PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($reports as $key => $title)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    {{ $title }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @switch($key)
                                        @case('sales')
                                            📊 Sales Performance
                                            @break
                                        @case('products')
                                            📦 Product Analytics
                                            @break
                                        @case('customers')
                                            👥 Customer Insights
                                            @break
                                        @case('inventory')
                                            📋 Stock Management
                                            @break
                                        @case('traffic')
                                            🌐 Website Traffic
                                            @break
                                        @case('conversion')
                                            🎯 Conversion Funnel
                                            @break
                                        @case('revenue')
                                            💰 Revenue Analysis
                                            @break
                                        @case('geographic')
                                            🗺️ Geographic Data
                                            @break
                                    @endswitch
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.reports.' . $key) }}" class="btn btn-primary btn-sm">
                                        View Report
                                    </a>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-area fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Quick Stats Row -->
    <div class="row mt-4">
        <div class="col-12">
            <h4 class="mb-3">Quick Overview</h4>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Today's Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format(\App\Models\Order::where('status', 'completed')->whereDate('created_at', today())->sum('total_amount'), 2) }}
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Today's Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Order::whereDate('created_at', today())->count() }}
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                New Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::whereDate('created_at', today())->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Low Stock Items
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Product::where('stock_quantity', '<=', 10)->where('status', 'active')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Analytics Events</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>User</th>
                                    <th>Details</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Analytics::with('user')->latest()->limit(10)->get() as $event)
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $event->event_type)) }}</span>
                                        </td>
                                        <td>{{ $event->user ? $event->user->name : 'Guest' }}</td>
                                        <td>
                                            @if($event->data)
                                                {{ json_encode($event->data) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $event->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function exportReport(format) {
    // Implementation for exporting reports
    console.log('Exporting reports as:', format);
    
    fetch('{{ route("admin.reports.export") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            format: format,
            type: 'overview'
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Export response:', data);
        // Handle export response
    });
}
</script>
@endpush
