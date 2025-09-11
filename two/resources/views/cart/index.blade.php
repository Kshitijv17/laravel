@extends('layouts.app')

@section('title', 'Shopping Cart - LaraShop')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>
    
    @if($cart && $cart->items->count() > 0)
    <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start xl:gap-x-16">
        <!-- Cart Items -->
        <section aria-labelledby="cart-heading" class="lg:col-span-7">
            <h2 id="cart-heading" class="sr-only">Items in your shopping cart</h2>
            
            <ul role="list" class="border-t border-b border-gray-200 divide-y divide-gray-200">
                @foreach($cart->items as $item)
                <li class="flex py-6 sm:py-10">
                    <div class="flex-shrink-0">
                        <a href="{{ route('products.show', $item->product->slug) }}">
                            @if($item->product->image)
                            <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" 
                                 class="w-24 h-24 rounded-md object-center object-cover sm:w-48 sm:h-48">
                            @else
                            <div class="w-24 h-24 sm:w-48 sm:h-48 bg-gray-200 rounded-md flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-2xl sm:text-4xl"></i>
                            </div>
                            @endif
                        </a>
                    </div>

                    <div class="ml-4 flex-1 flex flex-col justify-between sm:ml-6">
                        <div class="relative pr-9 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:pr-0">
                            <div>
                                <div class="flex justify-between">
                                    <h3 class="text-sm">
                                        <a href="{{ route('products.show', $item->product->slug) }}" 
                                           class="font-medium text-gray-700 hover:text-gray-800">
                                            {{ $item->product->name }}
                                        </a>
                                    </h3>
                                </div>
                                <div class="mt-1 flex text-sm">
                                    <p class="text-gray-500">{{ $item->product->category->name }}</p>
                                    @if($item->product->brand)
                                    <p class="ml-4 pl-4 border-l border-gray-200 text-gray-500">{{ $item->product->brand }}</p>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm font-medium text-gray-900">
                                    ${{ number_format($item->product->final_price, 2) }}
                                </p>
                            </div>

                            <div class="mt-4 sm:mt-0 sm:pr-9">
                                <div class="flex items-center border border-gray-300 rounded-md">
                                    <button type="button" 
                                            onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                            class="p-2 text-gray-600 hover:text-gray-800 disabled:opacity-50"
                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="px-3 py-2 text-gray-900 font-medium">{{ $item->quantity }}</span>
                                    <button type="button" 
                                            onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                            class="p-2 text-gray-600 hover:text-gray-800 disabled:opacity-50"
                                            {{ $item->quantity >= $item->product->stock_quantity ? 'disabled' : '' }}>
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>

                                <div class="absolute top-0 right-0">
                                    <button type="button" 
                                            onclick="removeItem({{ $item->id }})"
                                            class="-m-2 p-2 inline-flex text-gray-400 hover:text-gray-500">
                                        <span class="sr-only">Remove</span>
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <p class="mt-4 flex text-sm text-gray-700 space-x-2">
                            @if($item->product->stock_quantity > 0)
                            <i class="fas fa-check text-green-500"></i>
                            <span>In stock</span>
                            @else
                            <i class="fas fa-times text-red-500"></i>
                            <span>Out of stock</span>
                            @endif
                        </p>
                    </div>
                </li>
                @endforeach
            </ul>
        </section>

        <!-- Order Summary -->
        <section aria-labelledby="summary-heading" class="mt-16 bg-gray-50 rounded-lg px-4 py-6 sm:p-6 lg:p-8 lg:mt-0 lg:col-span-5">
            <h2 id="summary-heading" class="text-lg font-medium text-gray-900">Order summary</h2>

            <dl class="mt-6 space-y-4">
                <div class="flex items-center justify-between">
                    <dt class="text-sm text-gray-600">Subtotal</dt>
                    <dd class="text-sm font-medium text-gray-900">${{ number_format($cart->total, 2) }}</dd>
                </div>
                <div class="border-t border-gray-200 pt-4 flex items-center justify-between">
                    <dt class="flex items-center text-sm text-gray-600">
                        <span>Shipping estimate</span>
                        <a href="#" class="ml-2 flex-shrink-0 text-gray-400 hover:text-gray-500">
                            <i class="fas fa-question-circle"></i>
                        </a>
                    </dt>
                    <dd class="text-sm font-medium text-gray-900">
                        {{ $cart->total >= 50 ? 'Free' : '$5.00' }}
                    </dd>
                </div>
                <div class="border-t border-gray-200 pt-4 flex items-center justify-between">
                    <dt class="flex text-base font-medium text-gray-900">
                        <span>Order total</span>
                    </dt>
                    <dd class="text-base font-medium text-gray-900">
                        ${{ number_format($cart->total + ($cart->total >= 50 ? 0 : 5), 2) }}
                    </dd>
                </div>
            </dl>

            <div class="mt-6">
                @auth
                <a href="{{ route('checkout.index') }}" 
                   class="w-full bg-primary-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-primary-500 flex items-center justify-center">
                    <i class="fas fa-lock mr-2"></i>
                    Checkout
                </a>
                @else
                <div class="space-y-3">
                    <a href="{{ route('login') }}" 
                       class="w-full bg-primary-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-primary-500 flex items-center justify-center">
                        Login to Checkout
                    </a>
                    <p class="text-center text-sm text-gray-600">
                        or 
                        <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-500 font-medium">
                            create an account
                        </a>
                    </p>
                </div>
                @endauth
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('products.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Continue Shopping
                </a>
            </div>
        </section>
    </div>
    @else
    <!-- Empty Cart -->
    <div class="text-center py-16">
        <div class="w-24 h-24 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
            <i class="fas fa-shopping-cart text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Your cart is empty</h3>
        <p class="text-gray-600 mb-6">Add some products to get started</p>
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
function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 1) return;
    
    fetch(`/cart/update/${itemId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating quantity', 'error');
    });
}

function removeItem(itemId) {
    if (!confirm('Are you sure you want to remove this item?')) return;
    
    fetch(`/cart/remove/${itemId}`, {
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
        showNotification('Error removing item', 'error');
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
