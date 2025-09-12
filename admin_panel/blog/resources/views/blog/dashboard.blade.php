<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Blog Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Welcome to Your Blog Dashboard!</h3>
                    <p class="mb-4">You are logged in as a Blog User. Here you can manage your blog posts and profile.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="bg-indigo-50 p-6 rounded-lg">
                            <h4 class="font-semibold text-indigo-800">My Posts</h4>
                            <p class="text-indigo-600 mt-2">Create and manage your blog posts</p>
                            <button class="mt-3 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                                Create New Post
                            </button>
                        </div>
                        
                        <div class="bg-orange-50 p-6 rounded-lg">
                            <h4 class="font-semibold text-orange-800">Profile Settings</h4>
                            <p class="text-orange-600 mt-2">Update your profile information</p>
                            <button class="mt-3 bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">
                                Edit Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
