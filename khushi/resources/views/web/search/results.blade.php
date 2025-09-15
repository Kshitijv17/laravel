@extends('layouts.web')

@section('title', 'Search Results - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Search Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
            @if(request('q'))
                Search Results for "{{ request('q') }}"
            @else
                All Products
            @endif
        </h1>
        <p class="text-gray-600">{{ $products->total() }} {{ Str::plural('product', $products->total()) }} found</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Filters Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
                
                <form method="GET" action="{{ route('search') }}" id="search-filters">
                    <!-- Keep search query -->
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif

                    <!-- Categories -->
                    <div class="mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">Categories</h3>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach($categories as $category)
                                <label class="flex items-center">
                                    <input type="radio" name="category" value="{{ $category->id }}" 
                                           {{ request('category') == $category->id ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                    <span class="ml-auto text-xs text-gray-500">({{ $category->products_count ?? 0 }})</span>
                                </label>
                            @endforeach
                        </div>
                        @if(request('category'))
                            <button type="button" onclick="clearFilter('category')" 
                                    class="text-xs text-blue-600 hover:text-blue-800 mt-2">Clear</button>
                        @endif
                    </div>

                    <!-- Brands -->
                    @if($brands->count() > 0)
                        <div class="mb-6">
                            <h3 class="font-medium text-gray-900 mb-3">Brands</h3>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach($brands as $brand)
                                    <label class="flex items-center">
                                        <input type="radio" name="brand" value="{{ $brand->id }}" 
                                               {{ request('brand') == $brand->id ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $brand->name }}</span>
                                        <span class="ml-auto text-xs text-gray-500">({{ $brand->products_count ?? 0 }})</span>
                                    </label>
                                @endforeach
                            </div>
                            @if(request('brand'))
                                <button type="button" onclick="clearFilter('brand')" 
                                        class="text-xs text-blue-600 hover:text-blue-800 mt-2">Clear</button>
                            @endif
                        </div>
                    @endif

                    <!-- Price Range -->
                    <div class="mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">Price Range</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Min Price</label>
                                <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                       placeholder="0" min="0" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Max Price</label>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                       placeholder="1000" min="0" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        @if(request('min_price') || request('max_price'))
                            <button type="button" onclick="clearPriceFilter()" 
                                    class="text-xs text-blue-600 hover:text-blue-800 mt-2">Clear</button>
                        @endif
                    </div>

                    <!-- Rating -->
                    <div class="mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">Customer Rating</h3>
                        <div class="space-y-2">
                            @for($i = 5; $i >= 1; $i--)
                                <label class="flex items-center">
                                    <input type="radio" name="rating" value="{{ $i }}" 
                                           {{ request('rating') == $i ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 flex items-center">
                                        @for($j = 1; $j <= 5; $j++)
                                            <svg class="w-4 h-4 {{ $j <= $i ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="ml-1 text-sm text-gray-600">& up</span>
                                    </span>
                                </label>
                            @endfor
                        </div>
                        @if(request('rating'))
                            <button type="button" onclick="clearFilter('rating')" 
                                    class="text-xs text-blue-600 hover:text-blue-800 mt-2">Clear</button>
                        @endif
                    </div>

                    <!-- Availability -->
                    <div class="mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">Availability</h3>
                        <label class="flex items-center">
                            <input type="checkbox" name="in_stock" value="1" 
                                   {{ request('in_stock') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">In Stock Only</span>
                        </label>
                    </div>

                    <!-- Apply Filters Button -->
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                        Apply Filters
                    </button>

                    <!-- Clear All Filters -->
                    @if(request()->hasAny(['category', 'brand', 'min_price', 'max_price', 'rating', 'in_stock']))
                        <a href="{{ route('search', ['q' => request('q')]) }}" 
                           class="block w-full text-center text-gray-600 hover:text-gray-800 py-2 mt-2">
                            Clear All Filters
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="lg:col-span-3">
            <!-- Sort and View Options -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div class="mb-4 sm:mb-0">
                    <p class="text-sm text-gray-600">
                        Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} 
                        of {{ $products->total() }} results
                    </p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Sort Dropdown -->
                    <select name="sort" onchange="updateSort(this.value)" 
                            class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="relevance" {{ request('sort') == 'relevance' ? 'selected' : '' }}>Sort by Relevance</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Most Popular</option>
                    </select>

                    <!-- View Toggle -->
                    <div class="flex border border-gray-300 rounded">
                        <button onclick="setView('grid')" id="grid-view" 
                                class="px-3 py-2 text-gray-600 hover:text-gray-800 bg-gray-100">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button onclick="setView('list')" id="list-view" 
                                class="px-3 py-2 text-gray-600 hover:text-gray-800">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 8a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 12a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products -->
            @if($products->count() > 0)
                <div id="products-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="relative">
                                <img src="{{ $product->image_url }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-48 object-cover">
                                
                                @if($product->discount_percentage > 0)
                                    <span class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-xs">
                                        -{{ $product->discount_percentage }}%
                                    </span>
                                @endif
                                
                                @if($product->stock_quantity <= 0)
                                    <span class="absolute top-2 right-2 bg-gray-500 text-white px-2 py-1 rounded text-xs">
                                        Out of Stock
                                    </span>
                                @endif

                                <!-- Quick Actions -->
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center space-x-2">
                                    <a href="{{ route('product.show', $product->slug) }}" 
                                       class="bg-white text-gray-800 px-3 py-2 rounded text-sm hover:bg-gray-100">
                                        View Details
                                    </a>
                                    @if($product->stock_quantity > 0)
                                        <button onclick="addToCart({{ $product->id }})" 
                                                class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                                            Add to Cart
                                        </button>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    @if($product->category)
                                        <span class="text-xs text-gray-500">{{ $product->category->name }}</span>
                                    @endif
                                    @if($product->brand)
                                        <span class="text-xs text-blue-600">{{ $product->brand->name }}</span>
                                    @endif
                                </div>
                                
                                <h3 class="font-medium text-gray-900 mb-2 hover:text-blue-600">
                                    <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                </h3>
                                
                                <!-- Rating -->
                                @if($product->average_rating > 0)
                                    <div class="flex items-center mb-2">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="ml-1 text-sm text-gray-600">({{ $product->reviews_count }})</span>
                                    </div>
                                @endif
                                
                                <!-- Price -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        @if($product->discount_price)
                                            <span class="text-lg font-bold text-gray-900">${{ number_format($product->discount_price, 2) }}</span>
                                            <span class="text-sm text-gray-500 line-through ml-2">${{ number_format($product->price, 2) }}</span>
                                        @else
                                            <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>
                                    
                                    <button onclick="addToWishlist({{ $product->id }})" 
                                            class="text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                <!-- No Results -->
                <div class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
                    <p class="text-gray-600 mb-4">Try adjusting your search criteria or browse our categories.</p>
                    <a href="{{ route('home') }}" 
                       class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Browse All Products
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function clearFilter(filterName) {
    const form = document.getElementById('search-filters');
    const input = form.querySelector(`[name="${filterName}"]`);
    if (input) {
        if (input.type === 'radio' || input.type === 'checkbox') {
            input.checked = false;
        } else {
            input.value = '';
        }
        form.submit();
    }
}

function clearPriceFilter() {
    const form = document.getElementById('search-filters');
    form.querySelector('[name="min_price"]').value = '';
    form.querySelector('[name="max_price"]').value = '';
    form.submit();
}

function updateSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    window.location = url;
}

function setView(viewType) {
    const container = document.getElementById('products-container');
    const gridBtn = document.getElementById('grid-view');
    const listBtn = document.getElementById('list-view');
    
    if (viewType === 'list') {
        container.className = 'space-y-4';
        container.querySelectorAll('.product-card').forEach(card => {
            card.className = 'product-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow flex';
        });
        gridBtn.classList.remove('bg-gray-100');
        listBtn.classList.add('bg-gray-100');
    } else {
        container.className = 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6';
        container.querySelectorAll('.product-card').forEach(card => {
            card.className = 'product-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow';
        });
        listBtn.classList.remove('bg-gray-100');
        gridBtn.classList.add('bg-gray-100');
    }
    
    localStorage.setItem('productView', viewType);
}

// Auto-submit filters on change
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('search-filters');
    const inputs = form.querySelectorAll('input[type="radio"], input[type="checkbox"]');
    
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            form.submit();
        });
    });
    
    // Restore view preference
    const savedView = localStorage.getItem('productView');
    if (savedView) {
        setView(savedView);
    }
});

function addToCart(productId) {
    // Add to cart functionality
    fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Product added to cart!');
        }
    })
    .catch(error => console.error('Error:', error));
}

function addToWishlist(productId) {
    // Add to wishlist functionality
    fetch(`/wishlist/add/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to wishlist!');
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
