<?php

namespace App\Http\Controllers\Web;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::published()->with(['author', 'category']);
        
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }
        
        if ($request->has('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        $posts = $query->latest('published_at')->paginate(12);
        $categories = Category::active()->get();
        $tags = Tag::active()->get();
        $featuredPosts = BlogPost::published()->featured()->limit(3)->get();
        
        return view('web.blog.index', compact('posts', 'categories', 'tags', 'featuredPosts'));
    }
    
    public function show(BlogPost $post)
    {
        if ($post->status !== 'published') {
            abort(404);
        }
        
        $post->incrementViews();
        $post->load(['author', 'category', 'comments.user']);
        
        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->limit(3)
            ->get();
            
        return view('web.blog.show', compact('post', 'relatedPosts'));
    }
    
    public function category(Category $category)
    {
        $posts = BlogPost::published()
            ->byCategory($category->id)
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(12);
            
        return view('web.blog.category', compact('posts', 'category'));
    }
}
