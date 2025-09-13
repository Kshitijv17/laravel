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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Campaigns Sent</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['campaigns_sent'] ?? 0 }}</div>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Open Rate</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['open_rate'] ?? '0' }}%</div>
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
        <div class="row align-items-center">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date Range</label>
                <select class="form-select" id="dateFilter">
                    <option value="">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" id="searchInput" placeholder="Search by email...">
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
                    <!-- Sample data since $subscribers is not passed from controller -->
                    <tr>
                        <td>
                            <input type="checkbox" class="subscriber-checkbox" value="1">
                        </td>
                        <td>john@example.com</td>
                        <td>John Doe</td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td>Jan 15, 2024</td>
                        <td>Website</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus(1, 'inactive')">
                                    <i class="fas fa-pause"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSubscriber(1)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" class="subscriber-checkbox" value="2">
                        </td>
                        <td>jane@example.com</td>
                        <td>Jane Smith</td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td>Jan 10, 2024</td>
                        <td>Social Media</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus(2, 'inactive')">
                                    <i class="fas fa-pause"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSubscriber(2)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" class="subscriber-checkbox" value="3">
                        </td>
                        <td>mike@example.com</td>
                        <td>Mike Johnson</td>
                        <td>
                            <span class="badge bg-danger">Inactive</span>
                        </td>
                        <td>Jan 05, 2024</td>
                        <td>Newsletter</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-success" onclick="toggleStatus(3, 'active')">
                                    <i class="fas fa-play"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSubscriber(3)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
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
                            <option value="all">All Active Subscribers</option>
                            <option value="selected">Selected Subscribers</option>
                            <option value="segment">Specific Segment</option>
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

// Send newsletter form
$('#sendNewsletterForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    
    $.ajax({
        url: '/admin/newsletter/send',
        method: 'POST',
        data: formData + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
        success: function(response) {
            if (response.success) {
                $('#sendNewsletterModal').modal('hide');
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

function toggleStatus(id, status) {
    $.ajax({
        url: '/admin/newsletter/' + id + '/toggle-status',
        method: 'POST',
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
    const selectedIds = $('.subscriber-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    let url = '/admin/newsletter/export';
    if (selectedIds.length > 0) {
        url += '?ids=' + selectedIds.join(',');
    }
    
    window.open(url, '_blank');
}

function applyFilters() {
    const status = $('#statusFilter').val();
    const dateRange = $('#dateFilter').val();
    const search = $('#searchInput').val();
    
    let url = new URL(window.location.href);
    
    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    
    if (dateRange) url.searchParams.set('date_range', dateRange);
    else url.searchParams.delete('date_range');
    
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    
    window.location.href = url.toString();
}

// Bulk actions
function bulkAction(action) {
    const selectedIds = $('.subscriber-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        alert('Please select at least one subscriber');
        return;
    }
    
    if (confirm('Are you sure you want to perform this action on ' + selectedIds.length + ' subscribers?')) {
        $.ajax({
            url: '/admin/newsletter/bulk-action',
            method: 'POST',
            data: {
                action: action,
                ids: selectedIds,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error performing bulk action: ' + response.message);
                }
            },
            error: function() {
                alert('Error performing bulk action');
            }
        });
    }
}
</script>
@endpush
