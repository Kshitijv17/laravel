@extends('layouts.app')

@section('title', 'Order Details')

@push('styles')
<style>
 .modern-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.25rem 0 2rem; }
 .profile-card { background: rgba(255,255,255,.95); backdrop-filter: blur(20px); border: none; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,.1); overflow: hidden; max-width: 1100px; margin: 0 auto; }
 .profile-header { background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); color:#fff; padding: 1.25rem 2rem; }
 .profile-name { margin:0; font-weight:700; font-size:1.2rem; }
 .profile-email { margin:.2rem 0 0; opacity:.9; }
 .profile-body { padding: 1.25rem 1.25rem 1.5rem; }
 .badge-soft { background:#eef2ff; color:#3730a3; }
 .item-row { display:flex; gap:1rem; align-items:flex-start; }
 .item-img { width:72px; height:72px; border-radius:12px; object-fit:cover; flex:0 0 auto; }
 .item-title { font-weight:600; margin:0 0 .25rem; }
 .muted { color:#6b7280; }
 .section-title { font-weight:700; font-size:1rem; margin-bottom:.5rem; }
 .summary { background:#fafafa; border:1px solid #eee; border-radius:12px; padding:1rem; }
</style>
@endpush

@section('content')
<div class="modern-container">
  <div class="container">
    <div class="profile-card">
      <div class="profile-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h2 class="profile-name mb-1">Order #{{ $order->order_number ?? $order->id }}</h2>
          <p class="profile-email mb-0">Placed on {{ optional($order->created_at)->format('M d, Y h:i A') }}</p>
        </div>
        <div>
          <span class="badge {{ strtolower($order->status) === 'completed' ? 'bg-success' : (strtolower($order->status) === 'cancelled' ? 'bg-danger' : 'badge-soft') }} text-uppercase">{{ ucfirst($order->status ?? '—') }}</span>
        </div>
      </div>
      <div class="profile-body">
        <div class="row g-3">
          <div class="col-lg-8">
            <div class="mb-3">
              <div class="section-title">Items</div>
              <div class="list-group">
                @foreach($order->items as $item)
                  <div class="list-group-item py-3">
                    <div class="item-row">
                      <img class="item-img" src="{{ optional($item->product)->primary_image ?? asset('images/placeholder.jpg') }}" alt="{{ optional($item->product)->name ?? 'Product' }}">
                      <div class="flex-grow-1">
                        <h4 class="item-title">
                          @if($item->product)
                            <a class="text-decoration-none" href="{{ route('products.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                          @else
                            {{ $item->name ?? 'Product' }}
                          @endif
                        </h4>
                        <div class="muted small mb-1">Qty: {{ $item->quantity }} • Price: ${{ number_format($item->price, 2) }}</div>
                        <div class="fw-semibold">Subtotal: ${{ number_format(($item->price * $item->quantity), 2) }}</div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="summary mb-3">
              <div class="section-title">Order Summary</div>
              <div class="d-flex justify-content-between small mb-1"><span>Items Total</span><span>${{ number_format($order->items->sum(fn($i)=>$i->price * $i->quantity), 2) }}</span></div>
              @if(isset($order->shipping_amount))
                <div class="d-flex justify-content-between small mb-1"><span>Shipping</span><span>${{ number_format($order->shipping_amount, 2) }}</span></div>
              @endif
              @if(isset($order->tax_amount))
                <div class="d-flex justify-content-between small mb-1"><span>Tax</span><span>${{ number_format($order->tax_amount, 2) }}</span></div>
              @endif
              <hr>
              <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>${{ number_format($order->total_amount, 2) }}</span></div>
            </div>

            <div class="mb-3">
              <div class="section-title">Shipping Address</div>
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
            </div>

            <div class="mb-3">
              <div class="section-title">Payment</div>
              <div class="small">
                <div>Method: <strong>{{ optional($order->payment)->method ?? '—' }}</strong></div>
                <div>Status: <strong>{{ ucfirst(optional($order->payment)->status ?? '—') }}</strong></div>
                @if(optional($order->payment)->transaction_id)
                  <div class="text-muted">Txn: {{ $order->payment->transaction_id }}</div>
                @endif
              </div>
            </div>

            <div class="d-grid gap-2">
              @if(Route::has('orders.track'))
                <a class="btn btn-light btn-sm" href="{{ route('orders.track', $order->order_number) }}"><i class="fas fa-truck me-2"></i>Track Order</a>
              @endif
              @php $firstItem = optional($order->items)->first(); @endphp
              @if($firstItem && $firstItem->product)
                <a class="btn btn-primary btn-sm" href="{{ route('products.show', $firstItem->product->slug) }}">Buy Again</a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
