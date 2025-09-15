@if(!empty($comparisonData['products']))
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Compare Products ({{ count($comparisonData['products']) }})</h3>
            <a href="{{ route('comparison.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                View Details
            </a>
        </div>
        
        <div class="grid grid-cols-{{ min(count($comparisonData['products']), 4) }} gap-3">
            @foreach($comparisonData['products'] as $product)
                <div class="relative group">
                    <img src="{{ $product['image'] ? asset('storage/' . $product['image']) : asset('images/no-image.jpg') }}" 
                         alt="{{ $product['name'] }}" 
                         class="w-full h-20 object-cover rounded">
                    <button onclick="removeFromComparison({{ $product['id'] }})" 
                            class="absolute -top-1 -right-1 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <p class="text-xs mt-1 truncate">{{ $product['name'] }}</p>
                    <p class="text-xs font-semibold text-green-600">${{ number_format($product['price'], 2) }}</p>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4 flex space-x-2">
            <a href="{{ route('comparison.index') }}" 
               class="flex-1 bg-blue-600 text-white text-center py-2 px-3 rounded text-sm hover:bg-blue-700">
                Compare Now
            </a>
            <button onclick="clearComparison()" 
                    class="bg-gray-600 text-white py-2 px-3 rounded text-sm hover:bg-gray-700">
                Clear
            </button>
        </div>
    </div>
@else
    <div class="bg-gray-50 rounded-lg p-4 text-center">
        <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <p class="text-sm text-gray-600">No products to compare</p>
    </div>
@endif

<script>
function removeFromComparison(productId) {
    fetch('{{ route("comparison.remove") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function clearComparison() {
    if (confirm('Clear all products from comparison?')) {
        fetch('{{ route("comparison.clear") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
