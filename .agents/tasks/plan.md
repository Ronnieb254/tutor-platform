# Implementation Plan — Admin Group Sessions

## Codebase findings

- Admin views use **flat `.blade.php` files** in `resources/views/pages/admin/` (e.g. `clinics.blade.php`, `clinic-show.blade.php`, `clinic-create.blade.php`). The `examiner-clinics/` subdirectory referenced in the task prompt does not exist — only the flat files are present. The new group-sessions views follow the same flat pattern.
- Existing admin clinics use `ClinicController` (not `ExaminerClinicController`). `ExaminerClinicController` handles the separate `examiner-clinics` resource. The new `GroupSessionController` is modelled after `ExaminerClinicController` since group sessions live in the same `examiner_clinics` table.
- `ExaminerClinic` model has `scopeGroupSessions()`, `scopeClinics()`, and `isGroupSession()` already defined. The `is_group_session` column is protected by `Schema::hasColumn()` guards throughout.
- `User::isAdmin()` returns `true` for both `admin` and `super-admin` roles. Tutor list is fetched via `User::where('role', 'tutor')->...` pattern (confirmed in `clinic-create.blade.php` which passes `$tutors`).
- Admin route group: `Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(...)` — new group-session routes go inside this group.
- Admin sidebar "Content" section ends after `admin.blog` and before `admin.settings` — the new "Group Sessions" nav item goes between `admin.examiner-clinics.*` and `admin.settings`.
- `ExaminerClinicController::store()` sets `session_code = 'EXAM-' . Str::random(8)` and `status = 'upcoming'`. The new controller uses `'GRP-' . strtoupper(Str::random(8))` and `meeting_room = 'Mwalimu-group-' . Str::uuid()`.
- `Subject` model: `id`, `name`, `slug`, `icon`, `is_active`. Load with `Subject::where('is_active', true)->orderBy('name')->get()`.
- `ClinicEnrollment` model exists (referenced in `ExaminerClinic` relationships) — used on the show page to list enrolled students.
- Build / test: Laravel PHP project. Verification = `php artisan route:list | grep group-sessions` plus loading pages in a browser (no automated test suite discovered for controllers).

---

## Plan

