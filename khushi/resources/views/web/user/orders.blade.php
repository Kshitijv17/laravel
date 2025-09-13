@extends('layouts.web')

@section('title', 'My Orders')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            @include('web.user.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Orders</h5>
                        <div class="d-flex">
                            <form action="{{ route('user.orders') }}" method="GET" class="d-flex">
                                <select name="status" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Orders</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @php
                                                $itemsCount = $order->items->sum('quantity');
                                            @endphp
                                            {{ $itemsCount }} {{ Str::plural('item', $itemsCount) }}
                                        </td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $order->status === 'completed' ? 'success' : 
                                                ($order->status === 'processing' ? 'primary' : 
                                                ($order->status === 'shipped' ? 'info' : 
                                                ($order->status === 'cancelled' ? 'danger' : 'warning')))
                                            }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('user.order-details', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                            @if($order->status === 'pending' || $order->status === 'processing')
                                                <form action="{{ route('orders.cancel', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white pt-3">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-shopping-bag fa-4x text-muted"></i>
                            </div>
                            <h5>No orders found</h5>
                            <p class="text-muted">You haven't placed any orders yet.</p>
                            <a href="{{ route('shop') }}" class="btn btn-primary">Start Shopping</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .order-item {
        transition: all 0.2s ease;
    }
    .order-item:hover {
        background-color: #f8f9fa;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
</style>
@endpush
