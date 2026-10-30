# Implementation Plan — Examiner Dashboard (Mwalimu Tutor Platform)

Task: Turn `ExaminerController::dashboard()` + `resources/views/pages/examiner-dashboard.blade.php`
into a complete, functional examiner dashboard that matches the booking show page
(`resources/views/pages/bookings/show.blade.php`) visual language exactly.

All paths below are relative to `c:/Users/Administrator/Downloads/mediacity.co.in-2026-10-13/mediacity.co.in-2026-10-13/tutor-platform/`.

---

## Key findings from the codebase (grounding for every step)

### How examiner status is determined (confirmed, not assumed)
- `ExaminerRegistration` model (`app/Models/ExaminerRegistration.php`): table `examiner_registrations`,
  fillable `['tutor_id','status','rejection_reason']`, method `isApproved(): bool` returns `$this->status === 'approved'`.
- The existing `dashboard()` already gates access: `$registration = ExaminerRegistration::where('tutor_id', $user->id)->first();`
  then `if (!$registration || !$registration->isApproved()) { redirect()->route('examiner.apply') }`.
- Decision: keep this gate exactly. The dashboard view is only ever rendered to an **approved** examiner,
  so the "not yet verified" edge case is handled by the redirect to `examiner.apply` (no new view branch needed).
  The hero will show an "Approved Examiner" badge built from `$registration->status`.

### Data model facts used (real field / relationship names)
- `ExaminerClinic` (`app/Models/ExaminerClinic.php`): owned via `created_by` (and `tutor_id`). Key fields:
  `title`, `name`, `subject_id` (→ `subject()`), `paper_type` (PP1/PP2/PP3), `scheduled_at` (cast datetime),
  `duration_minutes`, `price` (decimal:2), `max_students` (default 30), `status` ('scheduled','live','completed','cancelled'),
  `batch_number`. Relationships: `enrollments()` (hasMany `ClinicEnrollment`), `subject()` (belongsTo).
  Helpers: `enrolledCount()`, `isFull()`, `spotsLeft()`, `canJoin(User)`. Scope `upcoming()`: `where('scheduled_at','>',now())->where('status','scheduled')`.
- `ClinicEnrollment` (`app/Models/ClinicEnrollment.php`): fillable `['clinic_id','student_id','payment_id','session_code','attended']`,
  default timestamps (so `latest()` orders by `created_at`). Relationships: `clinic()` (belongsTo `ExaminerClinic`),
  `student()` (belongsTo `User`, key `student_id`), `payment()` (belongsTo `Payment`).
- `Payment` (`app/Models/Payment.php`): `payment_type` ('clinic'|'booking'), `tutor_amount`, `platform_amount`,
  `commission_rate`, `status` ('success' via `scopeSuccessful`). Clinic commission = 40% tutor / 60% platform
  (`calculateCommission()`), i.e. **tutor_amount is the amount minus the platform fee**.
- `EarningsService` (`app/Services/EarningsService.php`): `calculateTutorEarnings($user)` returns an array including
  `clinic_earnings` (sum of `tutor_amount` for successful clinic payments on clinics `created_by` the tutor) and
  `total_earnings`. `getRecentEarnings($user)` adds `clinic_earnings_30d`, `total_earnings_30d`.

### Layout decision (resolves the `@extends('layouts.app')` vs. design-system conflict)
- The task text says "Use `@extends('layouts.app')`", BUT `resources/views/layouts/app.blade.php` is a **generic Tailwind**
  page (gray/black nav, `bg-gray-50`, Tailwind CDN) that does NOT contain the Mwalimu design system.
- The booking show page — the authoritative design reference per the workflow notifications — does NOT use
  `@extends('layouts.app')`. It uses standalone `<!DOCTYPE html>` + `@include('partials.head')` + `@include('partials.nav')`
  + `@include('partials.footer')`.
- `resources/views/partials/head.blade.php` IS the Mwalimu design system: it loads Fraunces + DM Sans, Font Awesome 6.5.1,
  Alpine, and defines the full token set (`.btn`, `.btn-v`, `.btn-mint`, `.card`, `.card-p`, `.badge`, `.stat-card`,
  `.w`, `.section`, `.flash-ok`, CSS vars `--ink #15281F`, `--trust-blue #0A64BC`, `--mint #10B981`, `--gold #EEBE15`,
  `--coral #E8522A`, `--sidebar #111C16`, `--cream #FBF5E6`).
- Decision: **follow the booking show page pattern** — standalone HTML with `@include('partials.head')`,
  `@include('partials.nav')`, `@include('partials.footer')`, plus a page `<style>` block for dashboard-specific
  classes (hero gradient, code/pill styles, capacity bars). This is the only way to match the design system the
  notifications require. Do NOT use `@extends('layouts.app')`.
- Confirmed `resources/views/partials/footer.blade.php` exists; show.blade.php ends with `@include('partials.footer')`
  then a `<script>` block.

