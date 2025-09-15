<?php

namespace App\Http\Controllers\Web;

use App\Models\Comment;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
            'status' => 'approved' // Auto-approve for logged users
        ]);

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    public function storeGuest(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'author_name' => 'required|string|max:255',
            'author_email' => 'required|email|max:255',
            'author_website' => 'nullable|url|max:255',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        Comment::create([
            'post_id' => $post->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'],
            'author_website' => $validated['author_website'] ?? null,
            'status' => 'pending' // Require approval for guests
        ]);

        return redirect()->back()->with('success', 'Comment submitted for approval!');
    }
}
