@if($recommendations && $recommendations->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                @switch($type)
                    @case('personalized')
                        Recommended for You
                        @break
                    @case('recently_viewed')
                        Based on Your Recent Views
                        @break
                    @case('abandoned_cart')
                        Complete Your Purchase
                        @break
                    @case('similar')
                        Similar Products
                        @break
                    @case('frequently_bought')
                        Frequently Bought Together
                        @break
                    @default
                        Recommendations
                @endswitch
            </h3>
            @if(in_array($type, ['personalized', 'recently_viewed', 'abandoned_cart']))
                <a href="{{ route('recommendations.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All
                </a>
            @endif
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach($recommendations->take(6) as $product)
                <div class="group cursor-pointer" onclick="trackRecommendationClick({{ $product->id }}, '{{ $type }}')">
                    <div class="relative overflow-hidden rounded-lg">
                        <img src="{{ $product->images->first()?->image_path ? asset('storage/' . $product->images->first()->image_path) : asset('images/no-image.jpg') }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-24 object-cover group-hover:scale-105 transition-transform duration-200">
                        @if(isset($product->recommendation_score) && $product->recommendation_score > 0.8)
                            <div class="absolute top-1 right-1 bg-green-500 text-white text-xs px-1 py-0.5 rounded">
                                Top Pick
                            </div>
                        @endif
                    </div>
                    <div class="mt-2">
                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</h4>
                        <p class="text-xs text-gray-600 truncate">{{ $product->brand?->name }}</p>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-sm font-bold text-green-600">${{ number_format($product->selling_price, 2) }}</span>
                            @if($product->average_rating)
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-xs text-gray-600 ml-1">{{ number_format($product->average_rating, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        @if(isset($product->recommendation_reasons))
                            <p class="text-xs text-gray-500 mt-1 truncate">{{ implode(', ', $product->recommendation_reasons) }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($type === 'frequently_bought' && $recommendations->count() > 0)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <button class="w-full bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 text-sm">
                    Add All to Cart
                </button>
            </div>
        @endif
    </div>
@else
    <div class="bg-gray-50 rounded-lg p-4 text-center">
        <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="text-sm text-gray-600">
            @switch($type)
                @case('personalized')
                    Browse products to get personalized recommendations
                    @break
                @case('recently_viewed')
                    View some products to see recommendations
                    @break
                @case('abandoned_cart')
                    Add items to cart to see related recommendations
                    @break
                @default
                    No recommendations available
            @endswitch
        </p>
    </div>
@endif

<script>
function trackRecommendationClick(productId, type) {
    // Track the click
    fetch('{{ route("recommendations.track-click") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            recommended_product_id: productId,
            recommendation_type: type,
            product_id: {{ request()->get('product_id', 'null') }}
        })
    });
    
    // Navigate to product page
    window.location.href = '/products/' + productId;
}
</script>
