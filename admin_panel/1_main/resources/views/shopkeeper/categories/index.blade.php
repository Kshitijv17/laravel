@extends('shopkeeper.layout')

@section('page-title', 'Categories')

@section('content')
<div class="mb-6">
    <h3 class="text-2xl font-bold text-gray-900 mb-2">Herbal Categories</h3>
    <p class="text-gray-600">Organize your herbal products into categories</p>
</div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="relative flex-1 max-w-md">
        <span class="material-symbols-outlined absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">search</span>
        <input type="text" placeholder="Search categories..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
    </div>
    <a href="{{ route('shopkeeper.categories.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors duration-200">
        <span class="material-symbols-outlined">add</span>
        Add Category
    </a>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        <div class="flex items-center">
            <span class="material-symbols-outlined mr-2">check_circle</span>
            {{ session('success') }}
        </div>
    </div>
@endif

<!-- Categories Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @if(isset($categories) && $categories->count() > 0)
        @foreach($categories as $category)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
                <!-- Category Image -->
                <div class="h-32 bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                    @if($category->image ?? false)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->title }}" class="w-full h-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-4xl text-green-600">category</span>
                    @endif
                </div>
                
                <!-- Category Content -->
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $category->title ?? 'Category Name' }}</h3>
                    
                    <!-- Status Badge -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ ($category->active ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ($category->active ?? 'active') === 'active' ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="text-xs text-gray-500">{{ $category->created_at->format('M d, Y') ?? 'Recently' }}</span>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('shopkeeper.categories.edit', $category->id ?? 1) }}" class="flex-1 bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200 flex items-center justify-center gap-1">
                            <span class="material-symbols-outlined text-sm">edit</span>
                            Edit
                        </a>
                        <button onclick="deleteCategory({{ $category->id ?? 1 }})" class="bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <!-- Sample Categories -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="h-32 bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                <span class="material-symbols-outlined text-4xl text-green-600">spa</span>
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Herbal Teas</h3>
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    <span class="text-xs text-gray-500">Oct 26, 2023</span>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="#" class="flex-1 bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200 flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        Edit
                    </a>
                    <button class="bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="h-32 bg-gradient-to-br from-yellow-100 to-yellow-200 flex items-center justify-center">
                <span class="material-symbols-outlined text-4xl text-yellow-600">local_florist</span>
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Essential Oils</h3>
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    <span class="text-xs text-gray-500">Oct 25, 2023</span>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="#" class="flex-1 bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200 flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        Edit
                    </a>
                    <button class="bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="h-32 bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center">
                <span class="material-symbols-outlined text-4xl text-purple-600">grass</span>
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Tinctures</h3>
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    <span class="text-xs text-gray-500">Oct 24, 2023</span>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="#" class="flex-1 bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200 flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        Edit
                    </a>
                    <button class="bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="h-32 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                <span class="material-symbols-outlined text-4xl text-blue-600">eco</span>
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Topicals</h3>
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    <span class="text-xs text-gray-500">Oct 23, 2023</span>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="#" class="flex-1 bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200 flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        Edit
                    </a>
                    <button class="bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
