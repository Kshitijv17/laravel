@extends('layouts.app')

@section('title', 'Frequently Asked Questions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h1>
            <p class="text-lg text-gray-600">Find answers to common questions about our products and services</p>
        </div>

        @if($faqs->count() > 0)
            <!-- FAQ Accordion -->
            <div class="space-y-4">
                @foreach($faqs as $index => $faq)
                    <div class="bg-white rounded-lg shadow-sm border">
                        <button class="w-full px-6 py-4 text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg"
                                onclick="toggleFaq({{ $index }})">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900 pr-4">
                                    {{ $faq->question }}
                                </h3>
                                <svg id="icon-{{ $index }}" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>
                        
                        <div id="content-{{ $index }}" class="hidden px-6 pb-4">
                            <div class="text-gray-700 leading-relaxed">
                                {!! nl2br(e($faq->answer)) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Search Box -->
            <div class="mt-12 bg-gray-50 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Can't find what you're looking for?</h3>
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" id="faq-search" placeholder="Search FAQs..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button onclick="searchFaqs()" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-200 font-medium">
                        Search
                    </button>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="mt-8 text-center">
                <p class="text-gray-600 mb-4">Still have questions?</p>
                <a href="{{ route('contact') }}" 
                   class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition duration-200 font-medium">
                    <i class="fas fa-envelope mr-2"></i>
                    Contact Support
                </a>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <i class="fas fa-question-circle text-gray-300 text-6xl mb-4"></i>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">No FAQs Available</h2>
                    <p class="text-gray-600 mb-6">
                        We're working on adding frequently asked questions. In the meantime, feel free to contact us directly.
                    </p>
                    <a href="{{ route('contact') }}" 
                       class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition duration-200 font-medium">
                        <i class="fas fa-envelope mr-2"></i>
                        Contact Us
                    </a>
                </div>
            </div>
        @endif

        <!-- Popular Topics -->
        @if($faqs->count() > 0)
            <div class="mt-16">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">Popular Topics</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="#" class="block p-4 bg-white rounded-lg border hover:border-blue-300 hover:shadow-md transition duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-shipping-fast text-blue-600 text-xl mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Shipping & Delivery</h4>
                                <p class="text-sm text-gray-600">Information about shipping times and costs</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="#" class="block p-4 bg-white rounded-lg border hover:border-blue-300 hover:shadow-md transition duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-undo text-green-600 text-xl mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Returns & Refunds</h4>
                                <p class="text-sm text-gray-600">How to return items and get refunds</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="#" class="block p-4 bg-white rounded-lg border hover:border-blue-300 hover:shadow-md transition duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-user-circle text-purple-600 text-xl mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Account & Orders</h4>
                                <p class="text-sm text-gray-600">Managing your account and order history</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="#" class="block p-4 bg-white rounded-lg border hover:border-blue-300 hover:shadow-md transition duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-credit-card text-orange-600 text-xl mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Payment Methods</h4>
                                <p class="text-sm text-gray-600">Accepted payment options and security</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="#" class="block p-4 bg-white rounded-lg border hover:border-blue-300 hover:shadow-md transition duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt text-red-600 text-xl mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Privacy & Security</h4>
                                <p class="text-sm text-gray-600">How we protect your personal information</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="#" class="block p-4 bg-white rounded-lg border hover:border-blue-300 hover:shadow-md transition duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-headset text-indigo-600 text-xl mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Customer Support</h4>
                                <p class="text-sm text-gray-600">Getting help when you need it</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- JavaScript for FAQ Functionality -->
<script>
function toggleFaq(index) {
    const content = document.getElementById(`content-${index}`);
    const icon = document.getElementById(`icon-${index}`);
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

function searchFaqs() {
    const searchTerm = document.getElementById('faq-search').value.toLowerCase();
    const faqItems = document.querySelectorAll('[id^="content-"]');
    
    faqItems.forEach((item, index) => {
        const question = item.parentElement.querySelector('h3').textContent.toLowerCase();
        const answer = item.textContent.toLowerCase();
        const container = item.parentElement;
        
        if (question.includes(searchTerm) || answer.includes(searchTerm) || searchTerm === '') {
            container.style.display = 'block';
            if (searchTerm !== '') {
                // Auto-expand matching FAQs
                item.classList.remove('hidden');
                document.getElementById(`icon-${index}`).style.transform = 'rotate(180deg)';
            }
        } else {
            container.style.display = 'none';
        }
    });
    
    if (searchTerm === '') {
        // Reset all FAQs to collapsed state
        faqItems.forEach((item, index) => {
            item.classList.add('hidden');
            document.getElementById(`icon-${index}`).style.transform = 'rotate(0deg)';
        });
    }
}

// Allow search on Enter key
document.getElementById('faq-search').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchFaqs();
    }
});

// Real-time search as user types
document.getElementById('faq-search').addEventListener('input', function() {
    searchFaqs();
});
</script>
@endsection
