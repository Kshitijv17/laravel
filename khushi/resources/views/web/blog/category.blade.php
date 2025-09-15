@extends('layouts.web')

@section('title', $category->meta_title ?: $category->name . ' - Blog - ' . config('app.name'))
@section('meta_description', $category->meta_description ?: 'Browse articles in ' . $category->name . ' category')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('blog.index') }}" class="hover:text-blue-600">Blog</a>
        <span class="mx-2">/</span>
        <span class="text-gray-700">{{ $category->name }}</span>
    </nav>

    <!-- Category Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">{{ $category->description }}</p>
        @endif
        <div class="mt-4">
            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                {{ $posts->total() }} {{ Str::plural('article', $posts->total()) }}
            </span>
        </div>
    </div>

    <!-- Posts Grid -->
    @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @foreach($posts as $post)
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" 
                             alt="{{ $post->title }}" 
                             class="w-full h-48 object-cover">
                    @endif
                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <span>{{ $post->published_at->format('M d, Y') }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $post->read_time }} min read</span>
                            <span class="mx-2">•</span>
                            <span>{{ $post->views_count }} views</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-blue-600">
                            <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $post->excerpt }}</p>
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
                               class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                Read More →
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $posts->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No articles in this category</h3>
            <p class="text-gray-600 mb-4">Check back later for new content.</p>
            <a href="{{ route('blog.index') }}" 
               class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Browse All Articles
            </a>
        </div>
    @endif

    <!-- Back to Blog -->
    <div class="text-center mt-12">
        <a href="{{ route('blog.index') }}" 
           class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to All Articles
        </a>
    </div>
</div>
@endsection
