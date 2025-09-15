@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title mb-1">User Details</h1>
                <p class="page-subtitle mb-0">View user information and activity</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Users
                </a>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit User
                </a>
            </div>
        </div>
        
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">{{ $user->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <!-- User Information Card -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 fw-bold">User Information</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" 
                             class="rounded-circle" width="80" height="80" alt="Avatar">
                        @else
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-user text-white fs-2"></i>
                        </div>
                        @endif
                    </div>
                    
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    <div class="mb-3">
                        @php
                            $statusColors = [
                                'active' => 'success',
                                'inactive' => 'warning',
                                'banned' => 'danger'
                            ];
                            $color = $statusColors[$user->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }} px-3 py-2">{{ ucfirst($user->status) }}</span>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-primary fs-4">{{ $user->orders->count() }}</div>
                            <small class="text-muted">Total Orders</small>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-success fs-4">${{ number_format($user->orders->sum('total_amount'), 2) }}</div>
                            <small class="text-muted">Total Spent</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 fw-bold">Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">EMAIL</label>
                        <div>{{ $user->email }}</div>
                        @if($user->email_verified_at)
                            <small class="text-success"><i class="fas fa-check-circle me-1"></i>Verified</small>
                        @else
                            <small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Not Verified</small>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">PHONE</label>
                        <div>{{ $user->phone ?: 'Not provided' }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-semibold text-muted small">JOINED</label>
                        <div>{{ $user->created_at->format('M d, Y') }}</div>
                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                    </div>
                    
                    <div>
                        <label class="fw-semibold text-muted small">LAST ACTIVITY</label>
                        <div>{{ $user->updated_at->format('M d, Y h:i A') }}</div>
                        <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Orders and Activity -->
        <div class="col-lg-8">
            <!-- Recent Orders -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 fw-bold">Recent Orders</h5>
                </div>
                <div class="card-body p-0">
                    @if($user->orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">Order ID</th>
                                    <th class="border-0">Date</th>
                                    <th class="border-0">Items</th>
                                    <th class="border-0">Total</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->orders->take(10) as $order)
                                <tr>
                                    <td class="fw-bold text-primary">#{{ $order->id }}</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>{{ $order->items->count() }} items</td>
                                    <td class="fw-bold text-success">${{ number_format($order->total_amount, 2) }}</td>
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
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Orders Yet</h5>
                        <p class="text-muted mb-0">This user hasn't placed any orders.</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Addresses -->
            @if($user->addresses && $user->addresses->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 fw-bold">Addresses</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($user->addresses as $address)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">{{ $address->type ?? 'Address' }}</h6>
                                    @if($address->is_default)
                                        <span class="badge bg-primary">Default</span>
                                    @endif
                                </div>
                                <p class="mb-0 text-muted">
                                    {{ $address->address_line_1 }}<br>
                                    @if($address->address_line_2)
                                        {{ $address->address_line_2 }}<br>
                                    @endif
                                    {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                    {{ $address->country }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Wishlist -->
            @if($user->wishlist && $user->wishlist->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 fw-bold">Wishlist Items</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($user->wishlist->take(6) as $item)
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-1">{{ $item->product->name }}</h6>
                                    <p class="card-text text-muted small mb-2">{{ Str::limit($item->product->description, 50) }}</p>
                                    <div class="fw-bold text-success">${{ number_format($item->product->price, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 12px;
    }
    
    .table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
        font-weight: 600;
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .row.text-center .col-6 {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush
