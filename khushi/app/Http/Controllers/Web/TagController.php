<?php

namespace App\Http\Controllers\Web;

use App\Models\Tag;
use App\Models\BlogPost;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::active()->withCount(['blogPosts', 'products'])->get();
        return view('web.tags.index', compact('tags'));
    }

    public function show(Tag $tag)
    {
        $blogPosts = BlogPost::published()
            ->whereJsonContains('tags', $tag->name)
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(12);

        $products = $tag->products()
            ->active()
            ->with(['category', 'reviews'])
            ->paginate(12);

        return view('web.tags.show', compact('tag', 'blogPosts', 'products'));
    }
}
