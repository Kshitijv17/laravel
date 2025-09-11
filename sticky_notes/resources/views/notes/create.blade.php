<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Note') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('notes.store') }}" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                            <textarea name="content" id="content" rows="6" required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Note Color</label>
                            <div class="flex space-x-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="color" value="#fef08a" class="sr-only" checked>
                                    <div class="w-8 h-8 rounded-full border-2 border-gray-300 bg-yellow-200 hover:border-gray-400"></div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="color" value="#fecaca" class="sr-only">
                                    <div class="w-8 h-8 rounded-full border-2 border-gray-300 bg-red-200 hover:border-gray-400"></div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="color" value="#bfdbfe" class="sr-only">
                                    <div class="w-8 h-8 rounded-full border-2 border-gray-300 bg-blue-200 hover:border-gray-400"></div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="color" value="#bbf7d0" class="sr-only">
                                    <div class="w-8 h-8 rounded-full border-2 border-gray-300 bg-green-200 hover:border-gray-400"></div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="color" value="#e9d5ff" class="sr-only">
                                    <div class="w-8 h-8 rounded-full border-2 border-gray-300 bg-purple-200 hover:border-gray-400"></div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="color" value="#fed7aa" class="sr-only">
                                    <div class="w-8 h-8 rounded-full border-2 border-gray-300 bg-orange-200 hover:border-gray-400"></div>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('notes.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-yellow-400 hover:bg-yellow-500 text-gray-800 font-bold py-2 px-4 rounded">
                                Create Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
