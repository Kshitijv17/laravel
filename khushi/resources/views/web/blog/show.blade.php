@extends('layouts.web')

@section('title', $post->meta_title ?: $post->title . ' - ' . config('app.name'))
@section('meta_description', $post->meta_description ?: $post->excerpt)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('blog.index') }}" class="hover:text-blue-600">Blog</a>
        @if($post->category)
            <span class="mx-2">/</span>
            <a href="{{ route('blog.category', $post->category->slug) }}" class="hover:text-blue-600">
                {{ $post->category->name }}
            </a>
        @endif
        <span class="mx-2">/</span>
        <span class="text-gray-700">{{ $post->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <article class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                @if($post->featured_image)
                    <img src="{{ asset('storage/' . $post->featured_image) }}" 
                         alt="{{ $post->title }}" 
                         class="w-full h-64 md:h-96 object-cover">
                @endif
                
                <div class="p-8">
                    <!-- Article Header -->
                    <header class="mb-8">
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>
                        
                        <div class="flex flex-wrap items-center text-sm text-gray-500 mb-4">
                            <div class="flex items-center mr-6">
                                @if($post->author->avatar)
                                    <img src="{{ asset('storage/' . $post->author->avatar) }}" 
                                         alt="{{ $post->author->name }}" 
                                         class="w-10 h-10 rounded-full mr-3">
                                @endif
                                <div>
                                    <div class="text-gray-700 font-medium">{{ $post->author->name }}</div>
                                    <div class="text-xs">Author</div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4 text-sm">
                                <span>{{ $post->published_at->format('M d, Y') }}</span>
                                <span>•</span>
                                <span>{{ $post->read_time }} min read</span>
                                <span>•</span>
                                <span>{{ $post->views_count }} views</span>
                                @if($post->category)
                                    <span>•</span>
                                    <a href="{{ route('blog.category', $post->category->slug) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        {{ $post->category->name }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Tags -->
                        @if($post->tags && count($post->tags) > 0)
                            <div class="flex flex-wrap gap-2 mb-6">
                                @foreach($post->tags as $tag)
                                    <a href="{{ route('blog.index', ['tag' => $tag]) }}" 
                                       class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm hover:bg-blue-200 transition-colors">
                                        #{{ $tag }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </header>

                    <!-- Article Content -->
                    <div class="prose prose-lg max-w-none mb-8">
                        {!! $post->content !!}
                    </div>

                    <!-- Social Share -->
                    <div class="border-t border-gray-200 pt-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Share this article</h3>
                        <div class="flex space-x-4">
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(request()->url()) }}" 
                               target="_blank" 
                               class="flex items-center px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                                Twitter
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                               target="_blank" 
                               class="flex items-center px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Facebook
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" 
                               target="_blank" 
                               class="flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                                LinkedIn
                            </a>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="border-t border-gray-200 pt-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6">
                            Comments ({{ $post->comments->where('status', 'approved')->count() }})
                        </h3>

                        @auth
                            <!-- Comment Form -->
                            <form action="{{ route('blog.comments.store', $post) }}" method="POST" class="mb-8">
                                @csrf
                                <div class="mb-4">
                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                        Leave a comment
                                    </label>
                                    <textarea name="content" id="content" rows="4" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                              placeholder="Share your thoughts..." required></textarea>
                                </div>
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Post Comment
                                </button>
                            </form>
                        @else
                            <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                                <p class="text-gray-600">
                                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Login</a> 
                                    or 
                                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800">register</a> 
                                    to leave a comment.
                                </p>
                            </div>
                        @endauth

                        <!-- Comments List -->
                        @if($post->comments->where('status', 'approved')->count() > 0)
                            <div class="space-y-6">
                                @foreach($post->comments->where('status', 'approved')->where('parent_id', null) as $comment)
                                    <div class="bg-gray-50 rounded-lg p-6">
                                        <div class="flex items-start space-x-4">
                                            @if($comment->user && $comment->user->avatar)
                                                <img src="{{ asset('storage/' . $comment->user->avatar) }}" 
                                                     alt="{{ $comment->user->name }}" 
                                                     class="w-10 h-10 rounded-full">
                                            @else
                                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                                    <span class="text-gray-600 font-medium">
                                                        {{ substr($comment->user->name ?? $comment->author_name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-2">
                                                    <h4 class="font-medium text-gray-900">
                                                        {{ $comment->user->name ?? $comment->author_name }}
                                                    </h4>
                                                    <span class="text-sm text-gray-500">
                                                        {{ $comment->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <p class="text-gray-700">{{ $comment->content }}</p>
                                                
                                                <!-- Replies -->
                                                @if($comment->replies->where('status', 'approved')->count() > 0)
                                                    <div class="mt-4 space-y-4">
                                                        @foreach($comment->replies->where('status', 'approved') as $reply)
                                                            <div class="flex items-start space-x-4 pl-4 border-l-2 border-gray-200">
                                                                @if($reply->user && $reply->user->avatar)
                                                                    <img src="{{ asset('storage/' . $reply->user->avatar) }}" 
                                                                         alt="{{ $reply->user->name }}" 
                                                                         class="w-8 h-8 rounded-full">
                                                                @else
                                                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                                                        <span class="text-gray-600 text-sm font-medium">
                                                                            {{ substr($reply->user->name ?? $reply->author_name, 0, 1) }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                                <div class="flex-1">
                                                                    <div class="flex items-center space-x-2 mb-1">
                                                                        <h5 class="font-medium text-gray-900 text-sm">
                                                                            {{ $reply->user->name ?? $reply->author_name }}
                                                                        </h5>
                                                                        <span class="text-xs text-gray-500">
                                                                            {{ $reply->created_at->diffForHumans() }}
                                                                        </span>
                                                                    </div>
                                                                    <p class="text-gray-700 text-sm">{{ $reply->content }}</p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </article>

        <!-- Sidebar -->
        <aside class="lg:col-span-1">
            <div class="space-y-8">
                <!-- Related Posts -->
                @if($relatedPosts->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Articles</h3>
                        <div class="space-y-4">
                            @foreach($relatedPosts as $related)
                                <article class="flex space-x-3">
                                    @if($related->featured_image)
                                        <img src="{{ asset('storage/' . $related->featured_image) }}" 
                                             alt="{{ $related->title }}" 
                                             class="w-16 h-16 object-cover rounded">
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 hover:text-blue-600 mb-1">
                                            <a href="{{ route('blog.show', $related->slug) }}">{{ $related->title }}</a>
                                        </h4>
                                        <p class="text-xs text-gray-500">{{ $related->published_at->format('M d, Y') }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Author Bio -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">About the Author</h3>
                    <div class="flex items-start space-x-4">
                        @if($post->author->avatar)
                            <img src="{{ asset('storage/' . $post->author->avatar) }}" 
                                 alt="{{ $post->author->name }}" 
                                 class="w-16 h-16 rounded-full">
                        @endif
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $post->author->name }}</h4>
                            @if($post->author->bio)
                                <p class="text-sm text-gray-600 mt-2">{{ $post->author->bio }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Newsletter -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Stay Updated</h3>
                    <p class="text-gray-600 text-sm mb-4">Get the latest articles delivered to your inbox.</p>
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
        </aside>
    </div>
</div>
@endsection
