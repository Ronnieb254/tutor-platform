# Curriculum Filter Gaps — Investigation Report

**Date:** Investigation completed on current codebase state  
**Scope:** Kenyan (8-4-4, CBE Junior Secondary, CBE Senior School) vs International (IGCSE Cambridge, IGCSE Edexcel, IB MYP, IB Diploma) curriculum filtering across tutors, group sessions, clinics, and library.

---

## Summary (Key Findings)

| Area | Has `curriculum_id` column? | Controller filters by track? | Browse view filters by track? |
|---|---|---|---|
| **Tutors** | Via pivot (`tutor_curricula`) ✅ | `HomeController::tutors()` — YES (partial) ✅ | `tutors/index.blade.php` — YES ✅ |
| **Subjects / Categories** | `subjects.curriculum_id` added by migration ✅ | Seeders do NOT set it ❌ | Category dropdown shows all subjects regardless of track ❌ |
| **Group Sessions** | `examiner_clinics` — NO column ❌ | `GroupSessionController::index()` — NO ❌ | `pages.group-sessions.index` — NO ❌ |
| **Clinics (ExaminerClinic)** | `examiner_clinics` — NO column ❌ | `ClinicController::index()` — NO ❌ | `pages.examiner-clinic` — NO ❌ |
| **Library (blog_posts)** | `blog_posts` — NO column ❌ | `LibraryController::index()` — NO ❌ | `pages.library.index` — NO ❌ |
| **Session storage** | `session('curriculum_track')` | Set in 2 places only ✅ (partial) | Read in tutor index only ❌ |

**One-line verdict:** Curriculum filtering is implemented only for tutors. Every other public-facing browse page (group sessions, clinics, library) has zero track awareness. Subject lists carry a `curriculum_id` column in the DB but it is never populated by the seeders, and the tutor-search category/subject dropdown shows all subjects regardless of the user's selected track.

---

## 1. Tutors

### 1a. Index controller query — `HomeController::tutors()` + `tutors/index.blade.php`

The route `/tutors` is handled by `HomeController::tutors()` (`app/Http/Controllers/HomeController.php`, line 37–44). The controller only stores the track in session:

```php
// HomeController.php lines 40-42
if ($request->has('track') && in_array($request->track, ['kenyan', 'international'])) {
    session(['curriculum_track' => $request->track]);
}
return view('pages.tutors.index');
```

All query logic is embedded directly in the Blade view `resources/views/pages/tutors/index.blade.php` (lines 14–26). The track filtering IS applied there:

```php
// tutors/index.blade.php  ~line 21
$track = request('track', session('curriculum_track', ''));
if ($track && in_array($track, ['kenyan','international'])) {
    session(['curriculum_track' => $track]);
}
$q = TutorProfile::approved()->with(['user','subjects','approvedCurricula'])
    ...
    ->when($track, fn($x) => $x->whereHas('approvedCurricula', fn($c) => $c->where('track', $track)));
```

**Result:** The tutor list IS filtered by track when `?track=kenyan` or `?track=international` is present, using a `whereHas('approvedCurricula', ...)` join on the `tutor_curricula` pivot.

### 1b. `tutor_curricula` pivot — structure

Migration `2026_11_10_000001_create_curricula_tables.php` creates:

```
tutor_curricula:  id | tutor_profile_id | curriculum_id | verification_status | verified_at
```

`TutorProfile::approvedCurricula()` returns curricula where `pivot.verification_status = 'approved'`. The index view filters against `approvedCurricula` only, so unverified tutors are excluded from track-filtered results even if they registered for the curriculum.

### 1c. Tutor profile show page — curriculum badges

`resources/views/pages/tutors/show.blade.php` does NOT load or display curriculum badges. A search for `approvedCurricula`, `curriculum`, and `track` in that file returns no results. The profile shows only `$tutor->subjects` and stat bars. **No curriculum badges on individual tutor profile pages.**

### 1d. `TutorProfile` model

`app/Models/TutorProfile.php` defines both `curricula()` and `approvedCurricula()` relationships correctly. No scope for filtering by track exists on the model itself.

---

## 2. Subjects / Categories

### 2a. `curriculum_id` column on `subjects`

Migration `2026_11_10_000001_create_curricula_tables.php` (lines 63–69) adds a nullable `curriculum_id` FK to the `subjects` table:

```php
$table->foreignId('curriculum_id')->nullable()->after('category_id')
      ->constrained('curricula')->nullOnDelete();
```

The column exists in the schema.

