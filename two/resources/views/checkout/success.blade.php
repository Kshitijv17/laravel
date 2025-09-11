@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="text-center mb-8">
            <div class="text-green-500 text-6xl mb-4">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Order Placed Successfully!</h1>
            <p class="text-gray-600">Thank you for your order #{{ $order->order_number }}</p>
            <p class="text-gray-600">A confirmation email has been sent to {{ Auth::user()->email }}</p>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <h3 class="font-medium text-gray-700">Order Number</h3>
                    <p class="text-gray-600">{{ $order->order_number }}</p>
                </div>
                <div>
                    <h3 class="font-medium text-gray-700">Date</h3>
                    <p class="text-gray-600">{{ $order->created_at->format('F j, Y') }}</p>
                </div>
                <div>
                    <h3 class="font-medium text-gray-700">Payment Method</h3>
                    <p class="text-gray-600 capitalize">{{ $order->payment_method }}</p>
                </div>
                <div>
                    <h3 class="font-medium text-gray-700">Order Status</h3>
                    <span class="px-2 py-1 text-sm rounded-full 
                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                           ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                           ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="font-medium text-gray-700 mb-2">Shipping Address</h3>
                <p class="text-gray-600">
                    {{ $order->shipping_address }},<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }},<br>
                    {{ $order->shipping_country }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <h3 class="font-medium text-gray-700 mb-2">Order Items</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="{{ $item->product->image ?? 'https://via.placeholder.com/50' }}" alt="{{ $item->product->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->product->sku ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                ${{ number_format($item->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                ${{ number_format($item->price * $item->quantity, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500">Subtotal</td>
                            <td class="px-6 py-3 text-right text-sm font-medium">${{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500">Shipping</td>
                            <td class="px-6 py-3 text-right text-sm font-medium">${{ number_format($order->shipping_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500">Tax</td>
                            <td class="px-6 py-3 text-right text-sm font-medium">${{ number_format($order->tax_amount, 2) }}</td>
                        </tr>
                        <tr class="border-t border-gray-200">
                            <td colspan="3" class="px-6 py-3 text-right text-base font-bold text-gray-900">Total</td>
                            <td class="px-6 py-3 text-right text-base font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-center">
                    <a href="{{ route('products.index') }}" class="w-full sm:w-auto mb-4 sm:mb-0 px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 text-center">
                        Continue Shopping
                    </a>
                    <div class="flex space-x-4">
                        <a href="{{ route('profile.orders.show', $order->id) }}" class="px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            View Order
                        </a>
                        <button onclick="window.print()" class="px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Print Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Clear cart from local storage if it exists
    if (typeof(Storage) !== 'undefined') {
        localStorage.removeItem('cart');
    }
    
    // Update cart count in the header
    document.addEventListener('DOMContentLoaded', function() {
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = '0';
        }
    });
</script>
@endpush
@endsection
