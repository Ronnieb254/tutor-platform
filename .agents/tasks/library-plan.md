# Implementation Plan — Blog → Library Migration

## Context summary

- The blog uses two tables (`blog_categories`, `blog_posts`) in the migration
  `2024_01_01_000012_create_blog_table.php`. Both tables stay; we alter them
  idempotently to add library-specific columns.
- `BlogPost` / `BlogCategory` models live in `app/Models/`. They stay as the
  ORM layer; we just add fillable fields and helpers.
- The existing `BlogController` is registered at `/blog` and `/blog/{slug}`.
  It becomes a redirect shim pointing to the new `/library` routes.
- Three new controllers replace it: public `LibraryController`, tutor-scoped
  `Tutor\LibraryController`, admin-scoped `Admin\LibraryController`.
- All tutor dashboard views (group-sessions, subjects) share an identical
  inline sidebar pattern — no Blade layout component, just copy-and-extend.
  The new tutor library views must match that pattern exactly.
- The admin blog view uses `@extends('layouts.admin')`. The new admin library
  view keeps that extension.
- Routes are in `routes/web.php` — one flat file. New library routes go in the
  same file, immediately after the blog redirect block.
- Public nav links are rendered in both a desktop `@foreach` array and a
  mobile `@foreach` array inside `resources/views/partials/nav.blade.php`.
  Both arrays must be updated in one edit.
- The admin sidebar is inside `resources/views/layouts/admin.blade.php` under
  the "Content" nav-section. The "Blog Posts" item must be renamed to "Library".

---

## Plan

- [ ] 1. Add library columns to `blog_posts` and `blog_categories` via an
      idempotent migration.

      The migration must use `Schema::hasColumn()` guards so running it twice
      is safe. Columns to add to `blog_posts`:
        - `resource_type` ENUM('article','pdf','video','notes','past_paper')
          DEFAULT 'article'
        - `file_path` VARCHAR nullable  (storage path for uploaded file)
        - `file_size` BIGINT UNSIGNED nullable  (bytes)
        - `subject_id` FK → subjects.id nullable nullOnDelete
        - `grade_level` VARCHAR(20) nullable  (e.g. "Form 3", "Grade 8")
        - `download_count` UNSIGNEDINTEGER DEFAULT 0
        - `is_approved` BOOLEAN DEFAULT true  (admin can hold a tutor upload)

      Columns to add to `blog_categories`:
        - `description` TEXT nullable
        - `icon` VARCHAR(50) nullable  (Font Awesome class e.g. "fa-book")

      Files:
        `database/migrations/2024_XX_XX_000000_add_library_columns_to_blog_tables.php`
        (use `date('Y_m_d_His')` prefix in actual filename, e.g. today + sequence)

      Verify: `php artisan migrate` — runs without errors; run a second time
      and still exits cleanly.

---

- [ ] 2. Update `BlogPost` and `BlogCategory` models.

      `BlogPost`:
        - Add to `$fillable`: `resource_type`, `file_path`, `file_size`,
          `subject_id`, `grade_level`, `download_count`, `is_approved`
        - Add cast `'is_approved' => 'boolean'`, `'download_count' => 'integer'`
        - Add relationship: `public function subject()` → `belongsTo(Subject::class)`
        - Add scope: `scopeApproved($q)` → `$q->where('is_approved', true)`
        - Extend `scopePublished` to also require `is_approved = true`
        - Add const array: `RESOURCE_TYPES = ['article','pdf','video','notes','past_paper']`

      `BlogCategory`:
        - Add to `$fillable`: `description`, `icon`

      Files:
        `app/Models/BlogPost.php`
        `app/Models/BlogCategory.php`

      Verify: `php artisan tinker --execute="App\Models\BlogPost::count();"` —
      no error; `php artisan tinker --execute="App\Models\BlogCategory::count();"` —
      no error.

---

