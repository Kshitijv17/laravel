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
        <form method="GET" class="row align-items-center">
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Priority</label>
                <select class="form-select" name="priority">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search tickets...">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.support.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
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
                    @forelse($tickets as $ticket)
                    <tr>
                        <td>
                            <a href="{{ route('admin.support.show', $ticket) }}" class="text-decoration-none">
                                #{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('admin.support.show', $ticket) }}" class="text-decoration-none">
                                {{ $ticket->subject }}
                            </a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user text-muted"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $ticket->user->name ?? 'Unknown User' }}</div>
                                    <small class="text-muted">{{ $ticket->user->email ?? 'No email' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">General</span>
                        </td>
                        <td>
                            <span class="badge {{ $ticket->priority === 'high' ? 'bg-danger' : ($ticket->priority === 'normal' ? 'bg-warning' : 'bg-success') }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $ticket->status === 'open' ? 'bg-primary' : ($ticket->status === 'in_progress' ? 'bg-warning' : ($ticket->status === 'resolved' ? 'bg-success' : 'bg-secondary')) }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                        <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.support.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-info" onclick="updateStatus({{ $ticket->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteTicket({{ $ticket->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                                <h5>No support tickets found</h5>
                                <p>No tickets match your current filters.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
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
                    <div class="mb-3">
                        <label class="form-label">Customer</label>
                        <select class="form-select" name="user_id" required>
                            <option value="">Select Customer</option>
                            @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
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
                                    <option value="normal" selected>Normal</option>
                                    <option value="high">High</option>
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
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="6" required></textarea>
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
    // Force remove any modal backdrops and reset body state
    function forceRemoveBackdrop() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '').css('padding-right', '');
        $('html').removeClass('modal-open');
    }
    
    // Initial cleanup
    forceRemoveBackdrop();
    
    // Aggressive cleanup every 100ms for the first 2 seconds
    let cleanupAttempts = 0;
    const cleanupInterval = setInterval(function() {
        forceRemoveBackdrop();
        cleanupAttempts++;
        if (cleanupAttempts > 20) {
            clearInterval(cleanupInterval);
        }
    }, 100);
    
    // Initialize DataTable only if table exists and has data
    const table = $('#supportTicketsTable');
    if (table.length && table.find('tbody tr').length > 0) {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#supportTicketsTable')) {
            table.DataTable().destroy();
        }
        
        // Initialize new DataTable
        table.DataTable({
            "pageLength": 25,
            "order": [[ 0, "desc" ]],
            "columnDefs": [
                { "orderable": false, "targets": [7] }
            ],
            "language": {
                "emptyTable": "No support tickets found"
            }
        });
    }
});

// Create ticket form
$('#createTicketForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    
    $.ajax({
        url: '{{ route("admin.support.store") }}',
        method: 'POST',
        data: formData + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
        success: function(response) {
            if (response.success) {
                $('#createTicketModal').modal('hide');
                forceRemoveBackdrop();
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
    
    const ticketId = $('#statusTicketId').val();
    const formData = $(this).serialize();
    
    $.ajax({
        url: '/admin/support/' + ticketId + '/status',
        method: 'POST',
        data: formData + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
        success: function(response) {
            if (response.success) {
                $('#updateStatusModal').modal('hide');
                forceRemoveBackdrop();
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

// Modal event handlers to prevent backdrop issues
$('.modal').on('hidden.bs.modal', function () {
    forceRemoveBackdrop();
});

$('.modal').on('show.bs.modal', function () {
    forceRemoveBackdrop();
});

// Global click handler to remove backdrop if clicked
$(document).on('click', '.modal-backdrop', function() {
    forceRemoveBackdrop();
});

// Add a global function to force remove backdrop
window.forceRemoveBackdrop = function() {
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('overflow', '').css('padding-right', '');
    $('html').removeClass('modal-open');
};

function updateTicketStatus(id, currentStatus) {
    $('#statusTicketId').val(id);
    $('#ticketStatus').val(currentStatus);
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
</script>
@endpush
