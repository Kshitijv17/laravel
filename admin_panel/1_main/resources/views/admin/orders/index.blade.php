@extends('admin.layout')

@section('subtitle', 'Manage and track all customer orders')

@section('content')
<div class="container-fluid py-4">
  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Total Orders</h6>
              <h3 class="mb-0">{{ number_format($stats['total_orders']) }}</h3>
            </div>
            <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Pending Orders</h6>
              <h3 class="mb-0">{{ number_format($stats['pending_orders']) }}</h3>
            </div>
            <i class="fas fa-clock fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Processing</h6>
              <h3 class="mb-0">{{ number_format($stats['processing_orders']) }}</h3>
            </div>
            <i class="fas fa-cogs fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="card-title mb-0">Total Revenue</h6>
              <h3 class="mb-0">${{ number_format($stats['total_revenue'], 2) }}</h3>
            </div>
            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters and Search -->
  <div class="card mb-4">
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters & Search</h5>
        <div>
          <a href="{{ route('super-admin.admin.orders.export', request()->query()) }}" class="btn btn-success btn-sm me-2">
            <i class="fas fa-download me-1"></i>Export CSV
          </a>
          <button class="btn btn-secondary btn-sm" onclick="clearFilters()">
            <i class="fas fa-times me-1"></i>Clear Filters
          </button>
        </div>
      </div>
    </div>
    <div class="card-body">
      <form method="GET" action="{{ route('super-admin.admin.orders.index') }}" id="filterForm">
        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="search" class="form-label">Search</label>
            <input type="text" name="search" id="search" class="form-control" 
                   value="{{ request('search') }}" 
                   placeholder="Order number, customer name, email...">
          </div>
          <div class="col-md-2 mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
              <option value="">All Statuses</option>
              @foreach(\App\Models\Order::getStatuses() as $key => $label)
                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2 mb-3">
            <label for="payment_status" class="form-label">Payment</label>
            <select name="payment_status" id="payment_status" class="form-control">
              <option value="">All Payments</option>
              @foreach(\App\Models\Order::getPaymentStatuses() as $key => $label)
                <option value="{{ $key }}" {{ request('payment_status') == $key ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2 mb-3">
            <label for="date_from" class="form-label">From Date</label>
            <input type="date" name="date_from" id="date_from" class="form-control" 
                   value="{{ request('date_from') }}">
          </div>
          <div class="col-md-2 mb-3">
            <label for="date_to" class="form-label">To Date</label>
            <input type="date" name="date_to" id="date_to" class="form-control" 
                   value="{{ request('date_to') }}">
          </div>
          <div class="col-md-1 mb-3">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-primary d-block w-100">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Orders Table -->
  <div class="card">
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          <i class="fas fa-list me-2"></i>Orders 
          <span class="badge bg-secondary">{{ $orders->total() }}</span>
        </h5>
        <div class="btn-group" role="group">
          <button class="btn btn-sm btn-outline-primary" onclick="refreshOrders()">
            <i class="fas fa-sync-alt me-1"></i>Refresh
          </button>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      @if($orders->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-dark">
              <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Items</th>
                <th>Total</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($orders as $order)
                <tr id="order-{{ $order->id }}">
                  <td>
                    <div class="fw-bold">{{ $order->order_number }}</div>
                    @if($order->tracking_number)
                      <small class="text-muted">
                        <i class="fas fa-truck me-1"></i>{{ $order->tracking_number }}
                      </small>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-2"
                           style="width: 35px; height: 35px; font-size: 14px;">
                        {{ strtoupper(substr($order->user->name ?? 'G', 0, 1)) }}
                      </div>
                      <div>
                        <div class="fw-bold">{{ $order->user->name ?? 'Guest' }}</div>
                        <small class="text-muted">{{ $order->user->email ?? 'No email' }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="dropdown">
                      <span class="badge bg-{{ $order->status_badge_color }} dropdown-toggle" 
                            data-bs-toggle="dropdown" style="cursor: pointer;">
                        {{ $order->status_display }}
                      </span>
                      <ul class="dropdown-menu">
                        @foreach(\App\Models\Order::getStatuses() as $statusKey => $statusLabel)
                          @if($statusKey !== $order->status)
                            <li>
                              <a class="dropdown-item" href="#" 
                                 onclick="updateOrderStatus({{ $order->id }}, '{{ $statusKey }}')">
                                <span class="badge bg-{{ $order->status_badge_color }} me-2">{{ $statusLabel }}</span>
                              </a>
                            </li>
                          @endif
                        @endforeach
                      </ul>
                    </div>
                  </td>
                  <td>
                    <div class="dropdown">
                      <span class="badge bg-{{ $order->payment_status_badge_color }} dropdown-toggle" 
                            data-bs-toggle="dropdown" style="cursor: pointer;">
                        {{ $order->payment_status_display }}
                      </span>
                      <ul class="dropdown-menu">
                        @foreach(\App\Models\Order::getPaymentStatuses() as $paymentKey => $paymentLabel)
                          @if($paymentKey !== $order->payment_status)
                            <li>
                              <a class="dropdown-item" href="#" 
                                 onclick="updatePaymentStatus({{ $order->id }}, '{{ $paymentKey }}')">
                                <span class="badge bg-{{ $order->payment_status_badge_color }} me-2">{{ $paymentLabel }}</span>
                              </a>
                            </li>
                          @endif
                        @endforeach
                      </ul>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-info">{{ $order->items->count() }} items</span>
                  </td>
                  <td>
                    <div class="fw-bold">${{ number_format($order->total_amount, 2) }}</div>
                    @if($order->currency !== 'USD')
                      <small class="text-muted">{{ $order->currency }}</small>
                    @endif
                  </td>
                  <td>
                    <div>{{ $order->created_at->format('M d, Y') }}</div>
                    <small class="text-muted">{{ $order->created_at->format('g:i A') }}</small>
                  </td>
                  <td>
                    <div class="btn-group" role="group">
                      <a href="{{ route('super-admin.admin.orders.show', $order) }}" class="btn btn-sm btn-info" title="View Order">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="{{ route('super-admin.admin.orders.edit', $order) }}" class="btn btn-sm btn-warning" title="Edit Order">
                        <i class="fas fa-edit"></i>
                      </a>
                      <div class="dropdown">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                          <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                          @if($order->canBeCancelled())
                            <li>
                              <a class="dropdown-item text-warning" href="#" 
                                 onclick="cancelOrder({{ $order->id }})">
                                <i class="fas fa-ban me-2"></i>Cancel Order
                              </a>
                            </li>
                          @endif
                          @if($order->canBeRefunded())
                            <li>
                              <a class="dropdown-item text-info" href="#" 
                                 onclick="showRefundModal({{ $order->id }}, {{ $order->total_amount }})">
                                <i class="fas fa-undo me-2"></i>Refund Order
                              </a>
                            </li>
                          @endif
                          <li><hr class="dropdown-divider"></li>
                          <li>
                            <a class="dropdown-item text-danger" href="#" 
                               onclick="deleteOrder({{ $order->id }}, '{{ $order->order_number }}')">
                              <i class="fas fa-trash me-2"></i>Delete Order
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer">
          {{ $orders->appends(request()->query())->links() }}
        </div>
      @else
        <div class="text-center py-5">
          <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
          <h5 class="text-muted">No Orders Found</h5>
          <p class="text-muted">No orders match your current filters.</p>
          @if(request()->hasAny(['search', 'status', 'payment_status', 'date_from', 'date_to']))
            <button class="btn btn-primary" onclick="clearFilters()">
              <i class="fas fa-times me-1"></i>Clear Filters
            </button>
          @endif
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
                     class="form-control" step="0.01" min="0" required>
            </div>
            <div class="form-text">Maximum refund amount: $<span id="max_refund_amount">0.00</span></div>
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

<script>
let currentOrderId = null;

// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
  const filterInputs = document.querySelectorAll('#filterForm select, #filterForm input[type="date"]');
  filterInputs.forEach(input => {
    input.addEventListener('change', function() {
      document.getElementById('filterForm').submit();
    });
  });

  // Search with debounce
  let searchTimeout;
  document.getElementById('search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      document.getElementById('filterForm').submit();
    }, 500);
  });
});