- [ ] 3. Create public `LibraryController`.

      Namespace: `App\Http\Controllers`
      File: `app/Http/Controllers/LibraryController.php`

      Methods:
      ```
      index()   — filter by ?category=, ?type=, ?search=, ?grade=
                  $resources = BlogPost::published()->approved()
                      ->with(['author','category','subject'])
                      ->when($request->category, fn($q,$v) => $q->where('blog_category_id',$v))
                      ->when($request->type,     fn($q,$v) => $q->where('resource_type',$v))
                      ->when($request->grade,    fn($q,$v) => $q->where('grade_level',$v))
                      ->when($request->search,   fn($q,$v) => $q->where('title','like',"%$v%")
                                                                  ->orWhere('excerpt','like',"%$v%"))
                      ->latest('published_at')->paginate(12)->withQueryString();
                  $categories = BlogCategory::withCount([...published+approved...])→get();
                  return view('pages.library.index', compact(...));

      show($slug) — increment download_count if not article type;
                    $resource = BlogPost::published()->approved()->where('slug',$slug)->firstOrFail();
                    $related  = same category, same resource_type, latest 3;
                    return view('pages.library.show', compact(...));

      download($slug) — auth required; increment download_count; return Storage::download($file_path);
      ```

      Route names this controller will serve:
        `library.index`, `library.show`, `library.download`

      Verify: step 5 (routes) must be done first; then `php artisan route:list | grep library`
      confirms the three routes exist.

---

- [ ] 4. Create `BlogController` redirect shim.

      Replace `app/Http/Controllers/BlogController.php` entirely.
      Both methods issue 301 redirects:
      ```php
      public function index()  { return redirect()->route('library.index', [], 301); }
      public function show($slug) { return redirect()->route('library.show', $slug, 301); }
      ```
      This preserves inbound links and any cached bookmarks.

      Files: `app/Http/Controllers/BlogController.php`

      Verify: after routes are registered (step 5), `curl -I /blog` returns
      `HTTP/1.1 301` with `Location: /library`.

---

- [ ] 5. Create `Tutor\LibraryController`.

      Namespace: `App\Http\Controllers\Tutor`
      File: `app/Http/Controllers/Tutor/LibraryController.php`

      Methods:
      ```
      index()   — auth()->user()'s own resources (BlogPost::where('author_id', auth()->id()))
                  paginate(15); return view('pages.tutor.library.index', ...)

      create()  — pass $categories and BlogPost::RESOURCE_TYPES to view
                  return view('pages.tutor.library.create', ...)

      store(Request $r)
                — validate: title required|max:255, blog_category_id nullable|exists,
                  resource_type required|in:article,pdf,video,notes,past_paper,
                  excerpt nullable|string, content nullable|string (required for articles),
                  file nullable|file|max:51200 (50 MB),
                  grade_level nullable|string|max:20, subject_id nullable|exists:subjects,id
                — if file uploaded: store to 'library' disk (public), set file_path & file_size
                — create BlogPost with author_id=auth()->id(), is_published=true,
                  published_at=now(), is_approved=true (tutor-posted auto-approved; admin
                  can unpublish later), slug = Str::slug($title).'-'.Str::random(5)
                — redirect()->route('tutor.library.index')->with('success','Resource uploaded!')

      destroy(BlogPost $post) — authorize: $post->author_id must equal auth()->id()
                — delete file from storage if exists; $post->delete()
                — redirect back with success
      ```

      Files: `app/Http/Controllers/Tutor/LibraryController.php`

      Verify: step 6 routes; `php artisan route:list | grep tutor.library`.

---

- [ ] 6. Create `Admin\LibraryController`.

      Namespace: `App\Http\Controllers\Admin`
      File: `app/Http/Controllers/Admin/LibraryController.php`

      Methods:
      ```
      index()   — all BlogPost records (no published filter), paginate(15)
                  pass $categories; return view('pages.admin.library', ...)

      store(Request $r)
                — same validation as tutor store + allow is_published checkbox
                — author_id = auth()->id()
                — redirect back with success

      toggle(BlogPost $post)
                — flip $post->is_published; save; redirect back

      approve(BlogPost $post)
                — $post->update(['is_approved'=>true]); redirect back

      destroy(BlogPost $post)
                — delete file; $post->delete(); redirect back

      storeCategory(Request $r)
                — validate name required|max:100, description nullable, icon nullable|max:50
                — BlogCategory::create([slug=Str::slug(name), ...])
                — redirect back

      destroyCategory(BlogCategory $category)
                — $category->delete(); redirect back
      ```

      Files: `app/Http/Controllers/Admin/LibraryController.php`

      Verify: step 7 routes; `php artisan route:list | grep admin.library`.

