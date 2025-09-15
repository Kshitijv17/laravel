<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['author', 'category']);
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        $posts = $query->latest()->paginate(15);
        $categories = Category::active()->get();
        
        return view('admin.blog.index', compact('posts', 'categories'));
    }
    
    public function create()
    {
        $categories = Category::active()->get();
        $authors = User::where('role', 'admin')->get();
        
        return view('admin.blog.create', compact('categories', 'authors'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'required|exists:users,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date'
        ]);
        
        // Handle image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }
        
        // Process tags
        if ($validated['tags']) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }
        
        // Set published_at if status is published
        if ($validated['status'] === 'published' && !$validated['published_at']) {
            $validated['published_at'] = now();
        }
        
        BlogPost::create($validated);
        
        return redirect()->route('admin.blog.index')->with('success', 'Blog post created successfully!');
    }
    
    public function show(BlogPost $post)
    {
        $post->load(['author', 'category', 'comments.user']);
        
        return view('admin.blog.show', compact('post'));
    }
    
    public function edit(BlogPost $post)
    {
        $categories = Category::active()->get();
        $authors = User::where('role', 'admin')->get();
        
        return view('admin.blog.edit', compact('post', 'categories', 'authors'));
    }
    
    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'required|exists:users,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date'
        ]);
        
        // Handle image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }
        
        // Process tags
        if ($validated['tags']) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }
        
        // Set published_at if status is published
        if ($validated['status'] === 'published' && !$validated['published_at']) {
            $validated['published_at'] = now();
        }
        
        $post->update($validated);
        
        return redirect()->route('admin.blog.index')->with('success', 'Blog post updated successfully!');
    }
    
    public function destroy(BlogPost $post)
    {
        // Delete featured image
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        
        $post->delete();
        
        return redirect()->route('admin.blog.index')->with('success', 'Blog post deleted successfully!');
    }
    
    // Comments management
    public function comments(Request $request)
    {
        $query = Comment::with(['post', 'user']);
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('post')) {
            $query->where('post_id', $request->post);
        }
        
        $comments = $query->latest()->paginate(20);
        $posts = BlogPost::select('id', 'title')->get();
        
        return view('admin.blog.comments', compact('comments', 'posts'));
    }
    
    public function approveComment(Comment $comment)
    {
        $comment->update(['status' => 'approved']);
        
        return redirect()->back()->with('success', 'Comment approved successfully!');
    }
    
    public function rejectComment(Comment $comment)
    {
        $comment->update(['status' => 'rejected']);
        
        return redirect()->back()->with('success', 'Comment rejected successfully!');
    }
    
    public function deleteComment(Comment $comment)
    {
        $comment->delete();
        
        return redirect()->back()->with('success', 'Comment deleted successfully!');
    }
}
