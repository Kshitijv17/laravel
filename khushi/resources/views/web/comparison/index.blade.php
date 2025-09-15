@extends('layouts.web')

@section('title', 'Product Comparison')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Product Comparison</h1>
        @if(!empty($comparisonData['products']))
            <div class="flex space-x-4">
                <button onclick="exportComparison('pdf')" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Export PDF
                </button>
                <button onclick="exportComparison('csv')" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Export CSV
                </button>
                <button onclick="shareComparison()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Share
                </button>
                <button onclick="clearComparison()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    Clear All
                </button>
            </div>
        @endif
    </div>

    @if(empty($comparisonData['products']))
        <div class="text-center py-16">
            <div class="mb-8">
                <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-medium text-gray-900 mb-4">No products to compare</h3>
            <p class="text-gray-600 mb-8">Add products to your comparison list to see detailed comparisons here.</p>
            <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                Browse Products
            </a>
        </div>
    @else
        <!-- Products Overview -->
        <div class="grid grid-cols-1 md:grid-cols-{{ count($comparisonData['products']) }} gap-6 mb-8">
            @foreach($comparisonData['products'] as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="relative">
                        <img src="{{ $product['image'] ? asset('storage/' . $product['image']) : asset('images/no-image.jpg') }}" 
                             alt="{{ $product['name'] }}" 
                             class="w-full h-48 object-cover">
                        <button onclick="removeFromComparison({{ $product['id'] }})" 
                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">{{ $product['name'] }}</h3>
                        <p class="text-gray-600 text-sm mb-2">{{ $product['brand'] }}</p>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-green-600">${{ number_format($product['price'], 2) }}</span>
                                @if($product['original_price'] && $product['original_price'] > $product['price'])
                                    <span class="text-sm text-gray-500 line-through ml-2">${{ number_format($product['original_price'], 2) }}</span>
                                @endif
                            </div>
                            @if($product['discount_percentage'] > 0)
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">
                                    {{ $product['discount_percentage'] }}% OFF
                                </span>
                            @endif
                        </div>
                        <div class="mt-3 flex items-center">
                            @if($product['rating'])
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $product['rating'] ? 'text-yellow-400' : 'text-gray-300' }}" 
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-600">({{ $product['reviews_count'] }})</span>
                                </div>
                            @endif
                        </div>
                        <div class="mt-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $product['stock_status'] === 'in_stock' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product['stock_status'] === 'in_stock' ? 'In Stock' : 'Out of Stock' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Detailed Comparison Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Detailed Comparison</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    @foreach($comparisonData['comparison_table'] as $section => $features)
                        <thead class="bg-gray-50">
                            <tr>
                                <th colspan="{{ count($comparisonData['products']) + 1 }}" 
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $section }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($features as $feature => $values)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">
                                        {{ $feature }}
                                    </td>
                                    @foreach($values as $value)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $value }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    @endforeach
                </table>
            </div>
        </div>

        <!-- Similar Alternatives -->
        @if(!empty($comparisonData['similar_alternatives']) && $comparisonData['similar_alternatives']->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Similar Alternatives</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
                    @foreach($comparisonData['similar_alternatives'] as $alternative)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <img src="{{ $alternative->images->first()?->image_path ? asset('storage/' . $alternative->images->first()->image_path) : asset('images/no-image.jpg') }}" 
                                 alt="{{ $alternative->name }}" 
                                 class="w-full h-32 object-cover">
                            <div class="p-3">
                                <h3 class="font-medium text-sm mb-1 truncate">{{ $alternative->name }}</h3>
                                <p class="text-xs text-gray-600 mb-2">{{ $alternative->brand?->name }}</p>
                                <p class="text-lg font-bold text-green-600">${{ number_format($alternative->selling_price, 2) }}</p>
                                <button onclick="addToComparison({{ $alternative->id }})" 
                                        class="w-full mt-2 bg-blue-600 text-white text-xs py-1 px-2 rounded hover:bg-blue-700">
                                    Add to Compare
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>

<!-- Share Modal -->
<div id="shareModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Share Comparison</h3>
            <button onclick="closeShareModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="shareContent">
            <p class="text-gray-600 mb-4">Share this comparison with others:</p>
            <div class="flex items-center space-x-2">
                <input type="text" id="shareUrl" readonly 
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                <button onclick="copyShareUrl()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Copy
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function removeFromComparison(productId) {
    fetch('/compare/remove', {
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

function addToComparison(productId) {
    fetch('{{ route("comparison.add") }}', {
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
        } else {
            alert(data.message);
        }
    });
}

function clearComparison() {
    if (confirm('Are you sure you want to clear all products from comparison?')) {
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

function exportComparison(format) {
    window.location.href = '{{ route("comparison.export") }}?format=' + format;
}

function shareComparison() {
    const productIds = @json(collect($comparisonData['products'] ?? [])->pluck('id')->toArray());
    
    fetch('{{ route("comparison.share") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ products: productIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('shareUrl').value = data.share_url;
            document.getElementById('shareModal').classList.remove('hidden');
            document.getElementById('shareModal').classList.add('flex');
        }
    });
}

function closeShareModal() {
    document.getElementById('shareModal').classList.add('hidden');
    document.getElementById('shareModal').classList.remove('flex');
}

function copyShareUrl() {
    const shareUrl = document.getElementById('shareUrl');
    shareUrl.select();
    document.execCommand('copy');
    
    // Show feedback
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Copied!';
    button.classList.add('bg-green-600');
    button.classList.remove('bg-blue-600');
    
    setTimeout(() => {
        button.textContent = originalText;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-blue-600');
    }, 2000);
}
</script>
@endpush
