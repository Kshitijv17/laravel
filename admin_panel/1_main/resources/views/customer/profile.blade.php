@extends('customer.layout')

@section('title', 'My Profile')

@section('content')
<div class="container py-5">
    <!-- Profile Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card profile-header">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="profile-avatar">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=120&background=007bff&color=fff" 
                                     class="rounded-circle" alt="Profile Picture" id="profile-image">
                                <div class="avatar-overlay">
                                    <i class="fas fa-camera"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="mb-1">{{ $user->name }}</h2>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $user->is_guest ? 'warning' : 'success' }} me-2">
                                    {{ $user->is_guest ? 'Guest Account' : 'Registered User' }}
                                </span>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Verified
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Unverified
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" onclick="toggleEditMode()">
                                <i class="fas fa-edit me-1"></i>Edit Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <form id="profile-form" method="POST" action="{{ route('customer.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" value="{{ $user->phone ?? '' }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ $user->date_of_birth ?? '' }}" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="3" readonly>{{ $user->address ?? '' }}</textarea>
                        </div>
                        
                        <div class="edit-buttons" style="display: none;">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="fas fa-save me-1"></i>Save Changes
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelEdit()">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Change -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('customer.password.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-1"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order History -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Recent Orders</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary" onclick="alert('Orders feature coming soon!')">View All</a>
                </div>
                <div class="card-body">
                    @if(isset($recentOrders) && $recentOrders->count() > 0)
                        @foreach($recentOrders as $order)
                            <div class="order-item d-flex justify-content-between align-items-center py-3 border-bottom">
                                <div>
                                    <h6 class="mb-1">Order #{{ $order->order_number }}</h6>
                                    <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="mb-1">
                                        <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                    <strong>${{ number_format($order->total_amount, 2) }}</strong>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No orders yet</p>
                            <a href="{{ route('customer.home') }}" class="btn btn-primary">Start Shopping</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Account Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Account Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="stat-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <i class="fas fa-shopping-cart text-primary me-2"></i>
                            <span>Total Orders</span>
                        </div>
                        <strong>{{ $stats['total_orders'] ?? 0 }}</strong>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <i class="fas fa-dollar-sign text-success me-2"></i>
                            <span>Total Spent</span>
                        </div>
                        <strong>${{ number_format($stats['total_spent'] ?? 0, 2) }}</strong>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <i class="fas fa-heart text-danger me-2"></i>
                            <span>Wishlist Items</span>
                        </div>
                        <strong>{{ $stats['wishlist_count'] ?? 0 }}</strong>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <i class="fas fa-calendar text-info me-2"></i>
                            <span>Member Since</span>
                        </div>
                        <strong>{{ $user->created_at->format('M Y') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-primary" onclick="alert('Orders feature coming soon!')">
                            <i class="fas fa-list me-1"></i>View All Orders
                        </a>
                        <a href="#" class="btn btn-outline-danger" onclick="alert('Wishlist feature coming soon!')">
                            <i class="fas fa-heart me-1"></i>My Wishlist
                        </a>
                        <a href="#" class="btn btn-outline-info" onclick="alert('Address management feature coming soon!')">
                            <i class="fas fa-map-marker-alt me-1"></i>Manage Addresses
                        </a>
                        <a href="#" class="btn btn-outline-warning" onclick="alert('Customer support feature coming soon!')">
                            <i class="fas fa-headset me-1"></i>Customer Support
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Security -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Account Security</h5>
                </div>
                <div class="card-body">
                    <div class="security-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <i class="fas fa-envelope me-2"></i>
                            <span>Email Verification</span>
                        </div>
                        @if($user->email_verified_at)
                            <span class="badge bg-success">Verified</span>
                        @else
                            <button class="btn btn-sm btn-warning" onclick="alert('Email verification feature coming soon!')">Verify</button>
                        @endif
                    </div>
                    <div class="security-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <i class="fas fa-mobile-alt me-2"></i>
                            <span>Phone Verification</span>
                        </div>
                        @if($user->phone_verified_at)
                            <span class="badge bg-success">Verified</span>
                        @else
                            <button class="btn btn-sm btn-outline-warning">Verify</button>
                        @endif
                    </div>
                    <div class="security-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <i class="fas fa-key me-2"></i>
                            <span>Two-Factor Auth</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary">Setup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.profile-avatar {
    position: relative;
    display: inline-block;
}

.avatar-overlay {
    position: absolute;
    bottom: 0;
    right: 0;
    background: #007bff;
    color: white;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 3px solid white;
}

.stat-item {
    border-bottom: 1px solid #eee;
}

.stat-item:last-child {
    border-bottom: none;
}

.security-item {
    border-bottom: 1px solid #eee;
}

.security-item:last-child {
    border-bottom: none;
}

.order-item:last-child {
    border-bottom: none !important;
}
</style>

<script>
function toggleEditMode() {
    const form = document.getElementById('profile-form');
    const inputs = form.querySelectorAll('input, textarea');
    const editButtons = form.querySelector('.edit-buttons');
    const editBtn = document.querySelector('button[onclick="toggleEditMode()"]');
    
    inputs.forEach(input => {
        if (input.name !== 'email') { // Keep email readonly
            input.readOnly = !input.readOnly;
        }
    });
    
    if (editButtons.style.display === 'none') {
        editButtons.style.display = 'block';
        editBtn.innerHTML = '<i class="fas fa-times me-1"></i>Cancel';
        editBtn.setAttribute('onclick', 'cancelEdit()');
    }
}

function cancelEdit() {
    location.reload(); // Simple way to reset form
}

// Profile image upload (placeholder)
document.querySelector('.avatar-overlay').addEventListener('click', function() {
    alert('Profile image upload functionality would be implemented here');
});
</script>
@endsection
