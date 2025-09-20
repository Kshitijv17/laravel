@extends('admin.layout')

@section('subtitle', 'Order Details - ' . $order->order_number)

@section('content')
<div class="container-fluid py-4">
  <!-- Order Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-1">
                <i class="fas fa-receipt me-2"></i>Order {{ $order->order_number }}
              </h4>
              <div class="d-flex align-items-center gap-3">
                <span class="badge bg-{{ $order->status_badge_color }} fs-6">
                  {{ $order->status_display }}
                </span>
                <span class="badge bg-{{ $order->payment_status_badge_color }} fs-6">
                  {{ $order->payment_status_display }}
                </span>
                @if($order->tracking_number)
                  <span class="badge bg-info fs-6">
                    <i class="fas fa-truck me-1"></i>{{ $order->tracking_number }}
                  </span>
                @endif
              </div>
            </div>
            <div>
              <a href="{{ route('super-admin.admin.orders.edit', $order) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i>Edit Order
              </a>
              <a href="{{ route('super-admin.admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Orders
              </a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3">
              <h6 class="text-muted">Order Date</h6>
              <p class="mb-0">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
            <div class="col-md-3">
              <h6 class="text-muted">Total Amount</h6>
              <p class="mb-0 h5 text-success">${{ number_format($order->total_amount, 2) }}</p>
            </div>
            <div class="col-md-3">
              <h6 class="text-muted">Items Count</h6>
              <p class="mb-0">{{ $order->items->count() }} items</p>
            </div>
            <div class="col-md-3">
              <h6 class="text-muted">Payment Method</h6>
              <p class="mb-0">{{ $order->payment_method ?? 'Not specified' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Order Items -->
    <div class="col-lg-8">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Order Items</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Product</th>
                  <th>SKU</th>
                  <th>Quantity</th>
                  <th>Unit Price</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                @foreach($order->items as $item)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        @if($item->product && $item->product->image)
                          <img src="{{ asset('storage/' . $item->product->image) }}" 
                               alt="{{ $item->product_name }}" 
                               class="rounded me-3" 
                               style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                          <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                               style="width: 50px; height: 50px;">
                            <i class="fas fa-image text-muted"></i>
                          </div>
                        @endif
                        <div>
                          <div class="fw-bold">{{ $item->product_name }}</div>
                          @if($item->product_options)
                            <small class="text-muted">
                              @foreach($item->product_options as $key => $value)
                                {{ ucfirst($key) }}: {{ $value }}{{ !$loop->last ? ', ' : '' }}
                              @endforeach
                            </small>
                          @endif
                        </div>
                      </div>
                    </td>
                    <td>
                      <code>{{ $item->product_sku ?? 'N/A' }}</code>
                    </td>
                    <td>
                      <span class="badge bg-primary">{{ $item->quantity }}</span>
                    </td>
                    <td>
                      ${{ number_format($item->unit_price, 2) }}
                    </td>
                    <td>
                      <strong>${{ number_format($item->total_price, 2) }}</strong>
                    </td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot class="table-light">
                <tr>
                  <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                  <td><strong>${{ number_format($order->subtotal, 2) }}</strong></td>
                </tr>
                @if($order->tax_amount > 0)
                  <tr>
                    <td colspan="4" class="text-end">Tax:</td>
                    <td>${{ number_format($order->tax_amount, 2) }}</td>
                  </tr>
                @endif
                @if($order->shipping_amount > 0)
                  <tr>
                    <td colspan="4" class="text-end">Shipping:</td>
                    <td>${{ number_format($order->shipping_amount, 2) }}</td>
                  </tr>
                @endif
                @if($order->discount_amount > 0)
                  <tr>
                    <td colspan="4" class="text-end text-success">Discount:</td>
                    <td class="text-success">-${{ number_format($order->discount_amount, 2) }}</td>
                  </tr>
                @endif
                <tr class="table-dark">
                  <td colspan="4" class="text-end"><strong>Total:</strong></td>
                  <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <!-- Order Timeline -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-history me-2"></i>Order Timeline</h5>
        </div>
        <div class="card-body">
          <div class="timeline">
            <div class="timeline-item {{ $order->created_at ? 'completed' : '' }}">
              <div class="timeline-marker bg-success">
                <i class="fas fa-plus"></i>
              </div>
              <div class="timeline-content">
                <h6 class="mb-1">Order Placed</h6>
                <p class="text-muted mb-0">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
              </div>
            </div>

            <div class="timeline-item {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'completed' : '' }}">
              <div class="timeline-marker {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'bg-info' : 'bg-light' }}">
                <i class="fas fa-cogs"></i>
              </div>
              <div class="timeline-content">
                <h6 class="mb-1">Processing</h6>
                <p class="text-muted mb-0">
                  @if(in_array($order->status, ['processing', 'shipped', 'delivered']))
                    Order is being processed
                  @else
                    Waiting for processing
                  @endif
                </p>
              </div>
            </div>

            <div class="timeline-item {{ in_array($order->status, ['shipped', 'delivered']) ? 'completed' : '' }}">
              <div class="timeline-marker {{ in_array($order->status, ['shipped', 'delivered']) ? 'bg-primary' : 'bg-light' }}">
                <i class="fas fa-truck"></i>
              </div>
              <div class="timeline-content">
                <h6 class="mb-1">Shipped</h6>
                <p class="text-muted mb-0">
                  @if($order->shipped_at)
                    {{ $order->shipped_at->format('M d, Y \a\t g:i A') }}
                    @if($order->tracking_number)
                      <br>Tracking: {{ $order->tracking_number }}
                    @endif
                  @elseif($order->status === 'shipped')
                    Recently shipped
                  @else
                    Waiting for shipment
                  @endif
                </p>
              </div>
            </div>

            <div class="timeline-item {{ $order->status === 'delivered' ? 'completed' : '' }}">
              <div class="timeline-marker {{ $order->status === 'delivered' ? 'bg-success' : 'bg-light' }}">
                <i class="fas fa-check"></i>
              </div>
              <div class="timeline-content">
                <h6 class="mb-1">Delivered</h6>
                <p class="text-muted mb-0">
                  @if($order->delivered_at)
                    {{ $order->delivered_at->format('M d, Y \a\t g:i A') }}
                  @elseif($order->status === 'delivered')
                    Recently delivered
                  @else
                    Waiting for delivery
                  @endif
                </p>
              </div>
            </div>

            @if($order->status === 'cancelled')
              <div class="timeline-item completed">
                <div class="timeline-marker bg-danger">
                  <i class="fas fa-times"></i>
                </div>
                <div class="timeline-content">
                  <h6 class="mb-1">Cancelled</h6>
                  <p class="text-muted mb-0">Order was cancelled</p>
                </div>
              </div>
            @endif

            @if($order->status === 'refunded')
              <div class="timeline-item completed">
                <div class="timeline-marker bg-warning">
                  <i class="fas fa-undo"></i>
                </div>
                <div class="timeline-content">
                  <h6 class="mb-1">Refunded</h6>
                  <p class="text-muted mb-0">Order was refunded</p>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- Order Details Sidebar -->
    <div class="col-lg-4">
      <!-- Customer Information -->
      <div class="card mb-4">
        <div class="card-header">
          <h6 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h6>
        </div>
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3"
                 style="width: 50px; height: 50px; font-size: 20px;">
              {{ strtoupper(substr($order->user->name ?? 'G', 0, 1)) }}
            </div>
            <div>
              <h6 class="mb-0">{{ $order->user->name ?? 'Guest Customer' }}</h6>
              <small class="text-muted">{{ $order->user->email ?? 'No email provided' }}</small>
            </div>
          </div>
          @if($order->user)
            <div class="row text-center">
              <div class="col-6">
                <div class="border-end">
                  <h6 class="mb-0">{{ $order->user->orders()->count() }}</h6>
                  <small class="text-muted">Total Orders</small>
                </div>
              </div>
              <div class="col-6">
                <h6 class="mb-0">${{ number_format($order->user->orders()->sum('total_amount'), 2) }}</h6>
                <small class="text-muted">Total Spent</small>
              </div>
            </div>
          @endif
        </div>
      </div>

      <!-- Shipping Address -->
      @if($order->shipping_address)
        <div class="card mb-4">
          <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-truck me-2"></i>Shipping Address</h6>
          </div>
          <div class="card-body">
            <address class="mb-0">
              @if(isset($order->shipping_address['name']))
                <strong>{{ $order->shipping_address['name'] }}</strong><br>
              @endif
              @if(isset($order->shipping_address['address_line_1']))
                {{ $order->shipping_address['address_line_1'] }}<br>
              @endif
              @if(isset($order->shipping_address['address_line_2']) && $order->shipping_address['address_line_2'])
                {{ $order->shipping_address['address_line_2'] }}<br>
              @endif
              @if(isset($order->shipping_address['city']))
                {{ $order->shipping_address['city'] }},
              @endif
              @if(isset($order->shipping_address['state']))
                {{ $order->shipping_address['state'] }}
              @endif
              @if(isset($order->shipping_address['postal_code']))
                {{ $order->shipping_address['postal_code'] }}<br>
              @endif
              @if(isset($order->shipping_address['country']))
                {{ $order->shipping_address['country'] }}
              @endif
            </address>
          </div>
        </div>
      @endif

      <!-- Billing Address -->
      @if($order->billing_address)
        <div class="card mb-4">
          <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Billing Address</h6>
          </div>
          <div class="card-body">
            <address class="mb-0">
              @if(isset($order->billing_address['name']))
                <strong>{{ $order->billing_address['name'] }}</strong><br>
              @endif
              @if(isset($order->billing_address['address_line_1']))
                {{ $order->billing_address['address_line_1'] }}<br>
              @endif
              @if(isset($order->billing_address['address_line_2']) && $order->billing_address['address_line_2'])
                {{ $order->billing_address['address_line_2'] }}<br>
              @endif
              @if(isset($order->billing_address['city']))
                {{ $order->billing_address['city'] }},
              @endif
              @if(isset($order->billing_address['state']))
                {{ $order->billing_address['state'] }}
              @endif
              @if(isset($order->billing_address['postal_code']))
                {{ $order->billing_address['postal_code'] }}<br>
              @endif
              @if(isset($order->billing_address['country']))
                {{ $order->billing_address['country'] }}
              @endif
            </address>
          </div>
        </div>
      @endif

      <!-- Order Actions -->
      <div class="card mb-4">
        <div class="card-header">
          <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Quick Actions</h6>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <!-- Status Update -->
            <div class="dropdown">
              <button class="btn btn-outline-primary dropdown-toggle w-100" data-bs-toggle="dropdown">
                <i class="fas fa-edit me-2"></i>Update Status
              </button>
              <ul class="dropdown-menu w-100">
                @foreach(\App\Models\Order::getStatuses() as $statusKey => $statusLabel)
                  @if($statusKey !== $order->status)
                    <li>
                      <a class="dropdown-item" href="#" onclick="updateOrderStatus('{{ $statusKey }}')">
                        {{ $statusLabel }}
                      </a>
                    </li>
                  @endif
                @endforeach
              </ul>
            </div>

            <!-- Payment Status Update -->
            <div class="dropdown">
              <button class="btn btn-outline-success dropdown-toggle w-100" data-bs-toggle="dropdown">
                <i class="fas fa-credit-card me-2"></i>Update Payment
              </button>
              <ul class="dropdown-menu w-100">
                @foreach(\App\Models\Order::getPaymentStatuses() as $paymentKey => $paymentLabel)
                  @if($paymentKey !== $order->payment_status)
                    <li>
                      <a class="dropdown-item" href="#" onclick="updatePaymentStatus('{{ $paymentKey }}')">
                        {{ $paymentLabel }}
                      </a>
                    </li>
                  @endif
                @endforeach
              </ul>
            </div>

            @if($order->canBeCancelled())
              <button class="btn btn-outline-warning" onclick="cancelOrder()">
                <i class="fas fa-ban me-2"></i>Cancel Order
              </button>
            @endif

            @if($order->canBeRefunded())
              <button class="btn btn-outline-info" onclick="showRefundModal()">
                <i class="fas fa-undo me-2"></i>Refund Order
              </button>
            @endif

            <button class="btn btn-outline-secondary" onclick="window.print()">
              <i class="fas fa-print me-2"></i>Print Order
            </button>
          </div>
        </div>
      </div>

      <!-- Order Notes -->
      @if($order->notes)
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Order Notes</h6>
          </div>
          <div class="card-body">
            <p class="mb-0">{{ $order->notes }}</p>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-undo me-2"></i>Refund Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="refundForm">
          @csrf
          <div class="mb-3">
            <label for="refund_amount" class="form-label">Refund Amount</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" name="refund_amount" id="refund_amount" 
                     class="form-control" step="0.01" min="0" max="{{ $order->total_amount }}" 
                     value="{{ $order->total_amount }}" required>
            </div>
            <div class="form-text">Maximum refund amount: ${{ number_format($order->total_amount, 2) }}</div>
          </div>
          <div class="mb-3">
            <label for="refund_reason" class="form-label">Refund Reason (Optional)</label>
            <textarea name="refund_reason" id="refund_reason" class="form-control" rows="3"
                      placeholder="Enter reason for refund..."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" onclick="processRefund()">
          <i class="fas fa-undo me-1"></i>Process Refund
        </button>
      </div>
    </div>
  </div>
</div>

<style>
.timeline {
  position: relative;
  padding-left: 30px;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 15px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #dee2e6;
}

.timeline-item {
  position: relative;
  margin-bottom: 30px;
}

.timeline-marker {
  position: absolute;
  left: -22px;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 12px;
}

.timeline-content {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  border-left: 3px solid #dee2e6;
}

.timeline-item.completed .timeline-content {
  border-left-color: #198754;
}

@media print {
  .btn, .card-header, .timeline::before {
    display: none !important;
  }
  
  .card {
    border: none !important;
    box-shadow: none !important;
  }
}
</style>

<script>
async function updateOrderStatus(status) {
  try {
    const response = await fetch(`/admin/orders/{{ $order->id }}/status`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ status: status })
    });

    const result = await response.json();
    
    if (result.success) {
      showAlert('success', result.message);
      setTimeout(() => window.location.reload(), 1000);
    } else {
      showAlert('error', result.message);
    }
  } catch (error) {
    showAlert('error', 'Failed to update order status');
  }
}

