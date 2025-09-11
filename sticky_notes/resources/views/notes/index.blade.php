<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Sticky Notes') }}
            </h2>
            <a href="{{ route('notes.create') }}" class="bg-yellow-400 hover:bg-yellow-500 text-gray-800 font-bold py-2 px-4 rounded-lg shadow-lg transform hover:scale-105 transition-all duration-200 inline-flex items-center">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Note
            </a>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Notes Board -->
            <div id="notesBoard" class="relative w-full min-h-[600px] bg-white rounded-lg shadow-lg p-8">
                <div class="absolute inset-0 opacity-5 bg-grid-pattern pointer-events-none"></div>
                
                @if($notes->count() > 0)
                    @foreach($notes as $loop_index => $note)
                        <div class="sticky-note absolute cursor-move transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl z-10" 
                             data-id="{{ $note->id }}"
                             style="left: {{ max(20, $note->position_x) }}px; top: {{ max(20, $note->position_y) }}px; background-color: {{ $note->color }}; z-index: {{ 10 + $loop_index }};">
                            
                            <!-- Note Header -->
                            <div class="note-header flex justify-between items-center p-3 border-b border-gray-200 bg-black bg-opacity-5">
                                <h3 class="font-semibold text-gray-800 truncate flex-1 mr-2">{{ Str::limit($note->title, 20) }}</h3>
                                <div class="flex space-x-1">
                                    <a href="{{ route('notes.edit', $note) }}" class="edit-note text-gray-600 hover:text-blue-600 p-1" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('notes.destroy', $note) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this note?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-note text-gray-600 hover:text-red-600 p-1" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Note Content -->
                            <div class="note-content p-3">
                                <p class="text-gray-700 text-sm leading-relaxed">{{ Str::limit($note->content, 150) }}</p>
                                @if(strlen($note->content) > 150)
                                    <a href="{{ route('notes.show', $note) }}" class="view-full text-blue-600 hover:text-blue-800 text-xs mt-2 inline-block">
                                        Read more...
                                    </a>
                                @endif
                            </div>
                            
                            <!-- Note Footer -->
                            <div class="note-footer px-3 pb-3">
                                <small class="text-gray-500 text-xs">{{ $note->updated_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-16">
                        <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No notes yet</h3>
                        <p class="mt-2 text-gray-500">Get started by creating your first sticky note!</p>
                        <a href="{{ route('notes.create') }}" class="inline-block mt-4 bg-yellow-400 hover:bg-yellow-500 text-gray-800 font-bold py-2 px-4 rounded-lg shadow-lg transform hover:scale-105 transition-all duration-200">
                            Create Your First Note
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add/Edit Note Modal -->
    <div id="noteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Add New Note</h3>
                <form id="noteForm">
                    <input type="hidden" id="noteId" name="noteId">
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" id="title" name="title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                        <textarea id="content" name="content" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                        <div class="flex space-x-2">
                            <button type="button" class="color-option w-8 h-8 rounded-full border-2 border-gray-300" style="background-color: #fef08a" data-color="#fef08a"></button>
                            <button type="button" class="color-option w-8 h-8 rounded-full border-2 border-gray-300" style="background-color: #fecaca" data-color="#fecaca"></button>
                            <button type="button" class="color-option w-8 h-8 rounded-full border-2 border-gray-300" style="background-color: #bfdbfe" data-color="#bfdbfe"></button>
                            <button type="button" class="color-option w-8 h-8 rounded-full border-2 border-gray-300" style="background-color: #bbf7d0" data-color="#bbf7d0"></button>
                            <button type="button" class="color-option w-8 h-8 rounded-full border-2 border-gray-300" style="background-color: #e9d5ff" data-color="#e9d5ff"></button>
                            <button type="button" class="color-option w-8 h-8 rounded-full border-2 border-gray-300" style="background-color: #fed7aa" data-color="#fed7aa"></button>
                        </div>
                        <input type="hidden" id="selectedColor" name="color" value="#fef08a">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-yellow-400 text-gray-800 rounded-md hover:bg-yellow-500 transition-colors font-medium">
                            Save Note
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Note Modal -->
    <div id="viewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="viewTitle">Note Details</h3>
                    <button id="closeViewModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="viewContent" class="text-gray-700 whitespace-pre-wrap"></div>
            </div>
        </div>
    </div>

    <style>
        .sticky-note {
            width: 250px;
            min-height: 200px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-family: 'Comic Sans MS', cursive, sans-serif;
            position: absolute !important;
            z-index: 10;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .sticky-note:hover {
            z-index: 1000 !important;
            transform: scale(1.05);
        }
        
        #notesBoard {
            position: relative;
            overflow: visible !important;
        }
        
        .color-option.selected {
            border-color: #374151;
            border-width: 3px;
        }
        
        .bg-grid-pattern {
            background-image: 
                linear-gradient(rgba(0,0,0,.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,.1) 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>

    <script>
        // CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Initialize drag and drop functionality when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeDragAndDrop();
        });
        
        function initializeDragAndDrop() {
            // Make all sticky notes draggable
            document.querySelectorAll('.sticky-note').forEach(note => {
                makeDraggable(note);
            });
        }
        
        function makeDraggable(element) {
            let isDragging = false;
            let startX, startY, initialLeft, initialTop;
            
            // Get the note header for dragging
            const noteHeader = element.querySelector('.note-header');
            if (!noteHeader) return;
            
            noteHeader.style.cursor = 'move';
            
            noteHeader.addEventListener('mousedown', function(e) {
                // Don't drag if clicking on buttons
                if (e.target.closest('a') || e.target.closest('button') || e.target.closest('form')) {
                    return;
                }
                
                isDragging = true;
                startX = e.clientX;
                startY = e.clientY;
                
                // Get current position
                const rect = element.getBoundingClientRect();
                const parentRect = element.parentElement.getBoundingClientRect();
                initialLeft = rect.left - parentRect.left;
                initialTop = rect.top - parentRect.top;
                
                element.style.zIndex = '1000';
                element.style.opacity = '0.8';
                
                e.preventDefault();
            });
            
            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                
                e.preventDefault();
                
                const deltaX = e.clientX - startX;
                const deltaY = e.clientY - startY;
                
                const newLeft = Math.max(0, initialLeft + deltaX);
                const newTop = Math.max(0, initialTop + deltaY);
                
                element.style.left = newLeft + 'px';
                element.style.top = newTop + 'px';
            });
            
            document.addEventListener('mouseup', function(e) {
                if (!isDragging) return;
                
                isDragging = false;
                element.style.zIndex = '';
                element.style.opacity = '';
                
                // Save the new position
                const noteId = element.dataset.id;
                const newLeft = parseInt(element.style.left);
                const newTop = parseInt(element.style.top);
                
                savePosition(noteId, newLeft, newTop);
            });
        }
        
        async function savePosition(noteId, x, y) {
            try {
                const response = await fetch(`/notes/${noteId}/position`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        position_x: Math.max(0, x),
                        position_y: Math.max(0, y)
                    })
                });
                
                if (!response.ok) {
                    console.error('Failed to save position');
                }
            } catch (error) {
                console.error('Error saving position:', error);
            }
        }
    </script>
</x-app-layout>
