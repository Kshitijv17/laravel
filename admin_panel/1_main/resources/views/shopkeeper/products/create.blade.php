@extends('shopkeeper.layout')

@section('page-title', 'Add Product')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Add New Herbal Product</h3>
            <p class="text-gray-600">Create a new product for your herbal collection</p>
        </div>
        <a href="{{ route('shopkeeper.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors duration-200">
            <span class="material-symbols-outlined">arrow_back</span>
            Back to Products
        </a>
    </div>
</div>

<!-- Error Messages -->
@if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        <div class="flex items-center mb-2">
            <span class="material-symbols-outlined mr-2">error</span>
            <h6 class="font-semibold">Please fix the following errors:</h6>
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        <div class="flex items-center">
            <span class="material-symbols-outlined mr-2">check_circle</span>
            {{ session('success') }}
        </div>
    </div>
@endif

<!-- Product Form -->
<form action="{{ route('shopkeeper.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Product Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <span class="material-symbols-outlined text-green-600">spa</span>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900">Basic Information</h4>
                </div>
                
                <div class="space-y-4">
                    <!-- Product Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Product Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('title') border-red-300 @enderror" 
                               value="{{ old('title') }}" 
                               placeholder="e.g., Organic Chamomile Tea"
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Enter a descriptive title for your herbal product</p>
                    </div>

                    <!-- Product Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Product Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('description') border-red-300 @enderror"
                                  placeholder="Describe the benefits, ingredients, and uses of your herbal product..."
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" id="category_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('category_id') border-red-300 @enderror"
                                required>
                            <option value="">Select a category</option>
                            @if(isset($categories))
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->title }}
                                    </option>
                                @endforeach
                            @else
                                <option value="1">Teas</option>
                                <option value="2">Essential Oils</option>
                                <option value="3">Tinctures</option>
                                <option value="4">Topicals</option>
                                <option value="5">Supplements</option>
                            @endif
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing & Inventory Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <span class="material-symbols-outlined text-yellow-600">payments</span>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900">Pricing & Inventory</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" name="price" id="price" step="0.01" min="0"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('price') border-red-300 @enderror"
                                   value="{{ old('price') }}"
                                   placeholder="0.00"
                                   required>
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Stock Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="quantity" id="quantity" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('quantity') border-red-300 @enderror"
                               value="{{ old('quantity') }}"
                               placeholder="0"
                               required>
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Product Image Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <span class="material-symbols-outlined text-blue-600">image</span>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900">Product Image</h4>
                </div>
                
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Image
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-green-400 transition-colors duration-200">
                        <div class="space-y-1 text-center">
                            <span class="material-symbols-outlined text-4xl text-gray-400">cloud_upload</span>
                            <div class="flex text-sm text-gray-600">
                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none">
                                    <span>Upload a file</span>
                                    <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                        </div>
                    </div>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Product Status Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <span class="material-symbols-outlined text-purple-600">toggle_on</span>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900">Product Status</h4>
                </div>
                
                <div class="space-y-4">
                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Active Product
                        </label>
                    </div>
                    <p class="text-xs text-gray-500">Inactive products won't be visible to customers</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="space-y-3">
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">add</span>
                        Create Product
                    </button>
                    <a href="{{ route('shopkeeper.products.index') }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">cancel</span>
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
