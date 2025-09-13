@extends('layouts.app')

@section('title', $category->name . ' - Products')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Category Header -->
    <div class="mb-8">
        <nav class="text-sm text-gray-600 mb-4">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('categories.index') }}" class="hover:text-blue-600">Categories</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ $category->name }}</span>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="text-gray-600">{{ $category->description }}</p>
                @endif
            </div>
            
            <div class="mt-4 md:mt-0">
                <span class="text-sm text-gray-500">{{ $products->total() }} products found</span>
            </div>
        </div>
    </div>

    <!-- Filters and Sorting -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
        <form method="GET" action="{{ route('categories.show', $category->slug) }}" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-6">
            <!-- Price Range Filter -->
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Price Range:</label>
                <div class="flex items-center space-x-2">
                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                           placeholder="Min" class="w-20 px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <span class="text-gray-500">-</span>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                           placeholder="Max" class="w-20 px-3 py-2 border border-gray-300 rounded-md text-sm">
                </div>
            </div>

            <!-- Sort Options -->
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Sort by:</label>
                <select name="sort" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                </select>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200 text-sm">
                Apply Filters
            </button>
        </form>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition duration-200">
                    <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-t-lg bg-gray-200">
                        @if($product->images->first())
                            <img src="{{ $product->images->first()->url }}" 
                                 alt="{{ $product->images->first()->alt_text }}" 
                                 class="h-48 w-full object-cover object-center group-hover:opacity-75">
                        @else
                            <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">No Image</span>
                            </div>
                        @endif
                        
                        @if($product->is_on_sale)
                            <div class="absolute top-2 left-2">
                                <span class="bg-red-500 text-white px-2 py-1 text-xs rounded">
                                    -{{ $product->discount_percentage }}%
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">
                            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-blue-600">
                                {{ $product->name }}
                            </a>
                        </h3>
                        
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                @if($product->is_on_sale)
                                    <span class="text-lg font-bold text-red-600">${{ number_format($product->discount_price, 2) }}</span>
                                    <span class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                                @else
                                    <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($product->reviews->count() > 0)
                            <div class="flex items-center mb-3">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $product->average_rating)
                                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600 ml-2">({{ $product->total_reviews }})</span>
                            </div>
                        @endif
                        
                        <div class="flex space-x-2">
                            <button class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200 text-sm">
                                Add to Cart
                            </button>
                            <button class="bg-gray-100 text-gray-600 px-3 py-2 rounded-md hover:bg-gray-200 transition duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1m8 0V4.5"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
            <p class="mt-1 text-sm text-gray-500">No products match your current filters.</p>
            <div class="mt-6">
                <a href="{{ route('categories.show', $category->slug) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Clear Filters
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Add to cart functionality
    document.addEventListener('DOMContentLoaded', function() {
        const addToCartButtons = document.querySelectorAll('.add-to-cart');
        
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Add your cart functionality here
                console.log('Add to cart clicked');
            });
        });
    });
</script>
@endpush
