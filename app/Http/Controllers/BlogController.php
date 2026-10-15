<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;

class BlogController extends Controller
{
    public function index()
    {
        $posts      = BlogPost::published()->with(['author','category'])->latest('published_at')->paginate(12);
        $categories = BlogCategory::withCount(['posts' => fn($q) => $q->published()])->get();
        return view('pages.blog.index', compact('posts', 'categories'));
    }

    public function show(string $slug)
    {
        $post    = BlogPost::published()->where('slug', $slug)->with(['author','category'])->firstOrFail();
        $related = BlogPost::published()->where('blog_category_id', $post->blog_category_id)
            ->where('id', '!=', $post->id)->latest('published_at')->take(3)->get();
        return view('pages.blog.show', compact('post', 'related'));
    }
}