- [ ] 1. Create `App\Http\Controllers\Admin\GroupSessionController`
      New file. Namespace `App\Http\Controllers\Admin`. Uses `ExaminerClinic`, `Subject`, `User`, `Request`, `Str`.
      All queries chain `->groupSessions()` (scopeGroupSessions). Store/update force `is_group_session = true`.

      Methods and their logic (mirrors `ExaminerClinicController` exactly, with group-session-specific overrides):

      ```php
      public function index(Request $request)
          // ExaminerClinic::with(['subject','tutor'])->groupSessions()->orderBy('scheduled_at','desc')
          // Filters: status, subject_id, search (title/description), from_date, to_date
          // paginate(20); pass $sessions, $subjects
          // return view('pages.admin.group-sessions.index', compact('sessions','subjects'))

      public function create()
          // $subjects = Subject::where('is_active',true)->orderBy('name')->get()
          // $tutors   = User::where('role','tutor')->orderBy('name')->get()
          // return view('pages.admin.group-sessions.create', compact('subjects','tutors'))

      public function store(Request $request)
          // validate: subject_id (required|exists:subjects,id), title (required|string|max:255),
          //           description (nullable|string), scheduled_at (required|date|after:now),
          //           duration_minutes (required|integer|min:15|max:480),
          //           max_students (nullable|integer|min:1|max:200),
          //           price (nullable|numeric|min:0),
          //           tutor_id (nullable|exists:users,id)
          // Set:
          //   $validated['is_group_session'] = true
          //   $validated['session_code']     = 'GRP-' . strtoupper(Str::random(8))
          //   $validated['meeting_room']     = 'Mwalimu-group-' . Str::uuid()
          //   $validated['created_by']       = auth()->id()
          //   $validated['status']           = 'scheduled'
          //   $validated['max_students']     = $validated['max_students'] ?? 30
          // ExaminerClinic::create($validated)
          // redirect()->route('admin.group-sessions.show', $session)->with('success',...)

      public function show(ExaminerClinic $groupSession)
          // load(['subject','tutor','enrollments.student'])  (student via ClinicEnrollment->belongsTo User as student_id)
          // abort-if !$groupSession->isGroupSession(), 404
          // $stats = [total_enrolled, spots_remaining, completion_rate]
          // return view('pages.admin.group-sessions.show', compact('groupSession','stats'))

      public function edit(ExaminerClinic $groupSession)
          // abort-if !$groupSession->isGroupSession(), 404
          // Guard: in_progress|completed|cancelled → redirect back with error
          // $subjects, $tutors
          // return view('pages.admin.group-sessions.edit', compact('groupSession','subjects','tutors'))

      public function update(Request $request, ExaminerClinic $groupSession)
          // abort-if !$groupSession->isGroupSession(), 404
          // Guard: completed|cancelled → redirect back with error
          // Same validation as store minus scheduled_at|after:now (allow past for rescheduling)
          // Prevent reducing max_students below enrollment count
          // Force is_group_session = true on update
          // redirect()->route('admin.group-sessions.show', $groupSession)->with('success',...)

      public function destroy(ExaminerClinic $groupSession)
          // abort-if !$groupSession->isGroupSession(), 404
          // Only allow deletion when status=scheduled AND enrollments=0
          // $groupSession->delete()
          // redirect()->route('admin.group-sessions.index')->with('success',...)

      public function cancel(ExaminerClinic $groupSession)
          // abort-if !$groupSession->isGroupSession(), 404
          // Guard: already completed|cancelled → back with error
          // $groupSession->update(['status'=>'cancelled'])
          // redirect back with success

      public function start(ExaminerClinic $groupSession)
          // abort-if !$groupSession->isGroupSession(), 404
          // Guard: status !== 'scheduled' → back with error
          // $groupSession->update(['status'=>'in_progress'])
          // redirect()->route('admin.group-sessions.show', $groupSession)->with('success',...)

      public function complete(ExaminerClinic $groupSession)
          // abort-if !$groupSession->isGroupSession(), 404
          // Guard: status !== 'in_progress' → back with error
          // $groupSession->update(['status'=>'completed'])
          // redirect back with success
      ```

      Files: `app/Http/Controllers/Admin/GroupSessionController.php`
      Verify: `php artisan route:list 2>&1 | Select-String "group-sessions"` — no output yet (routes not added), but file parses without error: `php artisan tinker --execute="new App\Http\Controllers\Admin\GroupSessionController;"` exits 0.

- [ ] 2. Register routes inside the admin group in `routes/web.php`
      Add the following block immediately after the `// Examiner Clinics management` block (around line 346 of the file, after the last `examiner-clinics.complete` route):

      ```php
      // Group Sessions management
      Route::get('/group-sessions',                               [App\Http\Controllers\Admin\GroupSessionController::class, 'index'])->name('group-sessions.index');
      Route::get('/group-sessions/create',                        [App\Http\Controllers\Admin\GroupSessionController::class, 'create'])->name('group-sessions.create');
      Route::post('/group-sessions',                              [App\Http\Controllers\Admin\GroupSessionController::class, 'store'])->name('group-sessions.store');
      Route::get('/group-sessions/{groupSession}',                [App\Http\Controllers\Admin\GroupSessionController::class, 'show'])->name('group-sessions.show');
      Route::get('/group-sessions/{groupSession}/edit',           [App\Http\Controllers\Admin\GroupSessionController::class, 'edit'])->name('group-sessions.edit');
      Route::patch('/group-sessions/{groupSession}',              [App\Http\Controllers\Admin\GroupSessionController::class, 'update'])->name('group-sessions.update');
      Route::delete('/group-sessions/{groupSession}',             [App\Http\Controllers\Admin\GroupSessionController::class, 'destroy'])->name('group-sessions.destroy');
      Route::post('/group-sessions/{groupSession}/cancel',        [App\Http\Controllers\Admin\GroupSessionController::class, 'cancel'])->name('group-sessions.cancel');
      Route::post('/group-sessions/{groupSession}/start',         [App\Http\Controllers\Admin\GroupSessionController::class, 'start'])->name('group-sessions.start');
      Route::post('/group-sessions/{groupSession}/complete',      [App\Http\Controllers\Admin\GroupSessionController::class, 'complete'])->name('group-sessions.complete');
      ```

      Route parameter name `{groupSession}` matches the controller method parameter `ExaminerClinic $groupSession` — Laravel resolves this via implicit model binding against `examiner_clinics.id`.

      Files: `routes/web.php`
      Verify: `php artisan route:list 2>&1 | Select-String "group-sessions"` — shows 10 routes all with `admin.group-sessions.*` names.

