@extends('layouts.app')

@section('title', 'LaraShop - Modern Ecommerce Store')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-primary-600 to-primary-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Welcome to LaraShop
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-primary-100">
                Discover amazing products at unbeatable prices
            </p>
            <a href="{{ route('products.index') }}" 
               class="bg-white text-primary-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition duration-300">
                Shop Now
            </a>
        </div>
    </div>
</section>

<!-- Featured Categories -->
@if($categories->count() > 0)
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Shop by Category</h2>
            <p class="text-gray-600">Explore our wide range of product categories</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @foreach($categories as $category)
            <a href="{{ route('category.show', $category->slug) }}" 
               class="group text-center p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300">
                <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-full flex items-center justify-center group-hover:bg-primary-200 transition duration-300">
                    <i class="fas fa-tag text-primary-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 transition duration-300">
                    {{ $category->name }}
                </h3>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<section class="py-16 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Products</h2>
            <p class="text-gray-600">Hand-picked products just for you</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <a href="{{ route('products.show', $product->slug) }}">
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                        @if($product->image)
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                             class="w-full h-48 object-cover">
                        @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                        </div>
                        @endif
                    </div>
                </a>
                
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                        <a href="{{ route('products.show', $product->slug) }}" class="hover:text-primary-600">
                            {{ $product->name }}
                        </a>
                    </h3>
                    
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                        {{ $product->short_description }}
                    </p>
                    
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
                                class="bg-primary-600 text-white px-3 py-1 rounded-md text-sm hover:bg-primary-700 transition duration-300">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-8">
            <a href="{{ route('products.index') }}" 
               class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition duration-300">
                View All Products
            </a>
        </div>
    </div>
</section>
@endif

<!-- New Products -->
@if($newProducts->count() > 0)
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">New Arrivals</h2>
            <p class="text-gray-600">Check out our latest products</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($newProducts as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <a href="{{ route('products.show', $product->slug) }}">
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200 relative">
                        @if($product->image)
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                             class="w-full h-48 object-cover">
                        @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                        </div>
                        @endif
                        <span class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 text-xs rounded">New</span>
                    </div>
                </a>
                
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                        <a href="{{ route('products.show', $product->slug) }}" class="hover:text-primary-600">
                            {{ $product->name }}
                        </a>
                    </h3>
                    
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                        {{ $product->short_description }}
                    </p>
                    
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
                                class="bg-primary-600 text-white px-3 py-1 rounded-md text-sm hover:bg-primary-700 transition duration-300">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Features Section -->
<section class="py-16 bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-primary-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-shipping-fast text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Free Shipping</h3>
                <p class="text-gray-400">Free shipping on orders over $50</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-primary-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-undo text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Easy Returns</h3>
                <p class="text-gray-400">30-day return policy</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-primary-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-headset text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">24/7 Support</h3>
                <p class="text-gray-400">Customer support available 24/7</p>
            </div>
        </div>
    </div>
</section>
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
            // Update cart count
            document.getElementById('cart-count').textContent = data.cart_count;
            
            // Show success message
            showNotification(data.message, 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding product to cart', 'error');
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush
