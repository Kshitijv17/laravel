@extends('layouts.admin')

@section('title', 'Orders Management')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Orders Management</h1>
            <p class="page-subtitle">Manage and track all customer orders</p>
        </div>
        <div>
            <button class="btn btn-primary" onclick="exportOrders()">
                <i class="fas fa-download me-2"></i>Export Orders
            </button>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Orders</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_orders'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_orders'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed_orders'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">All Orders</h5>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <input type="date" class="form-control form-control-sm" id="dateFilter" placeholder="Filter by date">
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table" id="ordersTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong>#{{ $order->id }}</strong>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    @if($order->user->avatar)
                                    <img src="{{ asset('storage/' . $order->user->avatar) }}" 
                                         class="rounded-circle" width="32" height="32" alt="Avatar">
                                    @else
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 32px; height: 32px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $order->user->name }}</div>
                                    <small class="text-muted">{{ $order->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $order->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $order->items->count() }} items</span>
                        </td>
                        <td>
                            <strong>${{ number_format($order->total_amount, 2) }}</strong>
                        </td>
                        <td>
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
                            <span class="badge bg-{{ $color }}">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td>
                            @php
                                $paymentColors = [
                                    'paid' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    'refunded' => 'info'
                                ];
                                $paymentColor = $paymentColors[$order->payment_status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $paymentColor }}">{{ ucfirst($order->payment_status) }}</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.orders.edit', $order->id) }}" 
                                   class="btn btn-sm btn-outline-secondary" title="Edit Order">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-info" 
                                        onclick="updateOrderStatus({{ $order->id }})" title="Update Status">
                                    <i class="fas fa-sync"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                <p>No orders found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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

@push('scripts')
<script>
let ordersDataTable = null;

$(document).ready(function() {
    initializeOrdersTable();
});

function initializeOrdersTable() {
    // Destroy existing DataTable if it exists
    if (ordersDataTable && $.fn.DataTable.isDataTable('#ordersTable')) {
        ordersDataTable.destroy();
        ordersDataTable = null;
    }
    
    // Clear any existing table HTML
    $('#ordersTable').empty();
    
    // Reinitialize table structure if needed
    if ($('#ordersTable thead').length === 0) {
        location.reload();
        return;
    }
    
    // Initialize new DataTable
    ordersDataTable = $('#ordersTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 25,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [7] }
        ],
        language: {
            search: "Search orders:",
            lengthMenu: "Show _MENU_ orders per page",
            info: "Showing _START_ to _END_ of _TOTAL_ orders",
            infoEmpty: "No orders found",
            infoFiltered: "(filtered from _MAX_ total orders)",
            emptyTable: "No orders available"
        }
    });
    
    // Status filter
    $('#statusFilter').off('change').on('change', function() {
        var status = $(this).val();
        if (ordersDataTable) {
            ordersDataTable.column(5).search(status).draw();
        }
    });
    
    // Date filter
    $('#dateFilter').off('change').on('change', function() {
        var date = $(this).val();
        if (ordersDataTable) {
            ordersDataTable.column(2).search(date).draw();
        }
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