---

- [ ] 7. Register all routes in `web.php`.

      Add immediately after the existing blog routes (around line 28):

      ```php
      // Library (replaces blog)
      Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
      Route::get('/library/{slug}', [LibraryController::class, 'show'])->name('library.show');
      Route::middleware('auth')->get('/library/{slug}/download',
          [LibraryController::class, 'download'])->name('library.download');
      ```

      Add import at top:
      ```php
      use App\Http\Controllers\LibraryController;
      ```

      Inside the `role:tutor` middleware group (after tutor group-sessions block):
      ```php
      // Library (tutor posts resources)
      Route::get('/tutor/library',
          [App\Http\Controllers\Tutor\LibraryController::class, 'index'])
          ->name('tutor.library.index');
      Route::get('/tutor/library/create',
          [App\Http\Controllers\Tutor\LibraryController::class, 'create'])
          ->name('tutor.library.create');
      Route::post('/tutor/library',
          [App\Http\Controllers\Tutor\LibraryController::class, 'store'])
          ->name('tutor.library.store');
      Route::delete('/tutor/library/{post}',
          [App\Http\Controllers\Tutor\LibraryController::class, 'destroy'])
          ->name('tutor.library.destroy');
      ```

      Inside the `role:admin` prefix group (after existing blog route):
      ```php
      // Library management
      Route::get('/library',
          [App\Http\Controllers\Admin\LibraryController::class, 'index'])
          ->name('library');
      Route::post('/library',
          [App\Http\Controllers\Admin\LibraryController::class, 'store'])
          ->name('library.store');
      Route::post('/library/{post}/toggle',
          [App\Http\Controllers\Admin\LibraryController::class, 'toggle'])
          ->name('library.toggle');
      Route::post('/library/{post}/approve',
          [App\Http\Controllers\Admin\LibraryController::class, 'approve'])
          ->name('library.approve');
      Route::delete('/library/{post}',
          [App\Http\Controllers\Admin\LibraryController::class, 'destroy'])
          ->name('library.destroy');
      Route::post('/library/categories',
          [App\Http\Controllers\Admin\LibraryController::class, 'storeCategory'])
          ->name('library.categories.store');
      Route::delete('/library/categories/{category}',
          [App\Http\Controllers\Admin\LibraryController::class, 'destroyCategory'])
          ->name('library.categories.destroy');
      ```

      Keep existing `/blog` and `/blog/{slug}` routes in place (they now call
      the redirect shim in BlogController).

      Files: `routes/web.php`

      Verify: `php artisan route:list | grep library` — shows all 10+ library
      routes across public, tutor, and admin groups.

---