### Earnings widget decision
- The current view does `@include('components.earnings-widget', ['earnings' => $earnings])`, but that component
  (`resources/views/components/earnings-widget.blade.php`) uses Bootstrap classes (`card`, `row`, `col-md-4`,
  `progress`, `progress-bar`) that do NOT render under the Mwalimu partials (no Bootstrap).
- Decision: do NOT include `components.earnings-widget`. Render earnings inline in the Mwalimu design tokens
  (a dedicated stat card + optional small breakdown), using the numbers from `EarningsService`. Keep passing
  `$earnings` so sub-text (e.g. 30-day figure) is available.

### Routes (confirmed in routes/web.php lines 97–103)
- `examiner.apply` (GET `/examiner/apply`) — for the not-verified CTA.
- `examiner.dashboard` (GET `/examiner/dashboard`).
- `examiner.clinics.create` (GET `/examiner/clinics/create`).
- `examiner.clinics.store` (POST `/examiner/clinics`).
- `examiner.clinics.show` (GET `/examiner/clinics/{clinic}`) — use `route('examiner.clinics.show', $clinic)` (implicit binding).
- `examiner.clinics.join` (GET `/examiner/clinics/{clinic}/join`) — use `route('examiner.clinics.join', $clinic)`.
- Browse page: `route('examiner-clinic')`; standard: `route('dashboard')`, `route('home')` (used by nav partial).

---

