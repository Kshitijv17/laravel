@extends('shopkeeper.layout')

@section('title', 'My Orders')
@section('subtitle', 'Manage orders for your shop')

@section('content')
<div class="container-fluid py-4">
  <!-- Stats Cards -->
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-primary text-white h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Total Orders</h6>
              <h3 class="mb-0">{{ $orders->total() }}</h3>
            </div>
            <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-warning text-white h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Pending</h6>
              <h3 class="mb-0">{{ $orders->where('status', 'pending')->count() }}</h3>
            </div>
            <i class="fas fa-clock fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-info text-white h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Processing</h6>
              <h3 class="mb-0">{{ $orders->where('status', 'processing')->count() }}</h3>
            </div>
            <i class="fas fa-cogs fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-success text-white h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Delivered</h6>
              <h3 class="mb-0">{{ $orders->where('status', 'delivered')->count() }}</h3>
            </div>
            <i class="fas fa-check-circle fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters and Actions -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-md-8">
              <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search orders..." value="{{ request('search') }}">
                <select name="status" class="form-select">
                  <option value="">All Status</option>
                  <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                  <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                  <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                  <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <select name="payment_status" class="form-select">
                  <option value="">All Payments</option>
                  <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                  <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-search"></i>
                </button>
              </form>
            </div>
            <div class="col-md-4 text-end">
              <a href="{{ route('shopkeeper.orders.export') }}" class="btn btn-success">
                <i class="fas fa-download me-1"></i>Export Orders
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Orders Table -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="fas fa-shopping-cart me-2"></i>Orders
            @if($orders->total() > 0)
              <span class="badge bg-primary ms-2">{{ $orders->total() }} orders</span>
            @endif
          </h5>
        </div>
        <div class="card-body p-0">
          @if($orders->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Products</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($orders as $order)
                    <tr>
                      <td>
                        <strong>#{{ $order->order_number ?? $order->id }}</strong>
                      </td>
                      <td>
                        @if($order->user)
                          <div>
                            <strong>{{ $order->user->name }}</strong>
                            <br><small class="text-muted">{{ $order->user->email }}</small>
                          </div>
                        @else
                          <span class="text-muted">Guest Order</span>
                        @endif
                      </td>
                      <td>
                        @if($order->items && $order->items->count() > 0)
                          <div>
                            @foreach($order->items->take(2) as $item)
                              <small class="d-block">{{ $item->product->name ?? 'Product' }} ({{ $item->quantity }})</small>
                            @endforeach
                            @if($order->items->count() > 2)
                              <small class="text-muted">+{{ $order->items->count() - 2 }} more</small>
                            @endif
                          </div>
                        @else
                          <span class="text-muted">No items</span>
                        @endif
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
                        @endphp
                        <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                          {{ ucfirst($order->status) }}
                        </span>
                      </td>
                      <td>
                        @php
                          $paymentColors = [
                            'pending' => 'warning',
                            'paid' => 'success',
                            'failed' => 'danger'
                          ];
                        @endphp
                        <span class="badge bg-{{ $paymentColors[$order->payment_status] ?? 'secondary' }}">
                          {{ ucfirst($order->payment_status) }}
                        </span>
                      </td>
                      <td>
                        <small>{{ $order->created_at->format('M d, Y') }}</small>
                        <br><small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="{{ route('shopkeeper.orders.show', $order) }}" 
                             class="btn btn-sm btn-info" title="View">
                            <i class="fas fa-eye"></i>
                          </a>
                          @if(in_array($order->status, ['pending', 'processing', 'shipped']))
                            <a href="{{ route('shopkeeper.orders.edit', $order) }}" 
                               class="btn btn-sm btn-warning" title="Update Status">
                              <i class="fas fa-edit"></i>
                            </a>
                          @endif
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            
            <!-- Pagination -->
            @if($orders->hasPages())
              <div class="card-footer">
                {{ $orders->appends(request()->query())->links() }}
              </div>
            @endif
          @else
            <div class="text-center py-5">
              <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
              <h4 class="text-muted">No Orders Found</h4>
              @if(request()->hasAny(['search', 'status', 'payment_status']))
                <p class="text-muted">Try adjusting your search criteria.</p>
                <a href="{{ route('shopkeeper.orders.index') }}" class="btn btn-secondary">
                  <i class="fas fa-times me-1"></i>Clear Filters
                </a>
              @else
                <p class="text-muted">Orders will appear here when customers purchase your products.</p>
              @endif
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
