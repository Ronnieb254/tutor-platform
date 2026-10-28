<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['author', 'category', 'subject']);

        if ($request->filled('category')) {
            $query->where('blog_category_id', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('resource_type', $request->type);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'published' => $query->where('is_published', true),
                'draft'     => $query->where('is_published', false),
                'pending'   => $query->where('is_approved', false),
                default     => null,
            };
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $resources = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'          => BlogPost::count(),
            'published'      => BlogPost::where('is_published', true)->count(),
            'pending'        => BlogPost::where('is_approved', false)->count(),
            'download_count' => BlogPost::sum('download_count'),
        ];

        $categories    = BlogCategory::all();
        $resourceTypes = BlogPost::RESOURCE_TYPES;

        return view('pages.admin.library', compact('resources', 'stats', 'categories', 'resourceTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'resource_type'      => 'required|in:' . implode(',', BlogPost::RESOURCE_TYPES),
            'blog_category_id'   => 'nullable|exists:blog_categories,id',
            'subject_id'         => 'nullable|exists:subjects,id',
            'grade_level'        => 'nullable|string|max:20',
            'excerpt'            => 'nullable|string',
            'content'            => 'nullable|string',
            'file'               => 'nullable|file|max:51200',
            'thumbnail'          => 'nullable|image|max:5120',
            'is_published'       => 'nullable|boolean',
        ]);

        $filePath  = null;
        $fileSize  = null;
        $thumbnail = null;

        if ($request->hasFile('file')) {
            $uploaded = $request->file('file');
            $filePath = $uploaded->store('library', 'public');
            $fileSize = $uploaded->getSize();
        }

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $isPublished = (bool) ($validated['is_published'] ?? false);
        $slug        = Str::slug($validated['title']) . '-' . Str::random(5);

        BlogPost::create([
            'author_id'        => auth()->id(),
            'blog_category_id' => $validated['blog_category_id'] ?? null,
            'subject_id'       => $validated['subject_id'] ?? null,
            'title'            => $validated['title'],
            'slug'             => $slug,
            'excerpt'          => $validated['excerpt'] ?? null,
            'content'          => $validated['content'] ?? null,
            'thumbnail'        => $thumbnail,
            'resource_type'    => $validated['resource_type'],
            'file_path'        => $filePath,
            'file_size'        => $fileSize,
            'grade_level'      => $validated['grade_level'] ?? null,
            'is_published'     => $isPublished,
            'published_at'     => $isPublished ? now() : null,
            'is_approved'      => true,
        ]);

        return redirect()->route('admin.library')
                         ->with('success', 'Resource created successfully.');
    }

    public function toggle(BlogPost $post)
    {
        $post->update([
            'is_published' => !$post->is_published,
            'published_at' => !$post->is_published ? now() : $post->published_at,
        ]);

        return redirect()->back()->with('success', 'Resource visibility updated.');
    }

    public function approve(BlogPost $post)
    {
        $post->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Resource approved.');
    }

    public function destroy(BlogPost $post)
    {
        if ($post->file_path && Storage::disk('public')->exists($post->file_path)) {
            Storage::disk('public')->delete($post->file_path);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Resource deleted.');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:50',
        ]);

        BlogCategory::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon'        => $validated['icon'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Category created.');
    }

    public function destroyCategory(BlogCategory $category)
    {
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted.');
    }
}
