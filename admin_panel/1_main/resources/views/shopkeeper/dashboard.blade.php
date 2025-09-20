@extends('shopkeeper.layout')

@section('content')
<div class="container-fluid py-4">
  <!-- Welcome Section -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card bg-gradient-primary text-white">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h2 class="mb-1">Welcome back, {{ auth()->user()->name }}!</h2>
              <p class="mb-0 opacity-75">Manage your shop "{{ $shop->name }}" from this dashboard</p>
            </div>
            <div class="col-md-4 text-end">
              <div class="d-flex align-items-center justify-content-end">
                @if($shop->logo)
                  <img src="{{ asset('storage/' . $shop->logo) }}" alt="{{ $shop->name }}" 
                       class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                @endif
                <div>
                  <h5 class="mb-0">{{ $shop->name }}</h5>
                  <small class="opacity-75">{{ $shop->slug }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Total Products</h6>
              <h3 class="mb-0">{{ number_format($stats['total_products']) }}</h3>
              <small class="opacity-75">{{ $stats['active_products'] }} active</small>
            </div>
            <i class="fas fa-box fa-2x opacity-75"></i>
          </div>
        </div>
        <div class="card-footer bg-primary border-0">
          <a href="{{ route('shopkeeper.products.index') }}" class="text-white text-decoration-none">
            <small><i class="fas fa-arrow-right me-1"></i>Manage Products</small>
          </a>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Total Orders</h6>
              <h3 class="mb-0">{{ number_format($stats['total_orders']) }}</h3>
              <small class="opacity-75">{{ $stats['pending_orders'] }} pending</small>
            </div>
            <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
          </div>
        </div>
        <div class="card-footer bg-success border-0">
          <a href="{{ route('shopkeeper.orders.index') }}" class="text-white text-decoration-none">
            <small><i class="fas fa-arrow-right me-1"></i>Manage Orders</small>
          </a>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Total Revenue</h6>
              <h3 class="mb-0">${{ number_format($stats['total_revenue'], 2) }}</h3>
              <small class="opacity-75">${{ number_format($stats['pending_revenue'], 2) }} pending</small>
            </div>
            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
          </div>
        </div>
        <div class="card-footer bg-warning border-0">
          <a href="{{ route('shopkeeper.orders.index', ['payment_status' => 'paid']) }}" class="text-white text-decoration-none">
            <small><i class="fas fa-arrow-right me-1"></i>View Revenue</small>
          </a>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Processing Orders</h6>
              <h3 class="mb-0">{{ number_format($stats['processing_orders']) }}</h3>
              <small class="opacity-75">{{ $stats['shipped_orders'] }} shipped</small>
            </div>
            <i class="fas fa-cogs fa-2x opacity-75"></i>
          </div>
        </div>
        <div class="card-footer bg-info border-0">
          <a href="{{ route('shopkeeper.orders.index', ['status' => 'processing']) }}" class="text-white text-decoration-none">
            <small><i class="fas fa-arrow-right me-1"></i>Process Orders</small>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Orders</h5>
            <a href="{{ route('shopkeeper.orders.index') }}" class="btn btn-sm btn-primary">
              <i class="fas fa-eye me-1"></i>View All
            </a>
          </div>
        </div>
        <div class="card-body p-0">
          @if($recentOrders->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($recentOrders as $order)
                    <tr>
                      <td>
                        <strong>{{ $order->order_number }}</strong>
                      </td>
                      <td>
                        <div>
                          <div class="fw-bold">{{ $order->user->name ?? 'Guest' }}</div>
                          <small class="text-muted">{{ $order->user->email ?? 'No email' }}</small>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-{{ $order->status_badge_color }}">
                          {{ $order->status_display }}
                        </span>
                      </td>
                      <td>
                        <strong>${{ number_format($order->total_amount, 2) }}</strong>
                      </td>
                      <td>
                        <small>{{ $order->created_at->format('M d, Y') }}</small>
                      </td>
                      <td>
                        <a href="{{ route('shopkeeper.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                          <i class="fas fa-eye"></i>
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-4">
              <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
              <h6 class="text-muted">No Recent Orders</h6>
              <p class="text-muted">Orders will appear here once customers start purchasing your products.</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Low Stock Alert & Quick Actions -->
    <div class="col-lg-4">
      <!-- Low Stock Products -->
      <div class="card mb-4">
        <div class="card-header">
          <h6 class="mb-0"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Low Stock Alert</h6>
        </div>
        <div class="card-body">
          @if($lowStockProducts->count() > 0)
            @foreach($lowStockProducts as $product)
              <div class="d-flex justify-content-between align-items-center mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                <div class="flex-grow-1">
                  <div class="fw-bold">{{ $product->title }}</div>
                  <small class="text-muted">{{ $product->category->name ?? 'No Category' }}</small>
                </div>
                <div class="text-end">
                  <span class="badge bg-{{ $product->quantity == 0 ? 'danger' : 'warning' }}">
                    {{ $product->quantity }} left
                  </span>
                </div>
              </div>
            @endforeach
            <div class="text-center mt-3">
              <a href="{{ route('shopkeeper.products.index', ['stock' => 'low']) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-boxes me-1"></i>Manage Stock
              </a>
            </div>
          @else
            <div class="text-center">
              <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
              <p class="text-muted mb-0">All products are well stocked!</p>
            </div>
          @endif
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="{{ route('shopkeeper.products.create') }}" class="btn btn-primary">
              <i class="fas fa-plus me-2"></i>Add New Product
            </a>
            <a href="{{ route('shopkeeper.orders.index', ['status' => 'pending']) }}" class="btn btn-warning">
              <i class="fas fa-clock me-2"></i>Process Pending Orders
            </a>
            <a href="{{ route('shopkeeper.shop.edit') }}" class="btn btn-info">
              <i class="fas fa-store me-2"></i>Edit Shop Details
            </a>
            <a href="{{ route('shopkeeper.orders.export') }}" class="btn btn-success">
              <i class="fas fa-download me-2"></i>Export Orders
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.bg-gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card-hover:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}
</style>
@endsection