async function updatePaymentStatus(paymentStatus) {
  try {
    const response = await fetch(`/admin/orders/{{ $order->id }}/payment-status`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ payment_status: paymentStatus })
    });

    const result = await response.json();
    
    if (result.success) {
      showAlert('success', result.message);
      setTimeout(() => window.location.reload(), 1000);
    } else {
      showAlert('error', result.message);
    }
  } catch (error) {
    showAlert('error', 'Failed to update payment status');
  }
}

async function cancelOrder() {
  if (!confirm('Are you sure you want to cancel this order?')) {
    return;
  }

  try {
    const response = await fetch(`/admin/orders/{{ $order->id }}/cancel`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    });

    const result = await response.json();
    
    if (result.success) {
      showAlert('success', result.message);
      setTimeout(() => window.location.reload(), 1000);
    } else {
      showAlert('error', result.message);
    }
  } catch (error) {
    showAlert('error', 'Failed to cancel order');
  }
}

function showRefundModal() {
  const modal = new bootstrap.Modal(document.getElementById('refundModal'));
  modal.show();
}

async function processRefund() {
  const form = document.getElementById('refundForm');
  const formData = new FormData(form);

  try {
    const response = await fetch(`/admin/orders/{{ $order->id }}/refund`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({
        refund_amount: formData.get('refund_amount'),
        refund_reason: formData.get('refund_reason')
      })
    });

    const result = await response.json();
    
    if (result.success) {
      showAlert('success', result.message);
      bootstrap.Modal.getInstance(document.getElementById('refundModal')).hide();
      setTimeout(() => window.location.reload(), 1000);
    } else {
      showAlert('error', result.message);
    }
  } catch (error) {
    showAlert('error', 'Failed to process refund');
  }
}

function showAlert(type, message) {
  const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
  const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
  
  const alert = document.createElement('div');
  alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
  alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
  alert.innerHTML = `
    <i class="fas ${icon} me-2"></i>${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `;
  
  document.body.appendChild(alert);
  
  setTimeout(() => {
    if (alert.parentNode) {
      alert.remove();
    }
  }, 5000);
}
</script>

@if(!isset($__env->getShared()['__csrf_token']))
<meta name="csrf-token" content="{{ csrf_token() }}">
@endif

@endsection
