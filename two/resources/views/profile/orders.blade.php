@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
        <p class="text-gray-600 mt-2">View and track your order history</p>
    </div>

    <!-- Orders List -->
    <div class="space-y-6">
        @forelse($orders as $order)
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <!-- Order Header -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Order #{{ $order->order_number }}</h3>
                            <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('F d, Y') }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $order->payment_status === 'refunded' ? 'bg-gray-100 text-gray-800' : '' }}">
                                Payment {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                        <span class="text-lg font-medium text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                        <a href="{{ route('profile.orders.show', $order) }}" 
                           class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 transition duration-300">
                            View Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($order->items->take(3) as $item)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 h-12 w-12">
                            @if($item->product->image)
                                <img class="h-12 w-12 rounded-lg object-cover" src="{{ $item->product->image }}" alt="{{ $item->product->name }}">
                            @else
                                <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-500">Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                    
                    @if($order->items->count() > 3)
                    <div class="flex items-center justify-center text-sm text-gray-500">
                        +{{ $order->items->count() - 3 }} more items
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Actions -->
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                    </div>
                    <div class="flex space-x-3">
                        @if($order->status === 'delivered' && $order->payment_status === 'paid')
                        <button class="text-sm text-primary-600 hover:text-primary-500">
                            Reorder
                        </button>
                        @endif
                        @if(in_array($order->status, ['pending', 'processing']))
                        <button class="text-sm text-red-600 hover:text-red-500">
                            Cancel Order
                        </button>
                        @endif
                        <a href="{{ route('profile.orders.show', $order) }}" 
                           class="text-sm text-gray-600 hover:text-gray-500">
                            Track Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white shadow rounded-lg p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-bag text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No orders yet</h3>
            <p class="text-gray-500 mb-6">You haven't placed any orders yet. Start shopping to see your orders here.</p>
            <a href="{{ route('products.index') }}" 
               class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition duration-300">
                Start Shopping
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="mt-8">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
