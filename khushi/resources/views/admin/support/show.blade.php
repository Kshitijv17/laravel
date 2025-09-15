@extends('layouts.admin')

@section('title', 'Support Ticket Details')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Ticket #{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}</h1>
            <p class="page-subtitle">{{ $ticket->subject }}</p>
        </div>
        <div>
            <button class="btn btn-info me-2" onclick="updateTicketStatus({{ $ticket->id }})">
                <i class="fas fa-edit me-2"></i>Update Status
            </button>
            <a href="{{ route('admin.support.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Tickets
            </a>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.support.index') }}">Support Tickets</a></li>
            <li class="breadcrumb-item active">#{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ticket Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Subject</h6>
                        <p class="fw-bold">{{ $ticket->subject }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Status</h6>
                        <span class="badge {{ $ticket->status === 'open' ? 'bg-primary' : ($ticket->status === 'in_progress' ? 'bg-warning' : ($ticket->status === 'resolved' ? 'bg-success' : 'bg-secondary')) }} fs-6">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Priority</h6>
                        <span class="badge {{ $ticket->priority === 'high' ? 'bg-danger' : ($ticket->priority === 'normal' ? 'bg-warning' : 'bg-success') }} fs-6">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Created</h6>
                        <p>{{ $ticket->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6 class="text-muted">Message</h6>
                    <div class="p-3 bg-light rounded">
                        {!! nl2br(e($ticket->message)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-lg bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">{{ $ticket->user->name ?? 'Unknown User' }}</h6>
                        <p class="text-muted mb-0">{{ $ticket->user->email ?? 'No email' }}</p>
                    </div>
                </div>
                
                @if($ticket->user)
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="mb-1">{{ $ticket->user->orders->count() ?? 0 }}</h5>
                            <small class="text-muted">Orders</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="mb-1">{{ $ticket->user->supportTickets->count() ?? 0 }}</h5>
                        <small class="text-muted">Tickets</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="updateTicketStatus({{ $ticket->id }})">
                        <i class="fas fa-edit me-2"></i>Change Status
                    </button>
                    <button class="btn btn-outline-info" onclick="editTicket({{ $ticket->id }})">
                        <i class="fas fa-pencil-alt me-2"></i>Edit Ticket
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteTicket({{ $ticket->id }})">
                        <i class="fas fa-trash me-2"></i>Delete Ticket
                    </button>
                </div>
            </div>
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
                    <input type="hidden" id="ticketId" name="ticket_id" value="{{ $ticket->id }}">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="ticketStatus" required>
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
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

<!-- Edit Ticket Modal -->
<div class="modal fade" id="editTicketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Support Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTicketForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority" required>
                                    <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="normal" {{ $ticket->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject" value="{{ $ticket->subject }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="6" required>{{ $ticket->message }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateTicketStatus(ticketId) {
    $('#updateStatusModal').modal('show');
}

function editTicket(ticketId) {
    $('#editTicketModal').modal('show');
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
                    window.location.href = '{{ route("admin.support.index") }}';
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

// Edit ticket form
$('#editTicketForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    
    $.ajax({
        url: '/admin/support/{{ $ticket->id }}',
        method: 'PUT',
        data: formData + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
        success: function(response) {
            if (response.success) {
                $('#editTicketModal').modal('hide');
                location.reload();
            } else {
                alert('Error updating ticket: ' + response.message);
            }
        },
        error: function() {
            alert('Error updating ticket');
        }
    });
});
</script>
@endpush