- [ ] 1. Rewrite `ExaminerController::dashboard()` to query and pass all dashboard data.
      Keep the existing access gate (`ExaminerRegistration::where('tutor_id',$user->id)->first()` +
      `->isApproved()`, redirect to `examiner.apply` otherwise). Then build:
      - `$registration` (for the Approved badge).
      - `$totalClinics` = `ExaminerClinic::where('created_by',$user->id)->count()`.
      - `$totalStudents` = `ClinicEnrollment::whereHas('clinic', fn($q)=>$q->where('created_by',$user->id))->count()`.
      - `$upcomingCount` = `ExaminerClinic::where('created_by',$user->id)->upcoming()->count()` (scope = `scheduled_at > now()` AND `status='scheduled'`).
      - `$clinicEarnings` = `$earnings['clinic_earnings']` from `EarningsService::calculateTutorEarnings($user)` (this is the tutor's 40% = amount minus 60% platform fee).
      - `$upcomingClinics` = `ExaminerClinic::where('created_by',$user->id)->with('subject')->withCount('enrollments')->upcoming()->orderBy('scheduled_at','asc')->take(6)->get()`.
      - `$pastClinics` = `ExaminerClinic::where('created_by',$user->id)->with('subject')->withCount('enrollments')->where('scheduled_at','<',now())->orderBy('scheduled_at','desc')->take(10)->get()`.
      - `$recentEnrollments` = `ClinicEnrollment::whereHas('clinic', fn($q)=>$q->where('created_by',$user->id))->with(['student','clinic'])->latest()->take(8)->get()`.
      - `$earnings` = merged array from `EarningsService::calculateTutorEarnings($user)` + `getRecentEarnings($user)` (kept for sub-text; do NOT pass it to the Bootstrap earnings-widget component).
      `return view('pages.examiner-dashboard', compact('registration','totalClinics','totalStudents','upcomingCount','clinicEarnings','upcomingClinics','pastClinics','recentEnrollments','earnings'));`.
      Files: app/Http/Controllers/ExaminerController.php
      Verify: `php -l app/Http/Controllers/ExaminerController.php` → "No syntax errors detected".

- [ ] 2. Rewrite `resources/views/pages/examiner-dashboard.blade.php` as a standalone page using the Mwalimu partials + booking-show design language.
      Structure (replace the entire file — drop the old inline sidebar/Tailwind approach):
      - `<!DOCTYPE html><html lang="en"><head>@include('partials.head')<title>Examiner Dashboard – Mwalimu</title><style>…</style></head><body>`
      - `@include('partials.nav')`
      - Hero: `<div class="session-hero">` using `background:linear-gradient(135deg,#111C16,#15281F)` with a `::before` decorative circle (`rgba(10,100,188,.1)`, 260px, top-right), inside a `.w` container. Contents: a back arrow link (`route('dashboard')`, `fa-arrow-left`, `rgba(255,255,255,.5)`); a flex row with left column = "Approved Examiner" pill badge (`background:rgba(16,185,129,.2);color:#4ADE80;border:1px solid rgba(16,185,129,.3)` + `fa-circle-check`) + Fraunces `<h1>` "Welcome back, {{ $user->name }}"; right column = "Create New Clinic" button (`btn btn-mint`, `fa-plus`, `route('examiner.clinics.create')`).
      - Stats row: a responsive grid of 4 `.stat-card` cards using the design-system classes from `partials.head` (`.stat-card`, `.stat-icon`, `.stat-label`, `.stat-value`, `.stat-sub`). Cards: Total Clinics (`$totalClinics`, icon `fa-chalkboard-user`, blue icon bg), Total Students Enrolled (`$totalStudents`, `fa-users`, mint), Upcoming Sessions (`$upcomingCount`, `fa-calendar-days`, gold), Clinic Earnings (`KSh number_format($clinicEarnings,0)`, `fa-sack-dollar`, mint; sub-text "Your 40% share after platform fee").
      - Upcoming Clinics section: `.w` container, section heading (Fraunces) + count; render `$upcomingClinics` as list cards cloned from the `examiner-clinic.blade.php` pattern (54px `fa-chalkboard-user` gold icon tile, title, subject/paper pills `padding:3px 11px;border-radius:999px`, date `D, M j \a\t g:ia` / duration / `enrolledCount()/max_students` with a capacity bar `height:6px`). Actions per card: "Start Session" green pulse button (`.join-btn-live` style: `background:#10B981;animation:pulse-green 2s infinite`, `fa-video`, links `route('examiner.clinics.join',$clinic)`) shown when `in_array($clinic->status,['scheduled','live'])`; "View Details" outline button (`fa-eye`, `route('examiner.clinics.show',$clinic)`). Empty state: centered card `fa-calendar-xmark` + "No upcoming clinics" + Create New Clinic CTA.
      - Past Clinics section: a white card (`border-radius:22px;border:1px solid rgba(21,40,31,.08);padding:28px`) containing a table using the design-system `.tbl-wrap`/`thead th`/`tbody tr` pattern (defined in `partials.head`): columns Clinic (title + subject), Date, Enrolled, Earnings (`KSh number_format($clinic->price * 0.40 * enrolledCount,0)` approx, or just show price). Empty state row if `$pastClinics->isEmpty()`.
      - Recent Enrollments section: white card with a timeline/list (dot indicators `width:10px;height:10px;border-radius:50%` per booking-show pattern) showing `$recentEnrollments`: `$enrollment->student->name`, `$enrollment->clinic->title`, `$enrollment->created_at->format('M j, Y')`. Empty state if none.
      - Empty states: no-clinics (when `$totalClinics === 0`) → a prominent centered card "Create your first clinic" CTA to `examiner.clinics.create`; no-earnings (when `$clinicEarnings <= 0`) → stat shows "KSh 0" with sub-text "Earnings appear once students book paid clinics".
      - Flash messages: render `@if(session('success'))` / `info` / `error` using `.flash .flash-ok` / `.flash-info` / `.flash-err` classes from `partials.head`.
      - `@include('partials.footer')` then a closing `<script>` (only if needed for copy-to-clipboard; otherwise just close `</body></html>`).
      Use Font Awesome (`fa-solid fa-*`) consistently; Fraunces for headings, DM Sans for body (already loaded by `partials.head`); responsive grids via inline `grid-template-columns:repeat(auto-fit,minmax(220px,1fr))` with a `@media(max-width:768px)` fallback to 1fr (same technique as `examiner-clinic.blade.php`).
      Files: resources/views/pages/examiner-dashboard.blade.php
      Verify: `php artisan view:cache` → compiles with no errors (run `php artisan view:clear` after).

- [ ] 3. Run the full verification suite and record results.
      Run from the `tutor-platform` directory (cwd note: the planner's shell is PowerShell — use `;` not `&&`):
      - `php -l app/Http/Controllers/ExaminerController.php` → expect "No syntax errors detected".
      - `php artisan view:cache` → expect successful compile, no Blade errors (then `php artisan view:clear`).
      - `php artisan route:list --name=examiner` → expect `examiner.dashboard`, `examiner.clinics.create`, `examiner.clinics.show`, `examiner.clinics.join`, `examiner.apply`, `examiner.clinics.store` listed.
      - (If a DB/app boot is available) `php artisan route:list` boot check: confirm no fatal errors on app boot.
      Record each command's actual output in the step findings. Note any environment limitation (e.g. if `route:list` fails to boot due to missing `.env`/DB, state that the blade + PHP-lint checks still validate syntax and templates).
      Files: (no file changes — verification only; record results in the feature findings)
      Verify: all three commands above exit 0 / report success.

---

## Notes for the implementer
- Do NOT change `routes/web.php` or add routes. Do NOT touch the apply/create/show/join controller methods.
- The dashboard view only renders for approved examiners (controller enforces it), so an "Approved Examiner" badge is always truthful; there is no in-dashboard "pending" branch by design.
- `$clinic->enrollments_count` is available on `$upcomingClinics` and `$pastClinics` because both use `withCount('enrollments')`.
- For per-clinic earnings in the past-clinics table, the platform fee is 60% for clinics, so the examiner's share is `$clinic->price * 0.40` per enrolled student; use `$clinic->enrollments_count * ($clinic->price * 0.40)` for the per-clinic earnings column (display only; the authoritative total is `$clinicEarnings` from `EarningsService`).
- Keep using `$clinic->title` (fall back to `$clinic->name ?? $clinic->title`) and `$clinic->subject->name ?? 'N/A'`.
- The `pulse-green` keyframes and `.session-hero` style already appear in show.blade.php; re-declare them in this page's `<style>` block so the page is self-contained.
