@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Wishlist</h1>
            <p class="text-gray-600">Items you've saved for later</p>
        </div>

        @if($wishlistItems->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border">
                <!-- Wishlist Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ $wishlistItems->count() }} {{ Str::plural('Item', $wishlistItems->count()) }}
                        </h2>
                        <div class="flex space-x-2">
                            <button onclick="clearWishlist()" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Clear All
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Wishlist Items -->
                <div class="divide-y divide-gray-200">
                    @foreach($wishlistItems as $item)
                        <div class="p-6 wishlist-item" data-product-id="{{ $item->product->id }}">
                            <div class="flex items-center space-x-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    <img src="{{ $item->product->primary_image }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="w-20 h-20 object-cover rounded-lg">
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">
                                        <a href="{{ route('products.show', $item->product->slug) }}" 
                                           class="hover:text-blue-600 transition duration-200">
                                            {{ $item->product->name }}
                                        </a>
                                    </h3>
                                    
                                    <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                        {{ Str::limit($item->product->description, 100) }}
                                    </p>

                                    <div class="flex items-center space-x-4">
                                        <!-- Price -->
                                        <div class="flex items-center space-x-2">
                                            @if($item->product->sale_price && $item->product->sale_price < $item->product->price)
                                                <span class="text-lg font-bold text-red-600">
                                                    ${{ number_format($item->product->sale_price, 2) }}
                                                </span>
                                                <span class="text-sm text-gray-500 line-through">
                                                    ${{ number_format($item->product->price, 2) }}
                                                </span>
                                            @else
                                                <span class="text-lg font-bold text-gray-900">
                                                    ${{ number_format($item->product->price, 2) }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Stock Status -->
                                        @if($item->product->stock_quantity > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                In Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Out of Stock
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col space-y-2">
                                    @if($item->product->stock_quantity > 0)
                                        <button onclick="addToCart({{ $item->product->id }})" 
                                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200 text-sm font-medium">
                                            Add to Cart
                                        </button>
                                    @endif
                                    
                                    <button onclick="removeFromWishlist({{ $item->product->id }})" 
                                            class="bg-red-100 text-red-700 px-4 py-2 rounded-md hover:bg-red-200 transition duration-200 text-sm font-medium">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Wishlist Footer -->
                <div class="p-6 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-600">
                            Total: {{ $wishlistItems->count() }} {{ Str::plural('item', $wishlistItems->count()) }}
                        </p>
                        <div class="flex space-x-3">
                            <a href="{{ route('home') }}" 
                               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition duration-200 text-sm font-medium">
                                Continue Shopping
                            </a>
                            @if($wishlistItems->where('product.stock_quantity', '>', 0)->count() > 0)
                                <button onclick="addAllToCart()" 
                                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200 text-sm font-medium">
                                    Add All to Cart
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Wishlist -->
            <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                <div class="max-w-md mx-auto">
                    <i class="fas fa-heart text-gray-300 text-6xl mb-4"></i>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Your wishlist is empty</h2>
                    <p class="text-gray-600 mb-6">
                        Start adding items to your wishlist by clicking the heart icon on products you love.
                    </p>
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition duration-200 font-medium">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Start Shopping
                    </a>
                </div>
            </div>
        @endif

        <!-- Recently Viewed Products -->
        @if($wishlistItems->count() > 0)
            <div class="mt-12">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">You might also like</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- This would typically show recommended products -->
                    <div class="text-center text-gray-500 col-span-full py-8">
                        <p>Recommended products would appear here</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- JavaScript for Wishlist Actions -->
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
            // Show success message
            showNotification('Product added to cart!', 'success');
            // Update cart count if you have one
            updateCartCount();
        } else {
            showNotification(data.message || 'Failed to add to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function removeFromWishlist(productId) {
    if (!confirm('Are you sure you want to remove this item from your wishlist?')) {
        return;
    }

    fetch(`/products/${productId}/wishlist/remove`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the item from the page
            document.querySelector(`[data-product-id="${productId}"]`).remove();
            showNotification('Item removed from wishlist', 'success');
            
            // Reload page if no items left
            const remainingItems = document.querySelectorAll('.wishlist-item');
            if (remainingItems.length === 0) {
                location.reload();
            }
        } else {
            showNotification(data.message || 'Failed to remove from wishlist', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function clearWishlist() {
    if (!confirm('Are you sure you want to clear your entire wishlist?')) {
        return;
    }

    // You would implement this endpoint
    fetch('/user/wishlist/clear', {
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
        } else {
            showNotification(data.message || 'Failed to clear wishlist', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function addAllToCart() {
    const inStockItems = document.querySelectorAll('.wishlist-item');
    let addedCount = 0;
    
    inStockItems.forEach(item => {
        const productId = item.dataset.productId;
        // Add logic to check if item is in stock before adding
        addToCart(productId);
        addedCount++;
    });
    
    if (addedCount > 0) {
        showNotification(`${addedCount} items added to cart!`, 'success');
    }
}

function showNotification(message, type) {
    // Simple notification system - you can enhance this
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function updateCartCount() {
    // Update cart count in header if you have one
    // This would typically fetch the current cart count
}
</script>
@endsection
