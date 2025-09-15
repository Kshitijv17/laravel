@extends('layouts.web')

@section('title', 'Blog - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Hero Section -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Our Blog</h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Stay updated with the latest news, tips, and insights from our team
        </p>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('blog.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search articles..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="min-w-48">
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Search
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3">
            @if($featuredPosts->count() > 0 && !request()->hasAny(['search', 'category', 'tag']))
                <!-- Featured Posts -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Articles</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($featuredPosts as $featured)
                            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                @if($featured->featured_image)
                                    <img src="{{ asset('storage/' . $featured->featured_image) }}" 
                                         alt="{{ $featured->title }}" 
                                         class="w-full h-48 object-cover">
                                @endif
                                <div class="p-6">
                                    <div class="flex items-center text-sm text-gray-500 mb-2">
                                        <span>{{ $featured->published_at->format('M d, Y') }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $featured->read_time }} min read</span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-blue-600">
                                        <a href="{{ route('blog.show', $featured->slug) }}">{{ $featured->title }}</a>
                                    </h3>
                                    <p class="text-gray-600 text-sm">{{ $featured->excerpt }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Blog Posts -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    @if(request('search'))
                        Search Results for "{{ request('search') }}"
                    @elseif(request('category'))
                        {{ $categories->find(request('category'))->name ?? 'Category' }} Articles
                    @else
                        Latest Articles
                    @endif
                </h2>

                @if($posts->count() > 0)
                    <div class="space-y-8">
                        @foreach($posts as $post)
                            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                <div class="md:flex">
                                    @if($post->featured_image)
                                        <div class="md:w-1/3">
                                            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                 alt="{{ $post->title }}" 
                                                 class="w-full h-48 md:h-full object-cover">
                                        </div>
                                    @endif
                                    <div class="p-6 {{ $post->featured_image ? 'md:w-2/3' : 'w-full' }}">
                                        <div class="flex items-center text-sm text-gray-500 mb-2">
                                            <span>{{ $post->published_at->format('M d, Y') }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $post->read_time }} min read</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $post->views_count }} views</span>
                                            @if($post->category)
                                                <span class="mx-2">•</span>
                                                <a href="{{ route('blog.category', $post->category->slug) }}" 
                                                   class="text-blue-600 hover:text-blue-800">
                                                    {{ $post->category->name }}
                                                </a>
                                            @endif
                                        </div>
                                        <h3 class="text-xl font-semibold text-gray-900 mb-3 hover:text-blue-600">
                                            <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                        </h3>
                                        <p class="text-gray-600 mb-4">{{ $post->excerpt }}</p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                @if($post->author->avatar)
                                                    <img src="{{ asset('storage/' . $post->author->avatar) }}" 
                                                         alt="{{ $post->author->name }}" 
                                                         class="w-8 h-8 rounded-full mr-3">
                                                @endif
                                                <span class="text-sm text-gray-700">{{ $post->author->name }}</span>
                                            </div>
                                            <a href="{{ route('blog.show', $post->slug) }}" 
                                               class="text-blue-600 hover:text-blue-800 font-medium">
                                                Read More →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $posts->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No articles found</h3>
                        <p class="text-gray-600">Try adjusting your search criteria or browse all articles.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="space-y-8">
                <!-- Categories -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                    <ul class="space-y-2">
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('blog.category', $category->slug) }}" 
                                   class="flex items-center justify-between text-gray-600 hover:text-blue-600 py-1">
                                    <span>{{ $category->name }}</span>
                                    <span class="text-sm bg-gray-100 px-2 py-1 rounded">
                                        {{ $category->blog_posts_count ?? 0 }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Popular Tags -->
                @if($tags->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" 
                                   class="inline-block px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-blue-100 hover:text-blue-700 transition-colors">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Newsletter Signup -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Stay Updated</h3>
                    <p class="text-gray-600 text-sm mb-4">Subscribe to our newsletter for the latest articles and updates.</p>
                    <form action="#" method="POST" class="space-y-3">
                        @csrf
                        <input type="email" name="email" placeholder="Your email address" 
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
