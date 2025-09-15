@extends('layouts.admin')

@section('title', 'Orders Management')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title mb-1">Orders Management</h1>
                <p class="page-subtitle mb-0">Manage and track all customer orders</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button class="btn btn-primary" onclick="exportOrders()">
                    <i class="fas fa-download me-2"></i>Export Orders
                </button>
            </div>
        </div>
        
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Orders</li>
            </ol>
        </nav>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stats-label">Total Orders</div>
                        <div class="stats-value text-primary">{{ $stats['total_orders'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-primary">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stats-label">Pending Orders</div>
                        <div class="stats-value text-warning">{{ $stats['pending_orders'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stats-label">Completed Orders</div>
                        <div class="stats-value text-success">{{ $stats['completed_orders'] ?? 0 }}</div>
                    </div>
                    <div class="stats-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stats-label">Total Revenue</div>
                        <div class="stats-value text-info">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                    </div>
                    <div class="stats-icon bg-info">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0 fw-bold">All Orders</h5>
                </div>
                <div class="col-md-6">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <select class="form-select form-select-sm" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <input type="date" class="form-control form-control-sm" id="dateFilter" placeholder="Filter by date">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="ordersTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-semibold">Order ID</th>
                            <th class="border-0 fw-semibold d-none d-md-table-cell">Customer</th>
                            <th class="border-0 fw-semibold d-none d-lg-table-cell">Date</th>
                            <th class="border-0 fw-semibold d-none d-sm-table-cell">Items</th>
                            <th class="border-0 fw-semibold">Total</th>
                            <th class="border-0 fw-semibold">Status</th>
                            <th class="border-0 fw-semibold d-none d-md-table-cell">Payment</th>
                            <th class="border-0 fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr class="align-middle">
                            <td class="py-3">
                                <div class="fw-bold text-primary">#{{ $order->id }}</div>
                                <div class="d-md-none">
                                    <small class="text-muted">{{ $order->user->name }}</small><br>
                                    <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell py-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        @if($order->user->avatar)
                                        <img src="{{ asset('storage/' . $order->user->avatar) }}" 
                                             class="rounded-circle" width="32" height="32" alt="Avatar">
                                        @else
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 32px; height: 32px;">
                                            <i class="fas fa-user text-white fs-6"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $order->user->name }}</div>
                                        <small class="text-muted">{{ $order->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-lg-table-cell py-3">
                                <div class="fw-semibold">{{ $order->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                            </td>
                            <td class="d-none d-sm-table-cell py-3">
                                <span class="badge bg-light text-dark border">{{ $order->items->count() }} items</span>
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-success">${{ number_format($order->total_amount, 2) }}</div>
                            </td>
                            <td class="py-3">
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $color = $statusColors[$order->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} px-2 py-1">{{ ucfirst($order->status) }}</span>
                                <div class="d-md-none mt-1">
                                    @php
                                        $paymentColors = [
                                            'paid' => 'success',
                                            'pending' => 'warning',
                                            'failed' => 'danger',
                                            'refunded' => 'info'
                                        ];
                                        $paymentColor = $paymentColors[$order->payment_status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $paymentColor }} px-2 py-1">{{ ucfirst($order->payment_status) }}</span>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell py-3">
                                @php
                                    $paymentColors = [
                                        'paid' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        'refunded' => 'info'
                                    ];
                                    $paymentColor = $paymentColors[$order->payment_status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $paymentColor }} px-2 py-1">{{ ucfirst($order->payment_status) }}</span>
                            </td>
                            <td class="text-center py-3">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('admin.orders.show', $order->id) }}">
                                            <i class="fas fa-eye me-2"></i>View Details
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.orders.edit', $order->id) }}">
                                            <i class="fas fa-edit me-2"></i>Edit Order
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }})">
                                            <i class="fas fa-sync me-2"></i>Update Status
                                        </a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-shopping-cart fa-3x mb-3 opacity-50"></i>
                                    <h5 class="text-muted">No orders found</h5>
                                    <p class="mb-0">Orders will appear here when customers place them.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Order Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="orderId">
                    <div class="mb-3">
                        <label class="form-label">Order Status</label>
                        <select class="form-select" id="orderStatus" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="statusNotes" rows="3" 
                                  placeholder="Add any notes about this status update..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stats-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px;
        will-change: transform;
    }
    
    .stats-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.08) !important;
    }
    
    .stats-label {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    .stats-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }
    
    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }
    
    .table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
        font-weight: 600;
        padding: 1rem 0.75rem;
    }
    
    .table td {
        padding: 0.75rem;
        border-color: #f1f3f4;
    }
    
    .badge {
        font-weight: 500;
        font-size: 0.75rem;
    }
    
    @media (max-width: 768px) {
        .stats-value {
            font-size: 1.5rem;
        }
        
        .stats-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .page-title {
            font-size: 1.5rem;
        }
        
        .card-header .row {
            gap: 1rem;
        }
        
        .table-responsive {
            border-radius: 0;
        }
    }
    
    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
            margin-left: 0;
            margin-right: 0;
            max-width: 100%;
        }
        
        .stats-card .card-body {
            padding: 1rem;
        }
        
        .page-header {
            padding: 1rem;
            margin-left: 0;
            margin-right: 0;
        }
        
        .card {
            margin-left: 0;
            margin-right: 0;
            border-radius: 0;
        }
        
        .row {
            margin-left: 0;
            margin-right: 0;
        }
        
        .col-xl-3, .col-lg-6, .col-md-6, .col-sm-6 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    }
    
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
        max-width: 100%;
        overflow-x: hidden;
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .main-content {
            width: 100% !important;
            margin-left: 0 !important;
            padding: 0;
            transform: translateZ(0);
        }
        
        .content-wrapper {
            padding: 0.5rem !important;
            margin: 0 !important;
            width: 100% !important;
            transform: translateZ(0);
        }
        
        /* Reduce repaints and reflows */
        .table-responsive {
            transform: translateZ(0);
            backface-visibility: hidden;
        }
        
        .card {
            transform: translateZ(0);
        }
    }