- [ ] 8. Create nine view files.

      ### 8a. `resources/views/pages/library/index.blade.php`
      Public library browser. Replaces `pages/blog/index.blade.php`.
      - Uses `@include('partials.nav')` + `@include('partials.footer')`
      - Hero section with title "Library — Learning Resources", subtitle,
        and icon `fa-book-open-reader`.
      - Filter bar (inline form GET): category dropdown, resource_type
        dropdown (labels: Article, PDF, Video, Notes, Past Paper), grade
        input, search input, Reset link.
      - Cards grid identical to blog card CSS but adds:
          - Resource type badge with icon per type
            (pdf→`fa-file-pdf` coral, video→`fa-video` blue, notes→`fa-sticky-note`
             gold, past_paper→`fa-file-alt` mint, article→`fa-newspaper` ink)
          - Download count badge if > 0
          - "Download" button linking to `library.download` for non-article types;
            "Read" for article types (both link to `library.show`)
      - Sidebar: category list with counts, resource type quick-filter links,
        "Find a Tutor" CTA (same pattern as blog sidebar).
      - Route name for pagination: preserve `withQueryString()` on paginator.

      ### 8b. `resources/views/pages/library/show.blade.php`
      Individual resource page. Replaces `pages/blog/show.blade.php`.
      - Back link → `route('library.index')`
      - If resource_type != 'article': show a download card with file size,
        a prominent "Download Resource" button → `route('library.download', $resource->slug)`,
        and note "Sign in to download" for guests (wrap button in `@auth/@else`).
      - If article: render content as before with `{!! nl2br(e($post->content)) !!}`.
      - Related resources sidebar: same as blog related, pointing to `library.show`.
      - All internal route references use `library.*` not `blog.*`.

      ### 8c. `resources/views/pages/tutor/library/index.blade.php`
      Tutor's own resources list. Full tutor dashboard sidebar layout
      (copy sidebar from `pages/tutor/group-sessions/index.blade.php` exactly,
      including the `x-data="{ sidebarOpen: false }"` wrapper and all CSS).
      Active sidebar item: a new "Library" item in a new `sb-section` labelled
      "Content" between "Group Classes" and "Examiner".
      - Stats row: Total Resources, Published, Downloads.
      - Table of own resources: Title, Type badge, Category, Date, Status, Actions
        (View on site | Delete form with `@method('DELETE')`).
      - "Upload Resource" button in topbar and in the empty state.

      ### 8d. `resources/views/pages/tutor/library/create.blade.php`
      Upload form. Same sidebar layout.
      - Form `method="POST" action="{{ route('tutor.library.store') }}" enctype="multipart/form-data"`
      - Fields:
          - Title (text, required)
          - Resource Type (select, required): maps RESOURCE_TYPES
          - Category (select from $categories, optional)
          - Subject (select from Subject::active()->orderBy('name')->get(), optional)
          - Grade Level (text input, placeholder "e.g. Form 3 / Grade 8")
          - Excerpt (text, optional)
          - Content (textarea, shown/hidden via Alpine `x-show="type==='article'"`,
            default hidden — only articles need content)
          - File (file input, shown/hidden via Alpine `x-show="type!=='article'"`,
            accept=".pdf,.mp4,.doc,.docx,.pptx,.png,.jpg,.jpeg,.zip",
            note max 50 MB)
          - Thumbnail (file input, accept="image/*")
          - Alpine `x-data="{ type: 'article' }"` on the form, bind select to `type`
      - Cancel link → `route('tutor.library.index')`

      ### 8e. `resources/views/pages/admin/library.blade.php`
      Admin library management. Extends `layouts.admin`.
      `@section('title', 'Library')`
      - Stats row: Total, Published, Pending Approval, Downloads.
      - Filter bar: category, resource_type, status (all / published / drafts /
        pending_approval).
      - Table columns: Title, Type, Author, Category, Grade, Date, Status, Actions
        (View | Approve if !is_approved | Publish/Unpublish toggle | Delete).
      - "New Resource" button opens modal (same pattern as blog modal but adds
        resource_type, file, subject_id, grade_level fields).
      - "Manage Categories" section below table: list of BlogCategories with
        inline "Delete" form; "Add Category" mini-form (name, description, icon).
      - All form actions use `admin.library.*` route names.

      ### 8f–8i. FOUR partial/shared views (can be simple single-file updates):

      **8f. `resources/views/partials/nav.blade.php`** — MODIFY, do not recreate.
        In BOTH the desktop `@foreach` array and the mobile `@foreach` array:
        - Replace `['blog.index', 'fa-newspaper', 'Blog']`
          with   `['library.index', 'fa-book-open-reader', 'Library']`
        Two occurrences, one per array.

      **8g. `resources/views/layouts/admin.blade.php`** — MODIFY, do not recreate.
        In the "Content" nav-section, replace the "Blog Posts" nav-item:
        - Change `route('admin.blog')` → `route('admin.library')`
        - Change `request()->routeIs('admin.blog')` → `request()->routeIs('admin.library*')`
        - Change label text "Blog Posts" → "Library"
        - Change the SVG icon to a book icon (reuse the subjects icon already there)

      **8h. `resources/views/pages/tutor/group-sessions/index.blade.php`** — MODIFY.
        In the sidebar, inside the `@if($isApproved)` block, after the
        "Group Classes" section, add a new section:
        ```html
        <div class="sb-section">
            <div class="sb-label">Content</div>
            <a href="{{ route('tutor.library.index') }}" class="sb-item">
                <!-- book-open SVG icon -->
                My Library
            </a>
            <a href="{{ route('tutor.library.create') }}" class="sb-item">
                <!-- plus-circle SVG icon -->
                Upload Resource
            </a>
        </div>
        ```
        Also add to `resources/views/pages/tutor-subjects/index.blade.php`
        (same sidebar, same location — this is noted as the same single change
        pattern applied to two files).

      **8i. Same sidebar injection into `pages/tutor/group-sessions/create.blade.php`
        and `pages/tutor/group-sessions/show.blade.php`** — both files have the
        identical sidebar block. Apply the same "Content" section insertion to
        both, keeping the same structure as 8h.

      Files to create:
        `resources/views/pages/library/index.blade.php`
        `resources/views/pages/library/show.blade.php`
        `resources/views/pages/tutor/library/index.blade.php`
        `resources/views/pages/tutor/library/create.blade.php`
        `resources/views/pages/admin/library.blade.php`

      Files to modify:
        `resources/views/partials/nav.blade.php`
        `resources/views/layouts/admin.blade.php`
        `resources/views/pages/tutor/group-sessions/index.blade.php`
        `resources/views/pages/tutor-subjects/index.blade.php`
        `resources/views/pages/tutor/group-sessions/create.blade.php`
        `resources/views/pages/tutor/group-sessions/show.blade.php`

      Verify: `php artisan view:clear` then load `/library` in browser;
      verify `/admin/library` loads with admin layout; verify tutor sidebar
      shows "My Library" link.

