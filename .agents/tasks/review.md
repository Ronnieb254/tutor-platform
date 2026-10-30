# Examiner dashboard: stats, clinics, and enrollments

This change rewrites `ExaminerController::dashboard()` to compute four statistics — total clinics, total students enrolled, upcoming sessions count, and clinic earnings net of the platform fee — and to pass upcoming clinics, past clinics, and recent enrollments to the view. It then rewrites `pages/examiner-dashboard.blade.php` to render all of that in the Mwalimu design system: a stats row of four cards, an Upcoming Clinics list with Start Session / View Details actions, a Past Clinics table, and a Recent Enrollments timeline, plus empty states. Earnings come from `EarningsService` (clinic `tutor_amount`, i.e. the examiner's 40% share), and every clinic/enrollment query is scoped to the examiner's own clinics via `created_by`. The view deliberately follows the booking-show pattern (`@include` of `partials.head/nav/footer`) rather than `@extends('layouts.app')` — a documented choice, since `layouts/app.blade.php` is the generic Tailwind layout that does not carry the themed tokens.

**Watch for:** a latent 500 if any clinic's `subject` is null — the Past Clinics table accesses `$clinic->subject->name` without the nullsafe operator, and Laravel's error handler turns the resulting property-on-null warning into an `ErrorException`. Also, the Past Clinics query has no status filter, so cancelled and never-started clinics show up under "Past Clinics" with an estimated earnings figure.

**Verdict**: CHANGES_REQUESTED

## High-level view

The controller computes every required stat from real model fields and relationships and scopes all of it to the examiner's own clinics (`created_by = $user->id`), with earnings pulled from `EarningsService::clinic_earnings` (the 40% tutor share, i.e. amount minus platform fee). `totalStudents` counts enrollment rows rather than distinct students, so a student booked into several clinics is counted once per booking.

The view matches the Mwalimu design system exactly — Fraunces headings, DM Sans body, the `#111C16` hero, `#0A64BC` blue, `#10B981` mint, and the `#fff / 22px / rgba(21,40,31,.08) / 28px` card chrome — delivered through `@include('partials.head')` + `partials.nav` + `partials.footer`, the same self-contained pattern used by the booking-show page and every other examiner page. `@extends('layouts.app')` is intentionally not used: that layout is the generic Tailwind shell and lacks the themed tokens, so using it would break the design.

Past Clinics is the weak spot. Its query filters only on `scheduled_at < now()` with no status guard, so cancelled and scheduled-but-never-started clinics appear alongside completed ones under a heading that says "Clinics that have already taken place," each carrying a per-clinic earnings figure computed as `enrollments_count × price × 0.40`. That figure is a display estimate the plan marks as non-authoritative, but it counts every enrollment at full price, so unpaid/pending/refunded seats inflate it against the realized `clinic_earnings` shown in the stat card. The same table also reads `$clinic->subject->name ?? 'N/A'` without the nullsafe operator; an admin can hard-delete a `Subject` (the destroy guard checks bookings, not clinics), which orphans the relation and turns that access into a 500.

The "not verified" case is handled by redirecting unapproved examiners to `examiner.apply` rather than rendering an inline pending message — a documented, deliberate choice, since verified tutors are auto-approved on apply and there is no pending state to show.

I independently ran `php -l` on the controller (passes: "No syntax errors detected"), but the dashboard commit message and the task `plan.md` record only the *expected* verification commands, not their actual outputs — `view:cache` and `route:list` results are not captured anywhere, so the review can't confirm the template compiles without re-running it.

<details>
<summary>Issues (4)</summary>

1. **Unguarded subject access in Past Clinics (latent 500)** — `{{ $clinic->subject->name ?? 'N/A' }}` reads `->name` on a possibly-null `subject`; the upcoming list guards this with `@if($clinic->subject)` and recent enrollments use `?->`, but Past Clinics does not. An admin can hard-delete a `Subject` (`Admin\SubjectController::destroy` only blocks on bookings, not clinics), orphaning the relation; the property-on-null warning becomes an `ErrorException` and the dashboard 500s. Use the nullsafe operator: `$clinic->subject?->name ?? 'N/A'`.
2. **Past Clinics includes cancelled/never-started clinics with estimated earnings** — the query filters only `scheduled_at < now()` with no status guard, so cancelled and scheduled-but-never-started clinics appear under "Past Clinics" (subtitled "Clinics that have already taken place"), each showing an estimated earnings figure. Exclude `cancelled` (and consider restricting to completed/in-progress), or add a status column so cancelled rows are clearly not "taken place."
3. **Per-clinic earnings estimate can overstate realized earnings** — the Past Clinics "Earnings" column uses `enrollments_count × price × 0.40`, counting every enrollment at full price; unpaid/pending/refunded seats inflate it versus the realized `clinic_earnings` in the stat card. The plan marks this "display only," but the two figures on the same page can disagree. Label the column "Est. earnings" or pull the actual `tutor_amount` per clinic.
4. **Verification results not recorded** — the dashboard commit message and `plan.md` contain only the expected `php -l` / `view:cache` / `route:list` commands, not their outputs (the plan steps are still unchecked). Record the actual command output in the commit message or task findings so the review can trust it without re-running.

</details>

<details><summary>Details</summary>

### Dashboard data wiring and owner scoping

The controller keeps the existing `isApproved()` gate, then builds:

```
ExaminerController::dashboard()
  │  gate: ExaminerRegistration::isApproved()  → else redirect examiner.apply
  ├─ totalClinics    = ExaminerClinic::where(created_by, me)->count()
  ├─ totalStudents   = ClinicEnrollment::whereHas(clinic, created_by=me)->count()    // enrollments, not distinct students
  ├─ upcomingCount   = ExaminerClinic::where(created_by, me)->upcoming()->count()    // scheduled_at>now AND status=scheduled
  ├─ clinicEarnings  = EarningsService::calculateTutorEarnings(me)['clinic_earnings'] // sum(tutor_amount) for successful clinic payments
  ├─ upcomingClinics   ->with('subject')->withCount('enrollments')->upcoming()->take(6)
  ├─ pastClinics       ->with('subject')->withCount('enrollments')->where(scheduled_at<now)->take(10)   // ← no status filter
  └─ recentEnrollments ->with(['student','clinic'])->latest()->take(8)
```

`clinic_earnings` is the sum of `tutor_amount` over successful `payment_type='clinic'` payments on clinics `created_by` the tutor — the price minus the 60% platform fee, matching the stat card's "Your 40% share after platform fee."

### Design system via the partials pattern, not layouts.app

The view is a self-contained `<!DOCTYPE html>` document that `@include`s `partials.head`, `partials.nav`, and `partials.footer`, with a page-local `<style>` block for dashboard-only classes (`.session-hero`, `.start-btn`/pulse, `.cap-bar`, `.pill`, `.dash-stats`, `.clinic-row`). This is the same pattern the booking-show page and every other examiner page use, and it is the only way to get the themed tokens: `partials/head.blade.php` loads Fraunces + DM Sans, Font Awesome 6.5.1, and defines `--ink #15281F`, `--trust-blue #0A64BC`, `--mint #10B981`, `--sidebar #111C16`, plus the `.btn`/`.btn-mint`/`.btn-v`/`.stat-card`/`.flash`/`.tbl-wrap` classes the dashboard relies on. `@extends('layouts.app')` is deliberately not used — that layout is the generic Tailwind shell (`bg-gray-50`, gray/black nav, no Fraunces or themed colors), so extending it would lose the design system the task requires.

One icon deviates from the "fa-solid" guidance: the upcoming-row date uses `fa-regular fa-calendar`.

### Past Clinics: cancelled clinics, an estimated earnings column, and an unguarded relation

Three concerns sit in the Past Clinics section, which is the part most likely to mislead an examiner.

The query is `ExaminerClinic::where('created_by', $user->id)->with('subject')->withCount('enrollments')->where('scheduled_at', '<', now())->orderBy('scheduled_at', 'desc')->take(10)` — no status filter. A cancelled clinic (status set to `cancelled` by `Admin\ExaminerClinicController` or `Admin\ClinicController`) and a clinic still marked `scheduled` whose time simply passed both land in this list under the heading "Clinics that have already taken place." Each row shows an "Earnings" value, so a cancelled clinic appears with a positive earnings figure that almost certainly was never realized. Restricting to non-cancelled statuses (or adding a status column) fixes both the mislabeling and the spurious earnings.

The earnings column itself is `$enrolled * ($clinic->price * 0.40)`. The plan marks this "display only" with `clinic_earnings` authoritative, so it is a deliberate estimate — but it counts every enrollment at full price. Unpaid, pending, or refunded enrollments inflate it, and the same page's stat card shows the realized `clinic_earnings` summed from actual `tutor_amount`, so the two numbers can disagree. Labelling the column "Est. earnings" (or summing real `tutor_amount` per clinic) would make the estimate honest.

Finally, the subject cell is `{{ $clinic->subject->name ?? 'N/A' }}`. The `?? 'N/A'` does not prevent the throw: on a null `subject`, PHP 8 emits "Attempt to read property 'name' on null", and Laravel's error handler converts that warning into an `ErrorException`, so the page 500s rather than rendering "N/A." The upcoming list guards exactly this with `@if($clinic->subject)`, and recent enrollments use `?->` — so the unguarded Past Clinics access is an oversight, not a deliberate choice. A null subject is reachable: `Admin\SubjectController::destroy` deletes a `Subject` when it has no bookings, with no check for examiner clinics, so a clinic's `subject_id` can be orphaned. The fix is one character: `$clinic->subject?->name ?? 'N/A'`.

### Verification evidence

The dashboard commit (`a8e909d`) message is a one-liner with no verification output, and `plan.md` lists the `php -l` / `view:cache` / `route:list` commands only as expectations with the checkboxes still unchecked — no actual command output is recorded in any task artifact. I used the one allowed narrow spot-check to run `php -l app/Http/Controllers/ExaminerController.php`, which returned "No syntax errors detected"; the Blade template was not re-compiled here. Recording the `view:cache` and `route:list` outputs in the commit message or task findings would let the review confirm the template compiles and the routes resolve without re-running them.

</details>

<details><summary>File map</summary>

- `app/Http/Controllers/ExaminerController.php` — `dashboard()` rewritten to compute four stats (total clinics, total students enrolled, upcoming sessions, clinic earnings) and pass upcoming clinics, past clinics, and recent enrollments; all queries scoped to `created_by`. Other methods unchanged.
- `resources/views/pages/examiner-dashboard.blade.php` — full rewrite as a self-contained page using `@include('partials.head/nav/footer')`: hero, four stat cards, upcoming clinics (Start Session → `examiner.clinics.join`, View Details → `examiner.clinics.show`), past clinics table, recent enrollments timeline, and no-clinics / no-earnings / empty-sublist states.

Full diff: `git diff a617be3 a8e909d -- app/Http/Controllers/ExaminerController.php resources/views/pages/examiner-dashboard.blade.php`

</details>