</style>
@endpush

@push('scripts')
<script>
let ordersDataTable = null;

$(document).ready(function() {
    // Delay DataTable initialization to prevent stuttering
    setTimeout(function() {
        initializeOrdersTable();
    }, 100);
});

function initializeOrdersTable() {
    // Check if DataTable already exists
    if ($.fn.DataTable.isDataTable('#ordersTable')) {
        return;
    }
    
    // Initialize DataTable with optimized settings
    ordersDataTable = $('#ordersTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 15,
        processing: false,
        serverSide: false,
        deferRender: true,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [-1] }, // Last column (actions)
            { responsivePriority: 1, targets: [0, 1, 4, 5] } // Priority columns for responsive
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries found",
            infoFiltered: "(filtered from _MAX_ total entries)",
            emptyTable: "No data available"
        },
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
        drawCallback: function() {
            // Re-initialize Bootstrap dropdowns after table draw
            $('[data-bs-toggle="dropdown"]').dropdown();
        }
    });
    
    // Optimized filters with debounce
    let statusTimeout;
    $('#statusFilter').off('change').on('change', function() {
        clearTimeout(statusTimeout);
        const status = $(this).val();
        statusTimeout = setTimeout(function() {
            if (ordersDataTable) {
                ordersDataTable.column(5).search(status).draw();
            }
        }, 300);
    });
    
    let dateTimeout;
    $('#dateFilter').off('change').on('change', function() {
        clearTimeout(dateTimeout);
        const date = $(this).val();
        dateTimeout = setTimeout(function() {
            if (ordersDataTable) {
                ordersDataTable.column(2).search(date).draw();
            }
        }, 300);
    });
}

function updateOrderStatus(orderId) {
    $('#orderId').val(orderId);
    $('#statusModal').modal('show');
}

$('#statusForm').on('submit', function(e) {
    e.preventDefault();
    
    var orderId = $('#orderId').val();
    var status = $('#orderStatus').val();
    var notes = $('#statusNotes').val();
    
    $.ajax({
        url: `/admin/orders/${orderId}/status`,
        method: 'PUT',
        data: {
            status: status,
            notes: notes,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#statusModal').modal('hide');
                location.reload();
            } else {
                alert('Error updating order status');
            }
        },
        error: function() {
            alert('Error updating order status');
        }
    });
});

function exportOrders() {
    window.location.href = '/admin/orders/export';
}
</script>
@endpush