### 2b. Seeders do not populate `curriculum_id`

`KenyanCurriculumSeeder.php` (the primary seeder called from `DatabaseSeeder`) inserts subjects using `DB::table('subjects')->insert([...])` with no `curriculum_id` field. Same for `KenyanCurriculumSeederSafe.php`. The `CurriculumSeeder.php` creates the curricula records but never back-fills `subjects.curriculum_id`.

**Result:** All `subjects` rows have `curriculum_id = NULL`. The column exists but is unused.

### 2c. `Subject` model

`app/Models/Subject.php` — `$fillable = ['category_id', 'name', 'slug', 'icon', 'is_active']`. `curriculum_id` is not in `$fillable`, and there is no `curriculum()` relationship.

### 2d. Subject list on tutor search is not track-filtered

The category/subject dropdown in `tutors/index.blade.php` loads:

```php
$categories = Category::active()->with('subjects')->get();
$subjects   = $catId ? Subject::where('category_id', $catId)->active()->get() : collect();
```

This loads all categories and subjects regardless of the selected track. A user on the "International" track sees Kenyan CBC categories (8-4-4 System, CBC Junior Secondary, etc.) in the dropdown.

### 2e. Categories have no `curriculum_id`

`categories` table (migration `2024_01_01_000002`) has no `curriculum_id`. The `Category` model has no curriculum relationship. Categories are logically curriculum-specific (the seeder names them "8-4-4 System (Form 1-4)", "CBC Junior Secondary", etc.) but there is no FK to enforce or filter by track.

---

## 3. Group Sessions

### 3a. `examiner_clinics.curriculum_id` — does not exist

The `examiner_clinics` table was originally created in migration `2024_01_01_000014_add_mwalimu_fields.php`. Subsequent migrations (`2026_10_16_000003`, `2026_10_17_*`, `2026_10_27_*`) add `is_group_session`, `series_id`, `paper_type`, `tutor_id`, BBB/Jitsi fields, etc. None adds a `curriculum_id` column. The latest curricula migration `2026_11_10_000001` also does not touch `examiner_clinics`. **No `curriculum_id` on `examiner_clinics`.**

### 3b. Public `GroupSessionController::index()` — no track filtering

`app/Http/Controllers/GroupSessionController.php` (lines 21–47):

```php
$sessions = ExaminerClinic::upcoming()
    ->groupSessions()
    ->with(['tutor', 'subject', 'enrollments'])
    ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
    ->when($search, ...)
    ->orderBy(...);
```

No `session('curriculum_track')` lookup. No curriculum join. All upcoming group sessions from all tracks are returned.

### 3c. Admin `Admin\GroupSessionController::index()` — no track filtering

`app/Http/Controllers/Admin/GroupSessionController.php` — filters only by `status`, `subject_id`, and `search`. No curriculum filtering.

### 3d. Tutor `Tutor\GroupSessionController` — no track filtering

`app/Http/Controllers/Tutor/GroupSessionController.php` — only scopes to `tutor_id`. No curriculum.

### 3e. Group sessions index view

`resources/views/pages/group-sessions/index.blade.php` — confirmed: no `curriculum`, no `track` references found. The subject dropdown loads `Subject::active()->with('category')->orderBy('name')->get()` (all subjects, no track filtering).

---

## 4. Clinics (ExaminerClinic)

### 4a. `examiner_clinics.curriculum_id` — does not exist

Same table as group sessions. No `curriculum_id` column has ever been added. See §3a above.

### 4b. `ClinicController::index()` — no track filtering

`app/Http/Controllers/ClinicController.php` (lines 18–38):

```php
$subjects = \App\Models\Subject::active()->with('category')->orderBy('name')->get();
$clinics  = ExaminerClinic::upcoming()
    ->clinics()
    ->with(['tutor', 'subject', 'enrollments'])
    ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
    ->when($paperType, ...)
    ->when($search, ...);
```

No `session('curriculum_track')` lookup. All upcoming clinics shown regardless of curriculum.

### 4c. Admin `ExaminerClinicController::index()` — no track filtering

`app/Http/Controllers/Admin/ExaminerClinicController.php` — filters by `status`, `subject_id`, date range, and `search` only. No curriculum filtering.

### 4d. Examiner clinic view

`resources/views/pages/examiner-clinic.blade.php` — no `curriculum` or `track` references found.

---

## 5. Library (blog_posts / resources)

### 5a. `blog_posts.curriculum_id` — does not exist

