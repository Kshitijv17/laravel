@extends('layouts.app')

@section('title', 'Checkout - LaraShop')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>
    
    <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start xl:gap-x-16">
        <!-- Checkout Form -->
        <div class="lg:col-span-7">
            <form method="POST" action="{{ route('checkout.process') }}" class="space-y-8">
                @csrf
                
                <!-- Shipping Information -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">Shipping Information</h2>
                    
                    <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                        <div class="sm:col-span-2">
                            <label for="shipping_address[name]" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="shipping_address[name]" id="shipping_address[name]" 
                                   value="{{ old('shipping_address.name', auth()->user()->name) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 @error('shipping_address.name') border-red-500 @enderror">
                            @error('shipping_address.name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="shipping_address[address]" class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" name="shipping_address[address]" id="shipping_address[address]" 
                                   value="{{ old('shipping_address.address', auth()->user()->address) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 @error('shipping_address.address') border-red-500 @enderror">
                            @error('shipping_address.address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_address[city]" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" name="shipping_address[city]" id="shipping_address[city]" 
                                   value="{{ old('shipping_address.city', auth()->user()->city) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 @error('shipping_address.city') border-red-500 @enderror">
                            @error('shipping_address.city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_address[state]" class="block text-sm font-medium text-gray-700">State / Province</label>
                            <input type="text" name="shipping_address[state]" id="shipping_address[state]" 
                                   value="{{ old('shipping_address.state', auth()->user()->state) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 @error('shipping_address.state') border-red-500 @enderror">
                            @error('shipping_address.state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_address[postal_code]" class="block text-sm font-medium text-gray-700">Postal Code</label>
                            <input type="text" name="shipping_address[postal_code]" id="shipping_address[postal_code]" 
                                   value="{{ old('shipping_address.postal_code', auth()->user()->postal_code) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 @error('shipping_address.postal_code') border-red-500 @enderror">
                            @error('shipping_address.postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_address[country]" class="block text-sm font-medium text-gray-700">Country</label>
                            <select name="shipping_address[country]" id="shipping_address[country]" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 @error('shipping_address.country') border-red-500 @enderror">
                                <option value="">Select Country</option>
                                <option value="US" {{ old('shipping_address.country', auth()->user()->country) == 'US' ? 'selected' : '' }}>United States</option>
                                <option value="CA" {{ old('shipping_address.country', auth()->user()->country) == 'CA' ? 'selected' : '' }}>Canada</option>
                                <option value="GB" {{ old('shipping_address.country', auth()->user()->country) == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                <option value="AU" {{ old('shipping_address.country', auth()->user()->country) == 'AU' ? 'selected' : '' }}>Australia</option>
                            </select>
                            @error('shipping_address.country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">Payment Method</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input id="cod" name="payment_method" type="radio" value="cod" 
                                   {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}
                                   class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300">
                            <label for="cod" class="ml-3 block text-sm font-medium text-gray-700">
                                Cash on Delivery
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="card" name="payment_method" type="radio" value="card" 
                                   {{ old('payment_method') == 'card' ? 'checked' : '' }}
                                   class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300">
                            <label for="card" class="ml-3 block text-sm font-medium text-gray-700">
                                Credit/Debit Card
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="paypal" name="payment_method" type="radio" value="paypal" 
                                   {{ old('payment_method') == 'paypal' ? 'checked' : '' }}
                                   class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300">
                            <label for="paypal" class="ml-3 block text-sm font-medium text-gray-700">
                                PayPal
                            </label>
                        </div>
                    </div>
                    
                    @error('payment_method')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Order Notes -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">Order Notes (Optional)</h2>
                    
                    <textarea name="notes" rows="4" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Any special instructions for your order...">{{ old('notes') }}</textarea>
                </div>
                
                <!-- Hidden fields for shipping info -->
                <input type="hidden" name="shipping_address" value="{{ old('shipping_address.address', auth()->user()->address) }}">
                <input type="hidden" name="shipping_city" value="{{ old('shipping_city', auth()->user()->city) }}">
                <input type="hidden" name="shipping_state" value="{{ old('shipping_state', auth()->user()->state) }}">
                <input type="hidden" name="shipping_zip" value="{{ old('shipping_zip', auth()->user()->postal_code) }}">
                <input type="hidden" name="shipping_country" value="{{ old('shipping_country', auth()->user()->country) }}">
                
                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit" id="submit-order"
                            class="w-full bg-primary-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-primary-500">
                        <i class="fas fa-lock mr-2"></i>
                        Complete Purchase
                    </button>
                </div>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="mt-10 lg:mt-0 lg:col-span-5">
            <div class="bg-gray-50 rounded-lg px-4 py-6 sm:p-6 lg:p-8">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Order Summary</h2>

                <!-- Cart Items -->
                <ul role="list" class="divide-y divide-gray-200 mb-6">
                    @foreach($cart->items as $item)
                    <li class="flex py-4">
                        <div class="flex-shrink-0">
                            @if($item->product->image)
                            <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" 
                                 class="w-16 h-16 rounded-md object-center object-cover">
                            @else
                            <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                            @endif
                        </div>

                        <div class="ml-4 flex-1">
                            <div class="flex justify-between">
                                <h4 class="text-sm font-medium text-gray-900">{{ $item->product->name }}</h4>
                                <p class="text-sm font-medium text-gray-900">${{ number_format($item->subtotal, 2) }}</p>
                            </div>
                            <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                            <p class="text-sm text-gray-500">${{ number_format($item->product->final_price, 2) }} each</p>
                        </div>
                    </li>
                    @endforeach
                </ul>

                <!-- Totals -->
                <dl class="space-y-4">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600">Subtotal</dt>
                        <dd class="text-sm font-medium text-gray-900">${{ number_format($cart->total, 2) }}</dd>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600">Shipping</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            {{ $cart->total >= 50 ? 'Free' : '$5.00' }}
                        </dd>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4 flex items-center justify-between">
                        <dt class="text-base font-medium text-gray-900">Order total</dt>
                        <dd class="text-base font-medium text-gray-900">
                            ${{ number_format($cart->total + ($cart->total >= 50 ? 0 : 5), 2) }}
                        </dd>
                    </div>
                </dl>

            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('form').setAttribute('id', 'checkout-form');
</script>
@endsection