function clearFilters() {
  window.location.href = '{{ route("super-admin.admin.orders.index") }}';
}

function refreshOrders() {
  window.location.reload();
}

async function updateOrderStatus(orderId, status) {
  try {
    const response = await fetch(`/admin/orders/${orderId}/status`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ status: status })
    });

    const result = await response.json();
    
    if (result.success) {
      // Update the status badge in the table
      const statusBadge = document.querySelector(`#order-${orderId} .badge`);
      statusBadge.className = `badge bg-${result.order.status_badge_color} dropdown-toggle`;
      statusBadge.textContent = result.order.status_display;
      
      showAlert('success', result.message);
    } else {
      showAlert('error', result.message);
    }
  } catch (error) {
    showAlert('error', 'Failed to update order status');
  }
}

async function updatePaymentStatus(orderId, paymentStatus) {
  try {
    const response = await fetch(`/admin/orders/${orderId}/payment-status`, {
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

async function cancelOrder(orderId) {
  if (!confirm('Are you sure you want to cancel this order?')) {
    return;
  }

  try {
    const response = await fetch(`/admin/orders/${orderId}/cancel`, {
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

function showRefundModal(orderId, maxAmount) {
  currentOrderId = orderId;
  document.getElementById('refund_amount').max = maxAmount;
  document.getElementById('refund_amount').value = maxAmount;
  document.getElementById('max_refund_amount').textContent = maxAmount.toFixed(2);
  
  const modal = new bootstrap.Modal(document.getElementById('refundModal'));
  modal.show();
}

async function processRefund() {
  const form = document.getElementById('refundForm');
  const formData = new FormData(form);

  try {
    const response = await fetch(`/admin/orders/${currentOrderId}/refund`, {
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

function deleteOrder(orderId, orderNumber) {
  if (!confirm(`Are you sure you want to delete order ${orderNumber}?\n\nThis action cannot be undone.`)) {
    return;
  }

  const form = document.createElement('form');
  form.method = 'POST';
  form.action = `/admin/orders/${orderId}`;
  
  const csrfToken = document.createElement('input');
  csrfToken.type = 'hidden';
  csrfToken.name = '_token';
  csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
  
  const methodField = document.createElement('input');
  methodField.type = 'hidden';
  methodField.name = '_method';
  methodField.value = 'DELETE';
  
  form.appendChild(csrfToken);
  form.appendChild(methodField);
  document.body.appendChild(form);
  form.submit();
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