---

## Critical implementation details

### Field names (use exactly these)
| Column | Table | Type |
|---|---|---|
| `resource_type` | blog_posts | enum default 'article' |
| `file_path` | blog_posts | string nullable |
| `file_size` | blog_posts | bigInteger unsigned nullable |
| `subject_id` | blog_posts | foreignId nullable |
| `grade_level` | blog_posts | string(20) nullable |
| `download_count` | blog_posts | unsignedInteger default 0 |
| `is_approved` | blog_posts | boolean default true |
| `description` | blog_categories | text nullable |
| `icon` | blog_categories | string(50) nullable |

### Route names — complete list
| Route name | URI | Controller method |
|---|---|---|
| `library.index` | GET /library | LibraryController@index |
| `library.show` | GET /library/{slug} | LibraryController@show |
| `library.download` | GET /library/{slug}/download | LibraryController@download |
| `tutor.library.index` | GET /tutor/library | Tutor\LibraryController@index |
| `tutor.library.create` | GET /tutor/library/create | Tutor\LibraryController@create |
| `tutor.library.store` | POST /tutor/library | Tutor\LibraryController@store |
| `tutor.library.destroy` | DELETE /tutor/library/{post} | Tutor\LibraryController@destroy |
| `admin.library` | GET /admin/library | Admin\LibraryController@index |
| `admin.library.store` | POST /admin/library | Admin\LibraryController@store |
| `admin.library.toggle` | POST /admin/library/{post}/toggle | Admin\LibraryController@toggle |
| `admin.library.approve` | POST /admin/library/{post}/approve | Admin\LibraryController@approve |
| `admin.library.destroy` | DELETE /admin/library/{post} | Admin\LibraryController@destroy |
| `admin.library.categories.store` | POST /admin/library/categories | Admin\LibraryController@storeCategory |
| `admin.library.categories.destroy` | DELETE /admin/library/categories/{category} | Admin\LibraryController@destroyCategory |

### Relationships
- `BlogPost` → `belongsTo(Subject::class)` via `subject_id`
- `BlogPost` → `belongsTo(User::class, 'author_id')` (existing)
- `BlogPost` → `belongsTo(BlogCategory::class, 'blog_category_id')` (existing)

### File storage
- Disk: `public` (existing), subdirectory `library/`
- Store with: `$request->file('file')->store('library', 'public')`
- Download with: `Storage::disk('public')->download($post->file_path, $post->title . '.' . pathinfo($post->file_path, PATHINFO_EXTENSION))`

### Verification commands (in order)
1. `php artisan migrate` — migration runs cleanly
2. `php artisan route:list | findstr library` — all 14 routes listed
3. `php artisan view:clear && php artisan config:clear` — no errors
4. Load `/library` in browser — shows library index (not blog)
5. Load `/blog` in browser — redirects 301 to `/library`
6. Load `/admin/library` — shows admin library view with correct sidebar label
7. Load `/tutor/library` (as tutor) — shows tutor library dashboard
8. Load `/tutor/library/create` (as tutor) — upload form renders, Alpine toggle works
