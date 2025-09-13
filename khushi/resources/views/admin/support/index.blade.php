@extends('layouts.admin')

@section('title', 'Support Tickets')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Support Tickets</h1>
            <p class="page-subtitle">Manage customer support requests and inquiries</p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                <i class="fas fa-plus me-2"></i>Create Ticket
            </button>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Support Tickets</li>
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
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tickets</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_tickets'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Open Tickets</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['open_tickets'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Resolved Today</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['resolved_today'] ?? 0 }}</div>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg Response Time</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['avg_response_time'] ?? '2h' }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Priority</label>
                <select class="form-select" id="priorityFilter">
                    <option value="">All Priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Category</label>
                <select class="form-select" id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="general">General</option>
                    <option value="technical">Technical</option>
                    <option value="billing">Billing</option>
                    <option value="shipping">Shipping</option>
                    <option value="returns">Returns</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" id="searchInput" placeholder="Search tickets...">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button class="btn btn-primary w-100" onclick="applyFilters()">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tickets Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Support Tickets</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="ticketsTable">
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Subject</th>
                        <th>Customer</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Sample data since $tickets is not passed from controller -->
                    <tr>
                        <td>
                            <a href="#" class="text-decoration-none">#001</a>
                        </td>
                        <td>
                            <a href="#" class="text-decoration-none">Login Issue with Account</a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user text-muted"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">John Doe</div>
                                    <small class="text-muted">john@example.com</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">Technical</span>
                        </td>
                        <td>
                            <span class="badge bg-danger">High</span>
                        </td>
                        <td>
                            <span class="badge bg-primary">Open</span>
                        </td>
                        <td>Jan 15, 2024</td>
                        <td>2 hours ago</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" onclick="updateStatus(1)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteTicket(1)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="#" class="text-decoration-none">#002</a>
                        </td>
                        <td>
                            <a href="#" class="text-decoration-none">Payment Processing Error</a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user text-muted"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Jane Smith</div>
                                    <small class="text-muted">jane@example.com</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">Billing</span>
                        </td>
                        <td>
                            <span class="badge bg-warning">Medium</span>
                        </td>
                        <td>
                            <span class="badge bg-warning">In Progress</span>
                        </td>
                        <td>Jan 14, 2024</td>
                        <td>1 day ago</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" onclick="updateStatus(2)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteTicket(2)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="#" class="text-decoration-none">#003</a>
                        </td>
                        <td>
                            <a href="#" class="text-decoration-none">Product Return Request</a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user text-muted"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Mike Johnson</div>
                                    <small class="text-muted">mike@example.com</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">Returns</span>
                        </td>
                        <td>
                            <span class="badge bg-success">Low</span>
                        </td>
                        <td>
                            <span class="badge bg-success">Resolved</span>
                        </td>
                        <td>Jan 13, 2024</td>
                        <td>2 days ago</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" onclick="updateStatus(3)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteTicket(3)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        @if(isset($tickets) && $tickets->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create Ticket Modal -->
<div class="modal fade" id="createTicketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Support Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createTicketForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" class="form-control" name="customer_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Customer Email</label>
                                <input type="email" class="form-control" name="customer_email" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="general">General</option>
                                    <option value="technical">Technical</option>
                                    <option value="billing">Billing</option>
                                    <option value="shipping">Shipping</option>
                                    <option value="returns">Returns</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="open" selected>Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="6" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Ticket Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStatusForm">
                <div class="modal-body">
                    <input type="hidden" id="ticketId" name="ticket_id">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="ticketStatus" required>
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Internal Note (Optional)</label>
                        <textarea class="form-control" name="internal_note" rows="3" placeholder="Add an internal note about this status change..."></textarea>
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
$(document).ready(function() {
    // Check if DataTable is already initialized
    if ($.fn.DataTable.isDataTable('#ticketsTable')) {
        $('#ticketsTable').DataTable().destroy();
    }
    
    $('#ticketsTable').DataTable({
        pageLength: 25,
        order: [[6, 'desc']],
        columnDefs: [
            { orderable: false, targets: [8] }
        ]
    });
});

// Create ticket form
$('#createTicketForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    
    $.ajax({
        url: '/admin/support',
        method: 'POST',
        data: formData + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
        success: function(response) {
            if (response.success) {
                $('#createTicketModal').modal('hide');
                alert('Ticket created successfully!');
                location.reload();
            } else {
                alert('Error creating ticket: ' + response.message);
            }
        },
        error: function() {
            alert('Error creating ticket');
        }
    });
});

// Update status form
$('#updateStatusForm').on('submit', function(e) {
    e.preventDefault();
    
    const ticketId = $('#ticketId').val();
    const formData = $(this).serialize();
    
    $.ajax({
        url: '/admin/support/' + ticketId + '/status',
        method: 'POST',
        data: formData + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
        success: function(response) {
            if (response.success) {
                $('#updateStatusModal').modal('hide');
                alert('Status updated successfully!');
                location.reload();
            } else {
                alert('Error updating status: ' + response.message);
            }
        },
        error: function() {
            alert('Error updating status');
        }
    });
});

function updateStatus(ticketId) {
    $('#ticketId').val(ticketId);
    $('#updateStatusModal').modal('show');
}

function deleteTicket(id) {
    if (confirm('Are you sure you want to delete this ticket? This action cannot be undone.')) {
        $.ajax({
            url: '/admin/support/' + id,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting ticket: ' + response.message);
                }
            },
            error: function() {
                alert('Error deleting ticket');
            }
        });
    }
}

function applyFilters() {
    const status = $('#statusFilter').val();
    const priority = $('#priorityFilter').val();
    const category = $('#categoryFilter').val();
    const search = $('#searchInput').val();
    
    let url = new URL(window.location.href);
    
    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    
    if (priority) url.searchParams.set('priority', priority);
    else url.searchParams.delete('priority');
    
    if (category) url.searchParams.set('category', category);
    else url.searchParams.delete('category');
    
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    
    window.location.href = url.toString();
}

// Auto-refresh every 5 minutes for new tickets
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endpush