The `blog_posts` table was created in `2024_01_01_000012_create_blog_table.php`. Library columns were added in `2026_10_28_132804_add_library_columns_to_blog_tables.php`, adding `resource_type`, `file_path`, `file_size`, `subject_id`, `grade_level`, `download_count`, and `is_approved`. No `curriculum_id` was added to `blog_posts` in any migration.

### 5b. `LibraryController::index()` — no track filtering

`app/Http/Controllers/LibraryController.php` (lines 13–31):

```php
$query = BlogPost::published()->with(['author', 'category', 'subject']);
// filters: category, type (resource_type), search, grade_level
```

No `session('curriculum_track')`. No curriculum join. All published resources are shown to all users regardless of track.

### 5c. Admin `Admin\LibraryController` — no track filtering

Filters by `category`, `type`, `status`, and `search` only. No curriculum.

### 5d. Tutor `Tutor\LibraryController` — no track filtering

Scoped only by `author_id`. No curriculum.

### 5e. `BlogPost` model

`app/Models/BlogPost.php` — `$fillable` includes `subject_id` and `grade_level` but not `curriculum_id`. No curriculum relationship.

### 5f. Library view

`resources/views/pages/library/index.blade.php` — no `curriculum` or `track` references found.

---

## 6. Session Storage (`curriculum_track`)

The track is stored in session via two paths:

| File | Method | Line(s) | Trigger |
|---|---|---|---|
| `HomeController.php` | `tutors()` | 40–42 | GET `/tutors?track=kenyan` or `?track=international` |
| `CurriculumController.php` | `store()` | 26 | POST to `curriculum.store` (from home page card onclick) |

The session value `curriculum_track` is only **consumed** in one place: `resources/views/pages/tutors/index.blade.php` (line 14). No other controller or view reads `session('curriculum_track')`. 

Specifically: `GroupSessionController`, `ClinicController`, and `LibraryController` never call `session('curriculum_track')`.

---

## 7. Gap Summary

### Tables missing `curriculum_id`

| Table | Notes |
|---|---|
| `examiner_clinics` | Used for both group sessions and clinics. No curriculum column. |
| `blog_posts` | Library resources. No curriculum column. |
| `categories` | Subject categories are curriculum-specific by name but have no FK. |
| `subjects` | Column EXISTS in schema but seeders never populate it (always NULL). Subject model's `$fillable` also omits it. |

### Controllers not filtering by curriculum track

| Controller | Method | Public route |
|---|---|---|
| `GroupSessionController` (public) | `index()` | `GET /group-sessions` |
| `GroupSessionController` (admin) | `index()` | `GET /admin/group-sessions` |
| `GroupSessionController` (tutor) | `index()`, `create()` | `GET /tutor/group-sessions` |
| `ClinicController` (public) | `index()` | `GET /examiner-clinic` |
| `ExaminerClinicController` (admin) | `index()` | `GET /admin/examiner-clinics` |
| `LibraryController` (public) | `index()` | `GET /library` |
| `LibraryController` (admin) | `index()` | `GET /admin/library` |
| `LibraryController` (tutor) | `index()`, `create()` | `GET /tutor/library` |
| `HomeController` | `tutors()` | Session stored but query NOT in controller (in view) |

### Views showing all results regardless of track

| View | Route |
|---|---|
| `pages.group-sessions.index` | `/group-sessions` |
| `pages.examiner-clinic` | `/examiner-clinic` |
| `pages.library.index` | `/library` |
| `pages.tutors.index` (category/subject dropdown) | `/tutors` — subject options are not track-filtered |

---

## 8. Recommendations

### Priority 1 — Add `curriculum_id` to the missing tables

Run a new migration that adds:
1. `examiner_clinics.curriculum_id` — nullable FK to `curricula`. Default NULL for existing rows.
2. `blog_posts.curriculum_id` — nullable FK to `curricula`. Default NULL.
3. Optionally `categories.curriculum_id` — or filter by joining through `subjects.curriculum_id`.

### Priority 2 — Populate `subjects.curriculum_id`

Update the seeders (or write a one-off migration data-fix) to set `subjects.curriculum_id` based on their category. The six categories in the seeder map directly to kenyan-track curricula (`8-4-4`, `CBE Junior Secondary`, `CBE Senior School`). International subjects are not seeded at all — they need a separate seeder with IGCSE/IB subjects. Add `curriculum_id` to `Subject::$fillable`.

### Priority 3 — Apply track filtering in controllers

For each controller that has a public browse/index, read `session('curriculum_track')` and filter:

