<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Note') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('notes.update', $note) }}" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $note->title) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                            <textarea name="content" id="content" rows="6" required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('content', $note->content) }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Note Color</label>
                            <div class="flex space-x-3" id="colorOptions">
                                <label class="cursor-pointer color-option" data-color="#fef08a">
                                    <input type="radio" name="color" value="#fef08a" class="sr-only" {{ old('color', $note->color) == '#fef08a' ? 'checked' : '' }}>
                                    <div class="w-10 h-10 rounded-full border-3 hover:scale-110 transition-all duration-200" style="background-color: #fef08a; border-color: {{ old('color', $note->color) == '#fef08a' ? '#374151' : '#d1d5db' }};"></div>
                                </label>
                                <label class="cursor-pointer color-option" data-color="#fecaca">
                                    <input type="radio" name="color" value="#fecaca" class="sr-only" {{ old('color', $note->color) == '#fecaca' ? 'checked' : '' }}>
                                    <div class="w-10 h-10 rounded-full border-3 hover:scale-110 transition-all duration-200" style="background-color: #fecaca; border-color: {{ old('color', $note->color) == '#fecaca' ? '#374151' : '#d1d5db' }};"></div>
                                </label>
                                <label class="cursor-pointer color-option" data-color="#bfdbfe">
                                    <input type="radio" name="color" value="#bfdbfe" class="sr-only" {{ old('color', $note->color) == '#bfdbfe' ? 'checked' : '' }}>
                                    <div class="w-10 h-10 rounded-full border-3 hover:scale-110 transition-all duration-200" style="background-color: #bfdbfe; border-color: {{ old('color', $note->color) == '#bfdbfe' ? '#374151' : '#d1d5db' }};"></div>
                                </label>
                                <label class="cursor-pointer color-option" data-color="#bbf7d0">
                                    <input type="radio" name="color" value="#bbf7d0" class="sr-only" {{ old('color', $note->color) == '#bbf7d0' ? 'checked' : '' }}>
                                    <div class="w-10 h-10 rounded-full border-3 hover:scale-110 transition-all duration-200" style="background-color: #bbf7d0; border-color: {{ old('color', $note->color) == '#bbf7d0' ? '#374151' : '#d1d5db' }};"></div>
                                </label>
                                <label class="cursor-pointer color-option" data-color="#e9d5ff">
                                    <input type="radio" name="color" value="#e9d5ff" class="sr-only" {{ old('color', $note->color) == '#e9d5ff' ? 'checked' : '' }}>
                                    <div class="w-10 h-10 rounded-full border-3 hover:scale-110 transition-all duration-200" style="background-color: #e9d5ff; border-color: {{ old('color', $note->color) == '#e9d5ff' ? '#374151' : '#d1d5db' }};"></div>
                                </label>
                                <label class="cursor-pointer color-option" data-color="#fed7aa">
                                    <input type="radio" name="color" value="#fed7aa" class="sr-only" {{ old('color', $note->color) == '#fed7aa' ? 'checked' : '' }}>
                                    <div class="w-10 h-10 rounded-full border-3 hover:scale-110 transition-all duration-200" style="background-color: #fed7aa; border-color: {{ old('color', $note->color) == '#fed7aa' ? '#374151' : '#d1d5db' }};"></div>
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
                                Update Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Color selection functionality
            const colorOptions = document.querySelectorAll('.color-option');
            
            colorOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selection from all options
                    colorOptions.forEach(opt => {
                        const circle = opt.querySelector('div');
                        circle.style.borderColor = '#d1d5db';
                        circle.style.borderWidth = '3px';
                        opt.querySelector('input').checked = false;
                    });
                    
                    // Add selection to clicked option
                    const circle = this.querySelector('div');
                    circle.style.borderColor = '#374151';
                    circle.style.borderWidth = '4px';
                    this.querySelector('input').checked = true;
                });
            });
        });
    </script>
</x-app-layout>
