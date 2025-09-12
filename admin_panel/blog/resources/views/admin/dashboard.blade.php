<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome to Admin Panel!</h3>
                    <p class="mb-4">You are logged in as an Administrator.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h4 class="font-semibold text-blue-800">Users Management</h4>
                            <p class="text-blue-600 mt-2">Manage all registered users</p>
                        </div>
                        
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h4 class="font-semibold text-green-800">Blog Posts</h4>
                            <p class="text-green-600 mt-2">Manage blog content</p>
                        </div>
                        
                        <div class="bg-purple-50 p-6 rounded-lg">
                            <h4 class="font-semibold text-purple-800">Settings</h4>
                            <p class="text-purple-600 mt-2">System configuration</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
