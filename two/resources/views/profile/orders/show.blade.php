@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
            <p class="text-gray-600 mt-2">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
        </div>
        <a href="{{ route('profile.orders') }}" 
           class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition duration-300">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Orders
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Status -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Status</h3>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                        {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $order->payment_status === 'refunded' ? 'bg-gray-100 text-gray-800' : '' }}">
                        Payment {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                
                @if($order->status === 'shipped')
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-truck mr-2"></i>
                        Your order has been shipped and is on its way to you.
                    </p>
                </div>
                @elseif($order->status === 'delivered')
                <div class="mt-4 p-4 bg-green-50 rounded-lg">
                    <p class="text-sm text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>
                        Your order has been delivered successfully.
                    </p>
                </div>
                @endif
            </div>

            <!-- Order Items -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                            <div class="flex-shrink-0 h-16 w-16">
                                @if($item->product->image)
                                    <img class="h-16 w-16 rounded-lg object-cover" src="{{ $item->product->image }}" alt="{{ $item->product->name }}">
                                @else
                                    <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900">{{ $item->product->name }}</h4>
                                <p class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</p>
                                <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">${{ number_format($item->price, 2) }} each</p>
                                <p class="text-sm text-gray-500">Total: ${{ number_format($item->price * $item->quantity, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Shipping Information</h3>
                <div class="text-sm text-gray-600">
                    <p class="font-medium text-gray-900">{{ $order->user->name }}</p>
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                    <p>{{ $order->shipping_country }}</p>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Order Summary</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900">${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax</span>
                        <span class="text-gray-900">${{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping</span>
                        <span class="text-gray-900">${{ number_format($order->shipping_amount, 2) }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between text-base font-medium">
                            <span class="text-gray-900">Total</span>
                            <span class="text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    @if($order->status === 'delivered' && $order->payment_status === 'paid')
                    <button class="w-full bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 transition duration-300">
                        <i class="fas fa-redo mr-2"></i>
                        Reorder Items
                    </button>
                    @endif
                    
                    @if(in_array($order->status, ['pending', 'processing']))
                    <button class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-300">
                        <i class="fas fa-times mr-2"></i>
                        Cancel Order
                    </button>
                    @endif
                    
                    <button class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                        <i class="fas fa-download mr-2"></i>
                        Download Invoice
                    </button>
                    
                    <a href="{{ route('contact.index') }}" 
                       class="w-full bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition duration-300 text-center block">
                        <i class="fas fa-question-circle mr-2"></i>
                        Contact Support
                    </a>
                </div>
            </div>

            <!-- Order Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Information</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order Number</span>
                        <span class="text-gray-900">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Method</span>
                        <span class="text-gray-900">{{ ucfirst($order->payment_method) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order Date</span>
                        <span class="text-gray-900">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Expected Delivery</span>
                        <span class="text-gray-900">
                            @if($order->status === 'delivered')
                                Delivered
                            @else
                                {{ $order->created_at->addDays(7)->format('M d, Y') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
