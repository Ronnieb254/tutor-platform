<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $resources = BlogPost::where('author_id', $user->id)
                             ->with(['category', 'subject'])
                             ->latest()
                             ->paginate(15);

        $stats = [
            'total'          => BlogPost::where('author_id', $user->id)->count(),
            'published'      => BlogPost::where('author_id', $user->id)->where('is_published', true)->count(),
            'download_count' => BlogPost::where('author_id', $user->id)->sum('download_count'),
        ];

        return view('pages.tutor.library.index', compact('resources', 'stats'));
    }

    public function create()
    {
        $categories    = BlogCategory::all();
        $subjects      = Subject::active()->orderBy('name')->get();
        $resourceTypes = BlogPost::RESOURCE_TYPES;

        return view('pages.tutor.library.create', compact('categories', 'subjects', 'resourceTypes'));
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

        $slug = Str::slug($validated['title']) . '-' . Str::random(5);

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
            'is_published'     => true,
            'published_at'     => now(),
            'is_approved'      => true,
        ]);

        return redirect()->route('tutor.library.index')
                         ->with('success', 'Resource published successfully.');
    }

    public function destroy(Request $request, BlogPost $post)
    {
        if ($post->author_id !== $request->user()->id) {
            abort(403);
        }

        if ($post->file_path && Storage::disk('public')->exists($post->file_path)) {
            Storage::disk('public')->delete($post->file_path);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Resource deleted.');
    }
}
