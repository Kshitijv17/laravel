@extends('shopkeeper.layout')

@section('page-title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h3 class="text-3xl font-bold font-serif text-[var(--heading-light)] dark:text-[var(--heading-dark)] mb-2">Welcome back, {{ auth()->user()->name }}!</h3>
    <p class="text-[var(--text-light)] dark:text-[var(--text-dark)]">Here's what's happening with your {{ auth()->user()->shop->name ?? 'shop' }} today.</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-[var(--card-light)] dark:bg-[var(--card-dark)] p-6 rounded-lg shadow-md border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-semibold text-[var(--heading-light)] dark:text-[var(--heading-dark)]">Total Products</h4>
            <span class="material-symbols-outlined text-green-500 text-3xl">inventory_2</span>
        </div>
        <p class="text-3xl font-bold text-[var(--heading-light)] dark:text-[var(--heading-dark)] mt-2">{{ $stats['total_products'] ?? 0 }}</p>
        <p class="text-sm text-green-600 dark:text-green-400">{{ $stats['active_products'] ?? 0 }} active products</p>
    </div>
    
    <div class="bg-[var(--card-light)] dark:bg-[var(--card-dark)] p-6 rounded-lg shadow-md border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-semibold text-[var(--heading-light)] dark:text-[var(--heading-dark)]">Total Orders</h4>
            <span class="material-symbols-outlined text-blue-500 text-3xl">receipt_long</span>
        </div>
        <p class="text-3xl font-bold text-[var(--heading-light)] dark:text-[var(--heading-dark)] mt-2">{{ $stats['total_orders'] ?? 0 }}</p>
        <p class="text-sm text-blue-600 dark:text-blue-400">{{ $stats['pending_orders'] ?? 0 }} pending orders</p>
    </div>
    
    <div class="bg-[var(--card-light)] dark:bg-[var(--card-dark)] p-6 rounded-lg shadow-md border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-semibold text-[var(--heading-light)] dark:text-[var(--heading-dark)]">Revenue</h4>
            <span class="material-symbols-outlined text-yellow-500 text-3xl">payments</span>
        </div>
        <p class="text-3xl font-bold text-[var(--heading-light)] dark:text-[var(--heading-dark)] mt-2">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
        <p class="text-sm text-yellow-600 dark:text-yellow-400">This month</p>
    </div>
    
    <div class="bg-[var(--card-light)] dark:bg-[var(--card-dark)] p-6 rounded-lg shadow-md border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <h4 class="text-lg font-semibold text-[var(--heading-light)] dark:text-[var(--heading-dark)]">Low Stock</h4>
            <span class="material-symbols-outlined text-purple-500 text-3xl">warning</span>
        </div>
        <p class="text-3xl font-bold text-[var(--heading-light)] dark:text-[var(--heading-dark)] mt-2">{{ $stats['low_stock_products'] ?? 0 }}</p>
        <p class="text-sm text-purple-600 dark:text-purple-400">Items need restocking</p>
    </div>
</div>
<!-- Recent Orders Section -->
<div class="bg-[var(--card-light)] dark:bg-[var(--card-dark)] p-6 rounded-lg shadow-md" style="background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAQAAADa613fAAAAAXNSR0IArs4c6QAAADhlWElmTU0AKgAAAAgAAYdpAAQAAAABAAAAGgAAAAAAAqACAAQAAAABAAAAZKADAAQAAAABAAAAZAAAAAAvu95hAAABUUlEQVR42u3WMQ7DIAwEUS4iUTe4/w18A/kPpSBQEYkfiyRpIeX1uM7O5fP5/C57r2PpfD5/9n2I3++2r4+P+/v7m23b/n4/Lp/P5/P5v5zP5/P5/C/nc/n8ZfJ8Pl+Gz+fz+Xw+n8/n8/l8Pp/P5/P5fD4/vwm/n8/n8/l8Pp/P5/P5fD6fz+fz+Xw+n8/n8/l8Pp/P5/P5fD6fz+fz+Xz+P8Pn8/l8Pp/P5/P5fD6fz+fz+Xw+n8/n8/l8Pp/P5/P5fD6fz+fz+Xw+n8/n8/l8Pp/P5/P5fD6fz+fz+Xw+n8/n8/l8Pp/P5/P5fD6fz+fz+fwt+Hw+n8/n8/l8Pp/P5/P5fD6fz+fz+Xw+n8/n8/l8Pp/P5/P5fD6fz+fz+fwW/L4EHy/fM3x+P5/P5/P5fD6fz+fz+Xw+n8/n8/l8Pp/P5/P5fD6fz+fz+fz+Fz/3P5/P5/P5/L/k/A0P+woYQd43YgAAAABJRU5ErkJggg=='); background-repeat: repeat; background-size: 20px;">
    <h4 class="text-xl font-semibold font-serif text-[var(--heading-light)] dark:text-[var(--heading-dark)] mb-4">Recent Orders</h4>
    <div class="overflow-x-auto">
        @if(isset($recentOrders) && $recentOrders->count() > 0)
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b-2 border-dashed border-[var(--border-light)] dark:border-[var(--border-dark)]">
                        <th class="py-3 px-4 font-semibold text-[var(--heading-light)] dark:text-[var(--heading-dark)]">Customer</th>
                        <th class="py-3 px-4 font-semibold text-[var(--heading-light)] dark:text-[var(--heading-dark)]">Status</th>
                        <th class="py-3 px-4 font-semibold text-[var(--heading-light)] dark:text-[var(--heading-dark)]">Date</th>
                        <th class="py-3 px-4 font-semibold text-[var(--heading-light)] dark:text-[var(--heading-dark)] text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                        <tr class="border-b border-[var(--border-light)] dark:border-[var(--border-dark)] hover:bg-[var(--primary)] dark:hover:bg-[var(--background-dark)] transition-colors duration-200">
                            <td class="py-3 px-4 flex items-center">
                                <span class="material-symbols-outlined text-green-600 mr-3">person</span>
                                <div>
                                    <p class="font-medium text-[var(--heading-light)] dark:text-[var(--heading-dark)]">{{ $order->customer_name }}</p>
                                    <p class="text-sm text-[var(--text-light)] dark:text-[var(--text-dark)]">{{ $order->customer_email }}</p>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($order->status === 'delivered') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100
                                    @elseif($order->status === 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100
                                    @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-[var(--text-light)] dark:text-[var(--text-dark)]">{{ $order->created_at->format('Y-m-d') }}</td>
                            <td class="py-3 px-4 text-right font-medium text-[var(--heading-light)] dark:text-[var(--heading-dark)]">${{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-8">
                <span class="material-symbols-outlined text-6xl text-[var(--text-light)] dark:text-[var(--text-dark)] mb-4">receipt_long</span>
                <p class="text-[var(--text-light)] dark:text-[var(--text-dark)]">No orders yet</p>
                <p class="text-sm text-[var(--text-light)] dark:text-[var(--text-dark)]">Orders will appear here once customers start purchasing</p>
            </div>
        @endif
    </div>
</div>
@endsection
