@extends('layouts.app')

@section('title', 'Products - LaraShop')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Products</h1>
        <p class="text-gray-600 mt-2">Discover our amazing collection of products</p>
    </div>
    
    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap items-center gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-64">
                <input type="text" name="search" placeholder="Search products..." 
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            
            <!-- Category Filter -->
            <div>
                <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Sort -->
            <div>
                <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Sort By</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
            </div>
            
            <!-- Filter Button -->
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition duration-300">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            
            <!-- Clear Filters -->
            @if(request()->hasAny(['search', 'category', 'sort']))
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-primary-600 px-4 py-2">
                <i class="fas fa-times mr-2"></i>Clear
            </a>
            @endif
        </form>
    </div>
    
    <!-- Results Info -->
    <div class="flex justify-between items-center mb-6">
        <p class="text-gray-600">
            Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
        </p>
        
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">View:</span>
            <button class="p-2 text-gray-600 hover:text-primary-600">
                <i class="fas fa-th-large"></i>
            </button>
            <button class="p-2 text-gray-600 hover:text-primary-600">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>
    
    <!-- Products Grid -->
    @if($products->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        @foreach($products as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 group">
            <a href="{{ route('products.show', $product->slug) }}">
                <div class="aspect-w-1 aspect-h-1 bg-gray-200 relative overflow-hidden">
                    @if($product->image)
                    <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                         class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
                    @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                    </div>
                    @endif
                    
                    <!-- Sale Badge -->
                    @if($product->sale_price)
                    <span class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 text-xs rounded">
                        -{{ $product->discount_percentage }}%
                    </span>
                    @endif
                    
                    <!-- Wishlist Button -->
                    @auth
                    <button onclick="toggleWishlist({{ $product->id }})" 
                            class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-red-500 transition duration-300 opacity-0 group-hover:opacity-100">
                        <i class="fas fa-heart text-sm"></i>
                    </button>
                    @endauth
                </div>
            </a>
            
            <div class="p-4">
                <div class="mb-2">
                    <span class="text-xs text-primary-600 font-medium">{{ $product->category->name }}</span>
                </div>
                
                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                    <a href="{{ route('products.show', $product->slug) }}" class="hover:text-primary-600">
                        {{ $product->name }}
                    </a>
                </h3>
                
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                    {{ $product->short_description }}
                </p>
                
                <!-- Rating -->
                <div class="flex items-center mb-3">
                    <div class="flex text-yellow-400">
                        @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-xs"></i>
                        @endfor
                    </div>
                    <span class="text-xs text-gray-500 ml-2">(4.5)</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        @if($product->sale_price)
                        <span class="text-lg font-bold text-primary-600">${{ number_format($product->sale_price, 2) }}</span>
                        <span class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                        @else
                        <span class="text-lg font-bold text-primary-600">${{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    
                    <button onclick="addToCart({{ $product->id }})" 
                            class="bg-primary-600 text-white px-3 py-2 rounded-md text-sm hover:bg-primary-700 transition duration-300 flex items-center space-x-1">
                        <i class="fas fa-cart-plus"></i>
                        <span class="hidden sm:inline">Add</span>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $products->withQueryString()->links() }}
    </div>
    @else
    <!-- No Products Found -->
    <div class="text-center py-16">
        <div class="w-24 h-24 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
            <i class="fas fa-search text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
        <p class="text-gray-600 mb-6">Try adjusting your search or filter criteria</p>
        <a href="{{ route('products.index') }}" 
           class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition duration-300">
            View All Products
        </a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function addToCart(productId) {
    fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cart-count').textContent = data.cart_count;
            showNotification(data.message, 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding product to cart', 'error');
    });
}

function toggleWishlist(productId) {
    fetch(`/wishlist/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success' : 'info');
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating wishlist', 'error');
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : type === 'info' ? 'bg-blue-500' : 'bg-red-500'}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush
