@extends('shopkeeper.layout')

@section('page-title', 'Products')

@section('content')
<div class="mb-6">
    <h3 class="text-2xl font-bold text-gray-900 mb-2">Manage Your Products</h3>
    <p class="text-gray-600">Add, edit, or delete herbal products.</p>
</div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="relative flex-1 max-w-md">
        <span class="material-symbols-outlined absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">search</span>
        <input type="text" placeholder="Search products..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
    </div>
    <a href="{{ route('shopkeeper.products.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors duration-200">
        <span class="material-symbols-outlined">add</span>
        Add Product
    </a>
</div>

<!-- Products Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @if(isset($products) && $products->count() > 0)
                    @foreach($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-green-600">spa</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->title ?? 'Calming Chamomile Tea' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $product->category->title ?? 'Teas' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $quantity = $product->quantity ?? 120;
                                    $stockClass = $quantity > 50 ? 'bg-green-100 text-green-800' : ($quantity > 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                    $stockText = $quantity > 50 ? 'In Stock' : ($quantity > 15 ? 'Low Stock' : 'Out of Stock');
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $stockClass }}">
                                    {{ $stockText }} ({{ $quantity }})
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($product->price ?? 12.50, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button class="text-green-600 hover:text-green-900 p-1 rounded">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900 p-1 rounded">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <!-- Sample data matching your image -->
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-green-600">spa</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Calming Chamomile Tea</div>
                                    <div class="text-sm text-gray-500">Organic, loose-leaf</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Teas</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                In Stock (120)
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $12.50
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-green-600 hover:text-green-900 p-1 rounded">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </button>
                                <button class="text-red-600 hover:text-red-900 p-1 rounded">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-yellow-600">local_florist</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Lavender Essential Oil</div>
                                    <div class="text-sm text-gray-500">100% Pure, therapeutic grade</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Oils</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                In Stock (85)
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $24.99
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-green-600 hover:text-green-900 p-1 rounded">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </button>
                                <button class="text-red-600 hover:text-red-900 p-1 rounded">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-amber-100 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-amber-600">grass</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Echinacea Tincture</div>
                                    <div class="text-sm text-gray-500">Immune support formula</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Tinctures</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Low Stock (15)
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $18.00
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-green-600 hover:text-green-900 p-1 rounded">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </button>
                                <button class="text-red-600 hover:text-red-900 p-1 rounded">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-green-600">eco</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Soothing Aloe Vera Gel</div>
                                    <div class="text-sm text-gray-500">For skin irritation</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Topicals</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Out of Stock
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $9.75
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-green-600 hover:text-green-900 p-1 rounded">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </button>
                                <button class="text-red-600 hover:text-red-900 p-1 rounded">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
