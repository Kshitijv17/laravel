@extends('layouts.app')

@section('title', 'Track Order')

@push('styles')
<style>
 .modern-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.25rem 0 2rem; }
 .profile-card { background: rgba(255,255,255,.95); backdrop-filter: blur(20px); border: none; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,.1); overflow: hidden; max-width: 900px; margin: 0 auto; }
 .profile-header { background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); color:#fff; padding: 1.25rem 2rem; text-align:center; }
 .profile-name { margin:0; font-weight:800; font-size:1.25rem; }
 .profile-email { margin:.2rem 0 0; opacity:.9; }
 .profile-body { padding: 1.25rem 1.25rem 1.5rem; }
 .timeline { position: relative; margin-left: .5rem; }
 .timeline::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background-color: #e9ecef; }
 .tl-item { position: relative; padding-left: 2.25rem; margin-bottom: .75rem; }
 .tl-item::before { content: ''; position: absolute; left: 5px; top: .35rem; width: 12px; height: 12px; border-radius: 50%; background: #4285f4; box-shadow: 0 0 0 3px rgba(66,133,244,.15); }
 .badge-soft { background:#eef2ff; color:#3730a3; }
</style>
@endpush

@section('content')
<div class="modern-container">
  <div class="container">
    <div class="profile-card">
      <div class="profile-header">
        <h2 class="profile-name mb-0">Track Your Order</h2>
        <p class="profile-email mb-0">Enter your order number to view the latest status</p>
      </div>
      <div class="profile-body">
        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('orders.track') }}" method="GET" class="row g-2 mb-3">
          <div class="col-12 col-md-8">
            <label class="form-label small" for="order_number">Order Number</label>
            <input type="text" class="form-control" id="order_number" name="order_number" value="{{ request('order_number') }}" placeholder="e.g. ORD-ABC123" required>
          </div>
          <div class="col-12 col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-truck me-2"></i>Track Order</button>
          </div>
        </form>

        @isset($order)
          <div class="mb-3 p-3 border rounded-3 bg-light">
            <div class="d-flex justify-content-between flex-wrap gap-2">
              <div>
                <div class="text-muted small">Order Number</div>
                <div class="fw-bold">#{{ $order->order_number }}</div>
              </div>
              <div>
                <div class="text-muted small">Placed On</div>
                <div class="fw-semibold">{{ optional($order->created_at)->format('M d, Y h:i A') }}</div>
              </div>
              <div>
                <div class="text-muted small">Status</div>
                @php $s = strtolower($order->status); @endphp
                <span class="badge {{ $s === 'delivered' || $s === 'completed' ? 'bg-success' : ($s === 'cancelled' ? 'bg-danger' : 'badge-soft') }}">{{ ucfirst($order->status) }}</span>
              </div>
              <div>
                <div class="text-muted small">Total</div>
                <div class="fw-bold">${{ number_format($order->total_amount, 2) }}</div>
              </div>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-lg-7">
              <div class="section-title fw-bold mb-2">Tracking Updates</div>
              @php $updates = optional($order->tracking)->updates ?? collect(); @endphp
              @if($updates->count() > 0)
                <div class="timeline">
                  @foreach($updates as $update)
                    <div class="tl-item">
                      <div class="fw-semibold">{{ ucfirst($update->status ?? 'update') }}</div>
                      @if(!empty($update->message))
                        <div class="text-muted small">{{ $update->message }}</div>
                      @endif
                      <div class="text-muted small">{{ optional($update->created_at)->format('M d, Y h:i A') }}</div>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="text-muted small">No tracking updates available yet.</div>
              @endif
            </div>
            <div class="col-lg-5">
              <div class="section-title fw-bold mb-2">Shipping Address</div>
              @php $addr = optional($order->addresses)->first(); @endphp
              @if($addr)
                <div class="small">
                  <div class="fw-semibold">{{ $addr->name ?? ($addr->full_name ?? '—') }}</div>
                  <div>{{ $addr->address_line_1 ?? $addr->line1 }}</div>
                  @if(($addr->address_line_2 ?? $addr->line2))<div>{{ $addr->address_line_2 ?? $addr->line2 }}</div>@endif
                  @php $parts = array_filter([$addr->city ?? null, $addr->state ?? null, $addr->postal_code ?? $addr->zip ?? null, $addr->country ?? null]); @endphp
                  @if(count($parts))<div>{{ implode(', ', $parts) }}</div>@endif
                  @if(($addr->phone ?? null))<div>Phone: {{ $addr->phone }}</div>@endif
                </div>
              @else
                <div class="text-muted small">—</div>
              @endif

              <div class="mt-3 d-grid gap-2">
                @auth
                  <a class="btn btn-light btn-sm" href="{{ route('user.order-details', $order->id) }}"><i class="fas fa-receipt me-2"></i>View Order Details</a>
                @endauth
                <a class="btn btn-primary btn-sm" href="{{ route('products.index') }}"><i class="fas fa-shopping-bag me-2"></i>Continue Shopping</a>
              </div>
            </div>
          </div>
        @endisset
      </div>
    </div>
  </div>
</div>
@endsection