- [ ] 3. Create view: `resources/views/pages/admin/group-sessions/index.blade.php`
      Pattern: mirrors `pages/admin/clinics.blade.php` but adapted for group sessions.
      `@extends('layouts.admin')`, `@section('title','Group Sessions')`.
      - Stats row: Total, Scheduled, In Progress, Completed (from `$sessions` collection counts before pagination — pass as `$stats` from controller or compute inline from a second unpaginated query).
        **Decision**: pass `$stats` as a separate array from the controller's `index()` method (`ExaminerClinic::groupSessions()->selectRaw(...)` counts by status). This avoids loading all records into the view.
      - Filter bar: `GET` form with inputs for `search` (text), `status` (select: All/scheduled/in_progress/completed/cancelled), `subject_id` (select), `from_date`, `to_date`.
      - Table columns: Title / Subject, Scheduled, Tutor, Slots (enrolled/max with fill bar), Status badge, Actions.
      - Status badges use the same CSS variables as existing views: `badge-green` (completed), `badge-blue` (scheduled), `badge-amber` (in_progress), `badge-red` (cancelled).
      - Action buttons per row: View, Edit (if scheduled), Cancel (if not cancelled/completed), Delete (if scheduled + 0 enrolled).
      - Pagination block identical to `clinics.blade.php`.
      - "Create Group Session" button links to `route('admin.group-sessions.create')`.

      Files: `resources/views/pages/admin/group-sessions/index.blade.php`
      Verify: Navigate to `/admin/group-sessions` as admin — page loads without 500 error, table renders (even empty).

- [ ] 4. Create view: `resources/views/pages/admin/group-sessions/create.blade.php`
      Pattern: mirrors `pages/admin/clinic-create.blade.php`.
      `@extends('layouts.admin')`, `@section('title','Create Group Session')`.
      Form `POST` to `route('admin.group-sessions.store')`.
      Fields (all use the same inline-CSS form style as clinic-create):
      - `subject_id` — select from `$subjects` (required)
      - `title` — text input (required, max 255)
      - `description` — textarea (nullable)
      - `tutor_id` — select from `$tutors` filtered to `role=tutor` (nullable; "Optional — leave blank if TBD")
      - `scheduled_at` — datetime-local input (required)
      - `duration_minutes` — number input, default 60, min 30, max 480 (required)
      - `max_students` — number input, default 30, min 1, max 200 (nullable; info note: "Default 30 if left blank")
      - `price` — number input, default 0, min 0 (nullable)
      - Error display block at top (same style as clinic-create).
      - Info callout box: "Session code (GRP-XXXXXXXX) and meeting room are auto-generated on creation."
      - Back link → `route('admin.group-sessions.index')`.

      Files: `resources/views/pages/admin/group-sessions/create.blade.php`
      Verify: Navigate to `/admin/group-sessions/create` — page loads, form submits successfully with valid data and redirects to the show page.

- [ ] 5. Create view: `resources/views/pages/admin/group-sessions/show.blade.php`
      Pattern: mirrors `pages/admin/clinic-show.blade.php`.
      `@extends('layouts.admin')`, `@section('title', 'Group Session – ' . $groupSession->title)`.
      Two-column layout (2fr / 1fr) with mobile stack.

      Left column:
      - **Enrolled Students** card — table with columns: #, Student (name + email), Attended (tick/dash), Enrolled at. Show enrollment count vs max_students capacity bar.
      - Empty state if no enrollments.

      Right column (meta panel cards):
      - **Session Info** card: Status, Session Code (`GRP-...`), Meeting Room, Price (KSh), Duration, Max Students, Scheduled At, Created By.
      - **Tutor** card (if `$groupSession->tutor` set): name, email, "Launch Session" button → `route('examiner.clinics.join', $groupSession)` (reuses existing Jitsi join route since it accepts any ExaminerClinic).
      - **Subject** card: subject name, slug.

      Action buttons in page header (top-right):
      - "Edit" → `route('admin.group-sessions.edit', $groupSession)` — shown only when `status === 'scheduled'`
      - "Start Session" (POST form → `route('admin.group-sessions.start', $groupSession)`) — shown when `status === 'scheduled'`
      - "Mark Complete" (POST form → `route('admin.group-sessions.complete', $groupSession)`) — shown when `status === 'in_progress'`
      - "Cancel" (POST form with confirm → `route('admin.group-sessions.cancel', $groupSession)`) — shown when not cancelled/completed
      - "Delete" (DELETE form with confirm → `route('admin.group-sessions.destroy', $groupSession)`) — shown when `status === 'scheduled'` and 0 enrollments

      Back link → `route('admin.group-sessions.index')`.

      Files: `resources/views/pages/admin/group-sessions/show.blade.php`
      Verify: After creating a group session, clicking "View" from the index lands on the show page with all meta data visible.

