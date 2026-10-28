<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::published()->with(['author', 'category', 'subject']);

        if ($request->filled('category')) {
            $query->where('blog_category_id', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('resource_type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($request->filled('grade')) {
            $query->where('grade_level', $request->grade);
        }

        $resources      = $query->latest('published_at')->paginate(12)->withQueryString();
        $categories     = BlogCategory::withCount(['posts' => fn($q) => $q->published()])->get();
        $resourceTypes  = BlogPost::RESOURCE_TYPES;

        return view('pages.library.index', compact('resources', 'categories', 'resourceTypes'));
    }

    public function show(string $slug)
    {
        $resource = BlogPost::published()->where('slug', $slug)
                            ->with(['author', 'category', 'subject'])
                            ->firstOrFail();

        // Increment download_count for non-article file types
        if ($resource->resource_type !== 'article' && $resource->file_path) {
            $resource->increment('download_count');
        }

        $related = BlogPost::published()
                            ->where('blog_category_id', $resource->blog_category_id)
                            ->where('id', '!=', $resource->id)
                            ->latest('published_at')
                            ->take(3)
                            ->get();

        return view('pages.library.show', compact('resource', 'related'));
    }

    public function download(string $slug)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $resource = BlogPost::published()->where('slug', $slug)->firstOrFail();

        if (!$resource->file_path) {
            abort(404, 'No file attached to this resource.');
        }

        $resource->increment('download_count');

        $ext      = pathinfo($resource->file_path, PATHINFO_EXTENSION);
        $filename = $resource->title . '.' . $ext;

        return Storage::disk('public')->download($resource->file_path, $filename);
    }
}
