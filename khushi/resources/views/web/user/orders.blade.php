@extends('layouts.app')

@section('title', 'My Orders')

@push('styles')
<style>
 .modern-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.25rem 0 2rem; }
 .profile-card { background: rgba(255,255,255,.95); backdrop-filter: blur(20px); border: none; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,.1); overflow: hidden; max-width: 1100px; margin: 0 auto; }
 .profile-header { background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); color:#fff; padding: 1.5rem 2rem; text-align:center; }
 .profile-name { margin:0; font-weight:700; font-size:1.4rem; }
 .profile-email { margin:.2rem 0 0; opacity:.85; }
 .profile-body { padding: 1.25rem 1.25rem 1.5rem; }
 .item-row { display:flex; gap:1rem; align-items:flex-start; }
 .item-img { width:80px; height:80px; border-radius:12px; object-fit:cover; flex:0 0 auto; }
 .item-title { font-weight:600; margin:0 0 .25rem; }
 .item-meta { color:#6b7280; font-size:.9rem; }
 .badge-soft { background:#eef2ff; color:#3730a3; }
 .badge-warn { background:#fff7ed; color:#9a3412; }
 .actions { display:flex; flex-direction:column; gap:.5rem; min-width:160px; }
 @media (max-width: 640px){ .item-row { flex-direction:column; } .actions{ width:100%; flex-direction:row; } }
</style>
@endpush

@section('content')
<div class="modern-container">
  <div class="container">
    <div class="profile-card">
      <div class="profile-header">
        <h2 class="profile-name">My Orders</h2>
        <p class="profile-email">Track and manage your orders</p>
      </div>
      <div class="profile-body">
        @if($orders->count() > 0)
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fw-semibold">{{ $orders->total() }} {{ Str::plural('Order', $orders->total()) }}</div>
            <form action="{{ route('user.orders') }}" method="GET" class="d-flex align-items-center gap-2">
              <label class="small text-muted">Status</label>
              <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
            </form>
          </div>

          <div class="list-group">
            @foreach($orders as $order)
              @php
                $itemsCount = $order->items->sum('quantity');
                $firstItem = $order->items->first();
                $status = strtolower($order->status);
                $badgeClass = $status === 'delivered' ? 'bg-success' : ($status === 'cancelled' ? 'bg-danger' : 'badge-soft');
              @endphp
              <div class="list-group-item py-3 order-item" data-order-id="{{ $order->id }}">
                <div class="item-row">
                  @if($firstItem && $firstItem->product)
                    <img src="{{ $firstItem->product->primary_image ?? asset('images/placeholder.jpg') }}" alt="{{ $firstItem->product->name ?? 'Product' }}" class="item-img">
                  @else
                    <div class="item-img d-flex align-items-center justify-content-center bg-light"><i class="fas fa-box text-muted"></i></div>
                  @endif
                  <div class="flex-grow-1">
                    <h3 class="item-title mb-1">Order #{{ $order->order_number }}</h3>
                    <div class="item-meta mb-2">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</div>
                    <div class="d-flex align-items-center gap-3 mb-2">
                      <span class="text-muted small"><i class="fas fa-box-open me-1"></i>{{ $itemsCount }} {{ Str::plural('item', $itemsCount) }}</span>
                      <span class="fw-bold">${{ number_format($order->total_amount, 2) }}</span>
                      <span class="badge {{ $badgeClass }}">{{ ucfirst($order->status) }}</span>
                    </div>
                    @if($order->status === 'shipped' || $order->status === 'delivered')
                      <div class="text-muted small">
                        @if($order->status === 'shipped')
                          <i class="fas fa-truck me-1"></i>
                          Expected delivery: {{ $order->expected_delivery_date ? $order->expected_delivery_date->format('M d, Y') : 'Soon' }}
                        @else
                          <i class="fas fa-check-circle me-1"></i>
                          Delivered on {{ $order->delivered_at ? $order->delivered_at->format('M d, Y') : $order->updated_at->format('M d, Y') }}
                        @endif
                      </div>
                    @endif
                  </div>
                  <div class="actions">
                    <a href="{{ route('user.order-details', $order->id) }}" class="btn btn-primary btn-sm">View Details</a>
                    @if(in_array($order->status, ['pending', 'processing']))
                      <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Cancel this order?')">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">Cancel Order</button>
                      </form>
                    @endif
                    @if($firstItem && $firstItem->product)
                      <a href="{{ route('products.show', $firstItem->product->slug) }}" class="btn btn-light btn-sm">Buy Again</a>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders</div>
            {{ $orders->links() }}
          </div>
        @else
          <div class="text-center py-5">
            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
            <h3 class="fw-semibold mb-2">No orders yet</h3>
            <p class="text-muted mb-3">Looks like you haven't made any purchases yet. Start shopping to see your orders here.</p>
            <a class="btn btn-primary" href="{{ route('products.index') }}"><i class="fas fa-shopping-cart me-2"></i>Start Shopping</a>
          </div>
        @endif

        @if($orders->count() > 0)
          <div class="row g-3 mt-3">
            <div class="col-12 col-md-3">
              <div class="p-3 bg-light rounded-3 border">
                <div class="text-muted small">Total Orders</div>
                <div class="fw-bold fs-5">{{ $orders->total() }}</div>
              </div>
            </div>
            <div class="col-12 col-md-3">
              <div class="p-3 bg-light rounded-3 border">
                <div class="text-muted small">Pending</div>
                <div class="fw-bold text-warning fs-5">{{ $orders->where('status', 'pending')->count() }}</div>
              </div>
            </div>
            <div class="col-12 col-md-3">
              <div class="p-3 bg-light rounded-3 border">
                <div class="text-muted small">Delivered</div>
                <div class="fw-bold text-success fs-5">{{ $orders->where('status', 'delivered')->count() }}</div>
              </div>
            </div>
            <div class="col-12 col-md-3">
              <div class="p-3 bg-light rounded-3 border">
                <div class="text-muted small">Total Spent</div>
                <div class="fw-bold fs-5">${{ number_format($orders->sum('total_amount'), 2) }}</div>
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- JavaScript for Order Actions -->
<script>
function initiateReturn(orderId) {
    // Implement return/exchange logic
    if (confirm('Would you like to initiate a return or exchange for this order?')) {
        window.location.href = `/orders/${orderId}/return`;
    }
}

function trackOrder(orderId) {
    // Implement order tracking
    window.location.href = `/orders/${orderId}/track`;
}

function showNotification(message, type) {
    // Simple notification system
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