- `GroupSessionController::index()` — add `->when($track, fn($q) => $q->whereHas('subject', fn($s) => $s->where('curriculum_id', ...)))` or add `curriculum_id` directly to `examiner_clinics`.
- `ClinicController::index()` — same pattern.
- `LibraryController::index()` — filter by `blog_posts.curriculum_id` once the column exists.

Filtering via `examiner_clinics.curriculum_id` (direct column) is simpler and more efficient than joining through subjects.

### Priority 4 — Track-aware subject/category dropdowns

In the tutors index view, filter categories and subjects based on `$track`:
```php
$categories = $track
    ? Category::active()->whereHas('subjects', fn($q) => $q->where('curriculum_id', ...))->with('subjects')->get()
    : Category::active()->with('subjects')->get();
```

This requires subjects to have `curriculum_id` populated (Priority 2 above).

### Priority 5 — Curriculum badges on tutor profile page

Add `$tutor->load('approvedCurricula')` in `HomeController::tutorProfile()` and display badge chips in `pages/tutors/show.blade.php`, similar to the pattern already used in `pages/tutors/index.blade.php` (lines that render `$tutor->approvedCurricula`).

### Priority 6 — International subjects seeding

No international-curriculum subjects exist in any seeder. A new seeder is needed for IGCSE and IB subjects (Mathematics, Physics, Chemistry, Biology, Economics, Business Studies, etc.) with `curriculum_id` set to the appropriate IGCSE Cambridge / IGCSE Edexcel / IB MYP / IB Diploma record.

---

## File Reference Index

| File | Relevance |
|---|---|
| `app/Http/Controllers/HomeController.php` | `tutors()` stores track in session; `tutorProfile()` loads tutor for show page (no curricula eager load) |
| `app/Http/Controllers/CurriculumController.php` | `store()` — only place that validates and stores track from home page cards |
| `app/Http/Controllers/GroupSessionController.php` | Public group session browse — no curriculum filter |
| `app/Http/Controllers/ClinicController.php` | Public clinic browse — no curriculum filter |
| `app/Http/Controllers/LibraryController.php` | Public library browse — no curriculum filter |
| `app/Http/Controllers/Admin/ExaminerClinicController.php` | Admin clinic management — no curriculum filter |
| `app/Http/Controllers/Admin/GroupSessionController.php` | Admin group session management — no curriculum filter |
| `app/Http/Controllers/Admin/LibraryController.php` | Admin library — no curriculum filter |
| `app/Http/Controllers/Tutor/GroupSessionController.php` | Tutor group session management — no curriculum filter on create/index |
| `app/Http/Controllers/Tutor/LibraryController.php` | Tutor library — no curriculum filter |
| `app/Http/Controllers/TutorOnboardingController.php` | Correctly syncs `curriculum_ids` to `tutor_curricula` pivot on onboarding |
| `app/Models/TutorProfile.php` | Has `curricula()` and `approvedCurricula()` relationships |
| `app/Models/Subject.php` | `$fillable` omits `curriculum_id`; no `curriculum()` relationship |
| `app/Models/BlogPost.php` | `$fillable` omits `curriculum_id`; no `curriculum()` relationship |
| `app/Models/ExaminerClinic.php` | No `curriculum_id` in `$fillable`; no relationship |
| `app/Models/Category.php` | No `curriculum_id`; no relationship |
| `database/migrations/2026_11_10_000001_create_curricula_tables.php` | Adds `curriculum_id` to `subjects` and `bookings`; creates `tutor_curricula` pivot |
| `database/migrations/2026_10_28_132804_add_library_columns_to_blog_tables.php` | Adds library columns to `blog_posts` — no `curriculum_id` |
| `database/migrations/2024_01_01_000014_add_mwalimu_fields.php` | Creates `examiner_clinics` table — no `curriculum_id` |
| `database/seeders/KenyanCurriculumSeeder.php` | Seeds Kenyan subjects only; does NOT set `curriculum_id` on subjects |
| `database/seeders/CurriculumSeeder.php` | Seeds curricula records; does NOT back-fill `subjects.curriculum_id` |
| `resources/views/pages/tutors/index.blade.php` | Only view that reads `curriculum_track` from session and filters tutors |
| `resources/views/pages/tutors/show.blade.php` | No curriculum badges |
| `resources/views/pages/group-sessions/index.blade.php` | No curriculum filtering |
| `resources/views/pages/examiner-clinic.blade.php` | No curriculum filtering |
| `resources/views/pages/library/index.blade.php` | No curriculum filtering |
