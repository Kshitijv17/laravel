@extends('layouts.app')

@section('title', 'Dashboard - E-Commerce Store')

@section('content')
<div class="modern-container">
  <div class="container">
    <div class="profile-card">
      <div class="profile-header text-center">
        <h2 class="profile-name mb-0">Welcome back, {{ auth()->user()->name }}!</h2>
        <p class="profile-email mb-0">Here’s a quick look at your account</p>
      </div>
      <div class="profile-body">

    <!-- Dashboard Stats -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['total_orders'] }}</h3>
                            <p class="mb-0">Total Orders</p>
                        </div>
                        <i class="fas fa-shopping-bag fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">${{ number_format($stats['total_spent'], 2) }}</h3>
                            <p class="mb-0">Total Spent</p>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['wishlist_items'] }}</h3>
                            <p class="mb-0">Wishlist Items</p>
                        </div>
                        <i class="fas fa-heart fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['addresses'] }}</h3>
                            <p class="mb-0">Saved Addresses</p>
                        </div>
                        <i class="fas fa-map-marker-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.orders') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>View Orders
                        </a>
                        <a href="{{ route('user.wishlist') }}" class="btn btn-outline-danger">
                            <i class="fas fa-heart me-2"></i>My Wishlist
                        </a>
                        <a href="{{ route('user.addresses') }}" class="btn btn-outline-info">
                            <i class="fas fa-map-marker-alt me-2"></i>Manage Addresses
                        </a>
                        <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-user me-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('user.support') }}" class="btn btn-outline-warning">
                            <i class="fas fa-headset me-2"></i>Support Tickets
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Recent Orders</h5>
                    <a href="{{ route('user.orders') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('user.order-details', $order->id) }}" class="text-decoration-none">
                                            #{{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $order->status === 'completed' ? 'success' : 
                                            ($order->status === 'cancelled' ? 'danger' : 'warning') 
                                        }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <a href="{{ route('orders.track', ['order_number' => $order->order_number]) }}" 
                                           class="btn btn-sm btn-outline-info">Track</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h5>No orders yet</h5>
                        <p class="text-muted">Start shopping to see your orders here</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">Shop Now</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Wishlist Preview -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-heart me-2"></i>Wishlist</h5>
                    <a href="{{ route('user.wishlist') }}" class="btn btn-sm btn-outline-danger">View All</a>
                </div>
                <div class="card-body">
                    @if($wishlistItems->count() > 0)
                    <div class="row g-3">
                        @foreach($wishlistItems->take(4) as $item)
                        <div class="col-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $item->product->images->first()->url ?? 'https://via.placeholder.com/40x40/f8f9fa/6c757d?text=Product' }}" 
                                             alt="{{ $item->product->name }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 small">{{ Str::limit($item->product->name, 25) }}</h6>
                                            <small class="text-primary">${{ number_format($item->product->price, 2) }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="fas fa-heart fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No items in wishlist</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Account Info -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 text-center">
                            @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="rounded-circle mb-2" style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                                <i class="fas fa-user fa-2x text-white"></i>
                            </div>
                            @endif
                        </div>
                        <div class="col-8">
                            <h6 class="mb-1">{{ auth()->user()->name }}</h6>
                            <p class="text-muted mb-1">{{ auth()->user()->email }}</p>
                            <p class="text-muted mb-2">{{ auth()->user()->phone ?? 'No phone number' }}</p>
                            <a href="{{ route('user.profile') }}" class="btn btn-sm btn-outline-primary">Edit Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Activity</h5>
                </div>
                <div class="card-body">
                    @if($recentActivity->count() > 0)
                    <div class="timeline">
                        @foreach($recentActivity as $activity)
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-{{ $activity->icon ?? 'circle' }} text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $activity->description }}</h6>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No recent activity</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
 .modern-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.25rem 0 2rem; }
 .profile-card { background: rgba(255,255,255,.95); backdrop-filter: blur(20px); border: none; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,.1); overflow: hidden; max-width: 1200px; margin: 0 auto; }
 .profile-header { background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); color:#fff; padding: 1.25rem 2rem; }
 .profile-name { margin:0; font-weight:800; font-size:1.25rem; }
 .profile-email { margin:.2rem 0 0; opacity:.9; }
 .profile-body { padding: 1.25rem 1.25rem 1.5rem; }
 .timeline {
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}

.opacity-75 {
    opacity: 0.75;
}
</style>
@endpush
