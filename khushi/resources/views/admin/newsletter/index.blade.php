@extends('layouts.admin')

@section('title', 'Newsletter Management')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Newsletter Management</h1>
            <p class="page-subtitle">Manage newsletter subscribers and campaigns</p>
        </div>
        <div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#sendNewsletterModal">
                <i class="fas fa-paper-plane me-2"></i>Send Newsletter
            </button>
            <button class="btn btn-primary" onclick="exportSubscribers()">
                <i class="fas fa-download me-2"></i>Export Subscribers
            </button>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Newsletter</li>
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
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Subscribers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_subscribers'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Subscribers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_subscribers'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Inactive Subscribers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inactive_subscribers'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-envelope fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">New This Month</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['new_this_month'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by email or name...">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.newsletter.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addSubscriberModal">
                        <i class="fas fa-plus me-1"></i>Add Subscriber
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Subscribers Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Newsletter Subscribers</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="subscribersTable">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Subscribed Date</th>
                        <th>Source</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $subscriber)
                    <tr>
                        <td>
                            <input type="checkbox" class="subscriber-checkbox" value="{{ $subscriber->id }}">
                        </td>
                        <td>{{ $subscriber->email }}</td>
                        <td>{{ $subscriber->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $subscriber->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($subscriber->status) }}
                            </span>
                        </td>
                        <td>{{ $subscriber->subscribed_at ? $subscriber->subscribed_at->format('M d, Y') : $subscriber->created_at->format('M d, Y') }}</td>
                        <td>Website</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary" onclick="editSubscriber({{ $subscriber->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($subscriber->status === 'active')
                                <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus({{ $subscriber->id }}, 'inactive')">
                                    <i class="fas fa-pause"></i>
                                </button>
                                @else
                                <button class="btn btn-sm btn-outline-success" onclick="toggleStatus({{ $subscriber->id }}, 'active')">
                                    <i class="fas fa-play"></i>
                                </button>
                                @endif
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSubscriber({{ $subscriber->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>No newsletter subscribers found</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubscriberModal">
                                    <i class="fas fa-plus me-2"></i>Add First Subscriber
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($subscribers) && $subscribers->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $subscribers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Send Newsletter Modal -->
<div class="modal fade" id="sendNewsletterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Newsletter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sendNewsletterForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recipients</label>
                        <select class="form-select" name="recipients" required>
                            <option value="active_only">Active Subscribers Only</option>
                            <option value="all">All Subscribers</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Template</label>
                        <select class="form-select" name="template">
                            <option value="default">Default Template</option>
                            <option value="promotional">Promotional Template</option>
                            <option value="newsletter">Newsletter Template</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="content" rows="8" placeholder="Enter your newsletter content here..."></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="send_immediately" id="sendImmediately" checked>
                            <label class="form-check-label" for="sendImmediately">
                                Send immediately
                            </label>
                        </div>
                    </div>
                    <div class="mb-3" id="scheduleSection" style="display: none;">
                        <label class="form-label">Schedule Date & Time</label>
                        <input type="datetime-local" class="form-control" name="scheduled_at">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Newsletter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Subscriber Modal -->
<div class="modal fade" id="addSubscriberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Newsletter Subscriber</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSubscriberForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Email Address *</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Optional">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select" name="status" required>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Subscriber
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Subscriber Modal -->
<div class="modal fade" id="editSubscriberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Newsletter Subscriber</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSubscriberForm">
                <div class="modal-body">
                    <input type="hidden" id="editSubscriberId" name="subscriber_id">
                    <div class="mb-3">
                        <label class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="editName" name="name" placeholder="Optional">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select" id="editStatus" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Subscriber
                    </button>
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
    if ($.fn.DataTable.isDataTable('#subscribersTable')) {
        $('#subscribersTable').DataTable().destroy();
    }
    
    $('#subscribersTable').DataTable({
        pageLength: 25,
        order: [[4, 'desc']],
        columnDefs: [
            { orderable: false, targets: [0, 6] }
        ],
        autoWidth: false,
        responsive: true
    });
});

// Select all functionality
$('#selectAll').on('change', function() {
    $('.subscriber-checkbox').prop('checked', $(this).prop('checked'));
});

// Add subscriber form
$('#addSubscriberForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    
    $.ajax({
        url: '{{ route("admin.newsletter.store") }}',
        method: 'POST',
        data: formData + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
        success: function(response) {
            if (response.success) {
                $('#addSubscriberModal').modal('hide');
                forceRemoveBackdrop();
                location.reload();
            } else {
                alert('Error adding subscriber: ' + response.message);
            }
        },
        error: function() {
            alert('Error adding subscriber');
        }
    });
});

// Edit subscriber form
$('#editSubscriberForm').on('submit', function(e) {
    e.preventDefault();
    
    const subscriberId = $('#editSubscriberId').val();
    const formData = $(this).serialize();
    
    $.ajax({
        url: '/admin/newsletter/' + subscriberId,
        method: 'PUT',
        data: formData + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
        success: function(response) {
            if (response.success) {
                $('#editSubscriberModal').modal('hide');
                forceRemoveBackdrop();
                location.reload();
            } else {
                alert('Error updating subscriber: ' + response.message);
            }
        },
        error: function() {
            alert('Error updating subscriber');
        }
    });
});

// Send newsletter form
$('#sendNewsletterForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    
    $.ajax({
        url: '{{ route("admin.newsletter.send") }}',
        method: 'POST',
        data: formData + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
        success: function(response) {
            if (response.success) {
                $('#sendNewsletterModal').modal('hide');
                forceRemoveBackdrop();
                alert('Newsletter sent successfully!');
                location.reload();
            } else {
                alert('Error sending newsletter: ' + response.message);
            }
        },
        error: function() {
            alert('Error sending newsletter');
        }
    });
});

// Toggle send immediately
$('#sendImmediately').on('change', function() {
    if ($(this).is(':checked')) {
        $('#scheduleSection').hide();
    } else {
        $('#scheduleSection').show();
    }
});

function editSubscriber(id) {
    // Get subscriber data via AJAX or from the table
    const row = $('input[value="' + id + '"]').closest('tr');
    const email = row.find('td:nth-child(2)').text();
    const name = row.find('td:nth-child(3)').text();
    const status = row.find('.badge').text().toLowerCase();
    
    $('#editSubscriberId').val(id);
    $('#editEmail').val(email);
    $('#editName').val(name === 'N/A' ? '' : name);
    $('#editStatus').val(status);
    
    $('#editSubscriberModal').modal('show');
}

function toggleStatus(id, status) {
    $.ajax({
        url: '/admin/newsletter/' + id,
        method: 'PUT',
        data: {
            status: status,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error updating status: ' + response.message);
            }
        },
        error: function() {
            alert('Error updating status');
        }
    });
}

function deleteSubscriber(id) {
    if (confirm('Are you sure you want to delete this subscriber?')) {
        $.ajax({
            url: '/admin/newsletter/' + id,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting subscriber: ' + response.message);
                }
            },
            error: function() {
                alert('Error deleting subscriber');
            }
        });
    }
}

function exportSubscribers() {
    window.open('{{ route("admin.newsletter.export") }}', '_blank');
}
</script>
@endpush
