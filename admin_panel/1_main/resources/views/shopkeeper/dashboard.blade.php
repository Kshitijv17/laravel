@extends('shopkeeper.layout')

@section('page-title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h3 class="text-3xl font-bold font-serif text-[var(--heading-light)] dark:text-[var(--heading-dark)] mb-2">Welcome back, {{ auth()->user()->name }}!</h3>
    <p class="text-[var(--text-light)] dark:text-[var(--text-dark)]">Here's what's happening with your {{ auth()->user()->shop->name ?? 'shop' }} today.</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                <span class="material-symbols-outlined text-yellow-500 text-2xl">local_florist</span>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['total_revenue'] ?? 45231.89, 2) }}</p>
        <p class="text-sm text-green-600 mt-1">+20.1% from last month</p>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm font-medium text-gray-600">Subscriptions</p>
                <span class="material-symbols-outlined text-green-500 text-2xl">eco</span>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">+{{ $stats['total_orders'] ?? 2350 }}</p>
        <p class="text-sm text-green-600 mt-1">+180.1% from last month</p>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm font-medium text-gray-600">Sales</p>
                <span class="material-symbols-outlined text-amber-700 text-2xl">potted_plant</span>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">+{{ $stats['total_products'] ?? 12234 }}</p>
        <p class="text-sm text-green-600 mt-1">+19% from last month</p>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm font-medium text-gray-600">Active Now</p>
                <span class="material-symbols-outlined text-lime-600 text-2xl">self_improvement</span>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">+{{ $stats['active_products'] ?? 573 }}</p>
        <p class="text-sm text-gray-600 mt-1">Online</p>
    </div>
</div>
<!-- Recent Orders Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h4 class="text-xl font-semibold text-gray-900 mb-4">Recent Orders</h4>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="py-3 px-4 font-semibold text-gray-700">Customer</th>
                    <th class="py-3 px-4 font-semibold text-gray-700">Status</th>
                    <th class="py-3 px-4 font-semibold text-gray-700">Date</th>
                    <th class="py-3 px-4 font-semibold text-gray-700 text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($recentOrders) && $recentOrders->count() > 0)
                    @foreach($recentOrders as $order)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                            <td class="py-3 px-4 flex items-center">
                                <span class="material-symbols-outlined text-green-600 mr-3">spa</span>
                                <img alt="User avatar" class="w-8 h-8 rounded-full mr-3" src="https://ui-avatars.com/api/?name={{ urlencode($order->customer_name ?? 'Customer') }}&background=68d391&color=fff">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $order->customer_name ?? 'Simran' }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->customer_email ?? 'simran@example.com' }}</p>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if(($order->status ?? 'paid') === 'delivered' || ($order->status ?? 'paid') === 'paid') bg-green-100 text-green-800
                                    @elseif(($order->status ?? 'paid') === 'shipped') bg-blue-100 text-blue-800
                                    @elseif(($order->status ?? 'paid') === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif(($order->status ?? 'paid') === 'cancelled') bg-red-100 text-red-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst($order->status ?? 'Paid') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-600">{{ $order->created_at->format('Y-m-d') ?? '2023-10-26' }}</td>
                            <td class="py-3 px-4 text-right font-medium text-gray-900">${{ number_format($order->total_amount ?? 250.00, 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    <!-- Sample data matching your image -->
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                        <td class="py-3 px-4 flex items-center">
                            <span class="material-symbols-outlined text-green-600 mr-3">spa</span>
                            <img alt="User avatar" class="w-8 h-8 rounded-full mr-3" src="https://ui-avatars.com/api/?name=Simran&background=68d391&color=fff">
                            <div>
                                <p class="font-medium text-gray-900">Simran</p>
                                <p class="text-sm text-gray-500">simran@example.com</p>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                        </td>
                        <td class="py-3 px-4 text-gray-600">2023-10-26</td>
                        <td class="py-3 px-4 text-right font-medium text-gray-900">$250.00</td>
                    </tr>
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                        <td class="py-3 px-4 flex items-center">
                            <span class="material-symbols-outlined text-yellow-600 mr-3">local_florist</span>
                            <img alt="User avatar" class="w-8 h-8 rounded-full mr-3" src="https://ui-avatars.com/api/?name=Aaril+Suri&background=68d391&color=fff">
                            <div>
                                <p class="font-medium text-gray-900">{{ auth()->user()->name ?? 'Aaril Suri' }}</p>
                                <p class="text-sm text-gray-500">{{ auth()->user()->email ?? 'aaril.suri@example.com' }}</p>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Shipped</span>
                        </td>
                        <td class="py-3 px-4 text-gray-600">2023-10-25</td>
                        <td class="py-3 px-4 text-right font-medium text-gray-900">$150.00</td>
                    </tr>
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                        <td class="py-3 px-4 flex items-center">
                            <span class="material-symbols-outlined text-amber-800 mr-3">grass</span>
                            <img alt="User avatar" class="w-8 h-8 rounded-full mr-3" src="https://ui-avatars.com/api/?name=Priya+Sharma&background=68d391&color=fff">
                            <div>
                                <p class="font-medium text-gray-900">Priya Sharma</p>
                                <p class="text-sm text-gray-500">priya.sharma@example.com</p>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        </td>
                        <td class="py-3 px-4 text-gray-600">2023-10-26</td>
                        <td class="py-3 px-4 text-right font-medium text-gray-900">$350.00</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="py-3 px-4 flex items-center">
                            <span class="material-symbols-outlined text-lime-700 mr-3">eco</span>
                            <img alt="User avatar" class="w-8 h-8 rounded-full mr-3" src="https://ui-avatars.com/api/?name=Rohan+Verma&background=68d391&color=fff">
                            <div>
                                <p class="font-medium text-gray-900">Rohan Verma</p>
                                <p class="text-sm text-gray-500">rohan.verma@example.com</p>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Canceled</span>
                        </td>
                        <td class="py-3 px-4 text-gray-600">2023-10-24</td>
                        <td class="py-3 px-4 text-right font-medium text-gray-900">$75.00</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