- [ ] 6. Create view: `resources/views/pages/admin/group-sessions/edit.blade.php`
      Pattern: mirrors `create.blade.php` with pre-filled `old()`/model values.
      `@extends('layouts.admin')`, `@section('title','Edit Group Session')`.
      Form `PATCH` to `route('admin.group-sessions.update', $groupSession)` (include `@method('PATCH')`).
      Same fields as create, pre-populated from `$groupSession`:
      - `subject_id` — pre-select `$groupSession->subject_id`
      - `title` — pre-fill `$groupSession->title`
      - `description` — pre-fill `$groupSession->description`
      - `tutor_id` — pre-select `$groupSession->tutor_id`
      - `scheduled_at` — pre-fill `$groupSession->scheduled_at->format('Y-m-d\TH:i')`
      - `duration_minutes` — pre-fill `$groupSession->duration_minutes`
      - `max_students` — pre-fill `$groupSession->max_students`
      - `price` — pre-fill `$groupSession->price`
      - Session Code shown as read-only text (not an input): "Cannot be changed after creation."
      Back link → `route('admin.group-sessions.show', $groupSession)`.

      Files: `resources/views/pages/admin/group-sessions/edit.blade.php`
      Verify: Clicking "Edit" on a scheduled group session loads the form with correct pre-filled values. Submitting updates and redirects back to show page with success flash.

- [ ] 7. Add "Group Sessions" nav item to the admin sidebar in `layouts/admin.blade.php`
      In the "Content" `nav-section`, add the new item **after** the "Examiner Clinics" `<a>` element and **before** the "Blog Posts" `<a>` element.

      Insert exactly:
      ```blade
      <a href="{{ route('admin.group-sessions.index') }}" class="nav-item {{ request()->routeIs('admin.group-sessions*') ? 'active' : '' }}">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Group Sessions
      </a>
      ```
      (Uses the "group of people" SVG icon, consistent with the Tutor Approvals icon already used in the People section.)

      Files: `resources/views/layouts/admin.blade.php`
      Verify: Admin sidebar renders "Group Sessions" link between "Examiner Clinics" and "Blog Posts". Active state highlights when on any `admin.group-sessions.*` route.

- [ ] 8. Update `GroupSessionController::index()` to pass stats array
      Add a `$stats` array computed **before** the paginated query by running a grouped count:
      ```php
      $stats = ExaminerClinic::groupSessions()
          ->selectRaw('status, count(*) as total')
          ->groupBy('status')
          ->pluck('total', 'status')
          ->toArray();
      $stats['total'] = ExaminerClinic::groupSessions()->count();
      ```
      Pass `$stats` into the view via `compact('sessions', 'subjects', 'stats')`.
      This is part of item 1 if written together; list as separate item because the view (item 3) depends on the stats array shape being defined.

      Files: `app/Http/Controllers/Admin/GroupSessionController.php`
      Verify: The index view renders the stats row without undefined variable errors.

> **Note on `{groupSession}` route-model binding**: Laravel resolves `ExaminerClinic $groupSession` from the route key `{groupSession}` using the model's primary key. No custom `getRouteKeyName()` override is needed. The `abort-if !isGroupSession()` guard in each method prevents a non-group-session record from being manipulated through these routes even if someone crafts a URL with a regular clinic ID.

> **No migration needed**: `is_group_session` column already exists in `examiner_clinics` (in `ExaminerClinic::$fillable` and confirmed by `scopeGroupSessions()` / `scopeClinics()` guards).
