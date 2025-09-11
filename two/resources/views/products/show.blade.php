@extends('layouts.app')

@section('title', $product->name . ' - LaraShop')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary-600">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-primary-600">Products</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('category.show', $product->category->slug) }}" class="text-gray-700 hover:text-primary-600">
                        {{ $product->category->name }}
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500">{{ $product->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
        <!-- Product Images -->
        <div class="flex flex-col-reverse">
            <!-- Image Gallery -->
            <div class="hidden mt-6 w-full max-w-2xl mx-auto sm:block lg:max-w-none">
                <div class="grid grid-cols-4 gap-6">
                    @if($product->gallery)
                        @foreach($product->gallery as $image)
                        <button class="relative h-24 bg-white rounded-md flex items-center justify-center text-sm font-medium uppercase text-gray-900 cursor-pointer hover:bg-gray-50 focus:outline-none focus:ring focus:ring-offset-4 focus:ring-primary-500">
                            <img src="{{ $image }}" alt="{{ $product->name }}" class="w-full h-full object-center object-cover rounded-md">
                        </button>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Main Image -->
            <div class="w-full aspect-w-1 aspect-h-1">
                @if($product->image)
                <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                     class="w-full h-96 object-center object-cover sm:rounded-lg">
                @else
                <div class="w-full h-96 bg-gray-200 flex items-center justify-center sm:rounded-lg">
                    <i class="fas fa-image text-gray-400 text-6xl"></i>
                </div>
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">{{ $product->name }}</h1>

            <div class="mt-3">
                <h2 class="sr-only">Product information</h2>
                <div class="flex items-center space-x-3">
                    @if($product->sale_price)
                    <p class="text-3xl text-primary-600 font-bold">${{ number_format($product->sale_price, 2) }}</p>
                    <p class="text-xl text-gray-500 line-through">${{ number_format($product->price, 2) }}</p>
                    <span class="bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded">
                        -{{ $product->discount_percentage }}%
                    </span>
                    @else
                    <p class="text-3xl text-primary-600 font-bold">${{ number_format($product->price, 2) }}</p>
                    @endif
                </div>
            </div>

            <!-- Reviews -->
            <div class="mt-3">
                <h3 class="sr-only">Reviews</h3>
                <div class="flex items-center">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-yellow-400"></i>
                        @endfor
                    </div>
                    <p class="sr-only">4.5 out of 5 stars</p>
                    <a href="#reviews" class="ml-3 text-sm font-medium text-primary-600 hover:text-primary-500">
                        {{ $product->reviews->count() }} reviews
                    </a>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="sr-only">Description</h3>
                <div class="text-base text-gray-700 space-y-6">
                    <p>{{ $product->short_description }}</p>
                </div>
            </div>

            <!-- Product Details -->
            <div class="mt-6 space-y-4">
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-900 w-20">SKU:</span>
                    <span class="text-sm text-gray-700">{{ $product->sku }}</span>
                </div>
                
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-900 w-20">Brand:</span>
                    <span class="text-sm text-gray-700">{{ $product->brand ?? 'N/A' }}</span>
                </div>
                
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-900 w-20">Stock:</span>
                    <span class="text-sm {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $product->stock_quantity > 0 ? $product->stock_quantity . ' in stock' : 'Out of stock' }}
                    </span>
                </div>
            </div>

            <form class="mt-6" x-data="{ quantity: 1 }">
                @csrf
                <!-- Quantity -->
                <div class="flex items-center space-x-3 mb-6">
                    <label for="quantity" class="text-sm font-medium text-gray-900">Quantity:</label>
                    <div class="flex items-center border border-gray-300 rounded-md">
                        <button type="button" @click="quantity = Math.max(1, quantity - 1)" 
                                class="p-2 text-gray-600 hover:text-gray-800">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" x-model="quantity" min="1" max="{{ $product->stock_quantity }}"
                               class="w-16 text-center border-0 focus:ring-0" readonly>
                        <button type="button" @click="quantity = Math.min({{ $product->stock_quantity }}, quantity + 1)" 
                                class="p-2 text-gray-600 hover:text-gray-800">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <!-- Add to Cart -->
                    <button type="button" 
                            onclick="addToCartWithQuantity({{ $product->id }})"
                            @if($product->stock_quantity == 0) disabled @endif
                            class="flex-1 bg-primary-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-cart-plus mr-2"></i>
                        {{ $product->stock_quantity > 0 ? 'Add to Cart' : 'Out of Stock' }}
                    </button>

                    <!-- Add to Wishlist -->
                    @auth
                    <button type="button" 
                            onclick="toggleWishlist({{ $product->id }})"
                            class="bg-gray-50 border border-gray-300 rounded-md py-3 px-3 flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-500">
                        <i class="fas fa-heart"></i>
                        <span class="sr-only">Add to wishlist</span>
                    </button>
                    @endauth
                </div>
            </form>

            <!-- Product Description -->
            <div class="mt-8">
                <div class="border-t border-gray-200 pt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                    <div class="prose prose-sm text-gray-700">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <a href="{{ route('products.show', $relatedProduct->slug) }}">
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                        @if($relatedProduct->image)
                        <img src="{{ $relatedProduct->image }}" alt="{{ $relatedProduct->name }}" 
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
                        <a href="{{ route('products.show', $relatedProduct->slug) }}" class="hover:text-primary-600">
                            {{ $relatedProduct->name }}
                        </a>
                    </h3>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            @if($relatedProduct->sale_price)
                            <span class="text-lg font-bold text-primary-600">${{ number_format($relatedProduct->sale_price, 2) }}</span>
                            <span class="text-sm text-gray-500 line-through">${{ number_format($relatedProduct->price, 2) }}</span>
                            @else
                            <span class="text-lg font-bold text-primary-600">${{ number_format($relatedProduct->price, 2) }}</span>
                            @endif
                        </div>
                        
                        <button onclick="addToCart({{ $relatedProduct->id }})" 
                                class="bg-primary-600 text-white px-3 py-1 rounded-md text-sm hover:bg-primary-700 transition duration-300">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Reviews Section -->
    <div class="mt-16" id="reviews">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Customer Reviews</h2>
        
        @if($product->reviews->count() > 0)
        <div class="space-y-6">
            @foreach($product->reviews->where('is_approved', true)->take(5) as $review)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                            <span class="text-primary-600 font-semibold">{{ substr($review->user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $review->user->name }}</h4>
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
                </div>
                
                @if($review->title)
                <h5 class="font-semibold text-gray-900 mb-2">{{ $review->title }}</h5>
                @endif
                
                <p class="text-gray-700">{{ $review->comment }}</p>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-gray-500">No reviews yet. Be the first to review this product!</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function addToCartWithQuantity(productId) {
    const quantity = document.querySelector('input[x-model="quantity"]').value;
    
    fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            quantity: parseInt(quantity)
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
