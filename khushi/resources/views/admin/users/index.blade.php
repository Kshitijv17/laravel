@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Users Management</h1>
            <p class="page-subtitle">Manage customer accounts and user data</p>
        </div>
        <div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New User
            </a>
        </div>
    </div>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Users</li>
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
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] ?? 0 }}</div>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_users'] ?? 0 }}</div>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">New This Month</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['new_users'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-plus fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Verification</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_verification'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">All Users</h5>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="banned">Banned</option>
                </select>
                <input type="date" class="form-control form-control-sm" id="dateFilter" placeholder="Registration date">
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table" id="usersTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                                         class="rounded-circle" width="40" height="40" alt="Avatar">
                                    @else
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $user->name }}</div>
                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $user->email }}</div>
                            @if($user->email_verified_at)
                            <small class="text-success"><i class="fas fa-check-circle"></i> Verified</small>
                            @else
                            <small class="text-warning"><i class="fas fa-clock"></i> Unverified</small>
                            @endif
                        </td>
                        <td>
                            {{ $user->phone ?? 'N/A' }}
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $user->orders_count ?? 0 }}</span>
                        </td>
                        <td>
                            <strong>${{ number_format($user->total_spent ?? 0, 2) }}</strong>
                        </td>
                        <td>
                            @if($user->status === 'active')
                            <span class="badge bg-success">Active</span>
                            @elseif($user->status === 'inactive')
                            <span class="badge bg-secondary">Inactive</span>
                            @elseif($user->status === 'banned')
                            <span class="badge bg-danger">Banned</span>
                            @else
                            <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $user->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.users.show', $user->id) }}" 
                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="btn btn-sm btn-outline-secondary" title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->status !== 'banned')
                                <button class="btn btn-sm btn-outline-warning" 
                                        onclick="banUser({{ $user->id }})" title="Ban User">
                                    <i class="fas fa-ban"></i>
                                </button>
                                @else
                                <button class="btn btn-sm btn-outline-success" 
                                        onclick="unbanUser({{ $user->id }})" title="Unban User">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <p>No users found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Check if DataTable already exists and destroy it
    if ($.fn.DataTable.isDataTable('#usersTable')) {
        $('#usersTable').DataTable().destroy();
    }
    
    // Initialize DataTable
    $('#usersTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 25,
        order: [[6, 'desc']],
        columnDefs: [
            { orderable: false, targets: [7] }
        ]
    });
    
    // Status filter
    $('#statusFilter').on('change', function() {
        var status = $(this).val();
        $('#usersTable').DataTable().column(5).search(status).draw();
    });
    
    // Date filter
    $('#dateFilter').on('change', function() {
        var date = $(this).val();
        $('#usersTable').DataTable().column(6).search(date).draw();
    });
});

function banUser(userId) {
    if (confirm('Are you sure you want to ban this user?')) {
        $.ajax({
            url: `/admin/users/${userId}`,
            method: 'PUT',
            data: {
                status: 'banned',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error banning user');
                }
            },
            error: function() {
                alert('Error banning user');
            }
        });
    }
}

function unbanUser(userId) {
    if (confirm('Are you sure you want to unban this user?')) {
        $.ajax({
            url: `/admin/users/${userId}`,
            method: 'PUT',
            data: {
                status: 'active',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error unbanning user');
                }
            },
            error: function() {
                alert('Error unbanning user');
            }
        });
    }
}
</script>
@endpush
