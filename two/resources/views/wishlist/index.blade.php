@extends('layouts.app')

@section('title', 'My Wishlist - LaraShop')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Wishlist</h1>
    
    @if($wishlist && $wishlist->items->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($wishlist->items as $item)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 group">
            <a href="{{ route('products.show', $item->product->slug) }}">
                <div class="aspect-w-1 aspect-h-1 bg-gray-200 relative overflow-hidden">
                    @if($item->product->image)
                    <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" 
                         class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
                    @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                    </div>
                    @endif
                    
                    <!-- Sale Badge -->
                    @if($item->product->sale_price)
                    <span class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 text-xs rounded">
                        -{{ $item->product->discount_percentage }}%
                    </span>
                    @endif
                    
                    <!-- Remove Button -->
                    <button onclick="removeFromWishlist({{ $item->product->id }})" 
                            class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center text-red-500 hover:bg-red-50 transition duration-300 opacity-0 group-hover:opacity-100">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </a>
            
            <div class="p-4">
                <div class="mb-2">
                    <span class="text-xs text-primary-600 font-medium">{{ $item->product->category->name }}</span>
                </div>
                
                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                    <a href="{{ route('products.show', $item->product->slug) }}" class="hover:text-primary-600">
                        {{ $item->product->name }}
                    </a>
                </h3>
                
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                    {{ $item->product->short_description }}
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
                        @if($item->product->sale_price)
                        <span class="text-lg font-bold text-primary-600">${{ number_format($item->product->sale_price, 2) }}</span>
                        <span class="text-sm text-gray-500 line-through">${{ number_format($item->product->price, 2) }}</span>
                        @else
                        <span class="text-lg font-bold text-primary-600">${{ number_format($item->product->price, 2) }}</span>
                        @endif
                    </div>
                    
                    <button onclick="addToCart({{ $item->product->id }})" 
                            @if($item->product->stock_quantity == 0) disabled @endif
                            class="bg-primary-600 text-white px-3 py-2 rounded-md text-sm hover:bg-primary-700 transition duration-300 flex items-center space-x-1 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-cart-plus"></i>
                        <span class="hidden sm:inline">{{ $item->product->stock_quantity > 0 ? 'Add to Cart' : 'Out of Stock' }}</span>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Empty Wishlist -->
    <div class="text-center py-16">
        <div class="w-24 h-24 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
            <i class="fas fa-heart text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Your wishlist is empty</h3>
        <p class="text-gray-600 mb-6">Save items you love to your wishlist</p>
        <a href="{{ route('products.index') }}" 
           class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition duration-300">
            Start Shopping
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

function removeFromWishlist(productId) {
    fetch(`/wishlist/remove/${productId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error removing from wishlist', 'error');
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
