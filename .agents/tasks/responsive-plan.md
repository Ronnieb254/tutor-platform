# Responsive Implementation Plan
## Mwalimu Tutor Platform — Full Mobile / Tablet / Desktop Pass

**Target breakpoints:** 320px+ (mobile), 768px+ (tablet), 1024px+ (desktop)  
**Approach decision:** Almost all pages use raw inline `style=""` attributes, not Tailwind classes. The entire codebase uses a custom CSS design-system (`partials/head.blade.php`). Therefore **Option A** applies everywhere: add `<style>` blocks with `@media` overrides. Option B (Tailwind responsive classes) is used **only** on the one file already using Tailwind — `layouts/app.blade.php`.  
**DataTables rule:** Wrap every `<table>` that feeds a DataTable in a `<div style="overflow-x:auto;">`. The Responsive extension already handles column hiding. The empty-tbody bug (DataTables TN/18) must also be fixed — add a `<tr><td colspan="N">No records found</td></tr>` inside every `@empty` block.

**Constraints** (enforced throughout):
- Do NOT change PHP logic, routes, or controller code.
- Preserve all element `id=` attributes — DataTables and JS hooks depend on them.
- Keep all Alpine.js `x-*` / `@click` / `x-data` attributes untouched.
- Keep all `wire:` Livewire attributes untouched.
- Do NOT alter desktop layout at ≥1024px — only add rules for smaller viewports.

---

## Phase 1 — Layouts & Navigation

- [ ] 1. **Fix mobile hamburger in `layouts/app.blade.php`**  
  The mobile hamburger button exists in the markup but has no Alpine `x-data` / `x-show` wiring, so the menu never opens. Add `x-data="{ mobileOpen: false }"` to the `<nav>` wrapper, toggle `mobileOpen` on the hamburger click, and add a hidden mobile drawer (`x-show="mobileOpen"`) containing the same links as the desktop nav. Also hide the desktop link group on small screens using the existing `hidden md:flex` pattern (already present). This file uses Tailwind CDN, so use Option B responsive classes.  
  Files: `resources/views/layouts/app.blade.php`  
  Verify: Open the app-layout pages (e.g. `/sessions`) on a 375px viewport — hamburger tap opens a dropdown with all nav links, tap again closes it.

- [ ] 2. **Nav partial (`partials/nav.blade.php`) — already responsive, minor polish**  
  Inspection shows this file is already well-structured: desktop links hide below 1024px via `.mw-desklinks`, hamburger shows via `.mw-ham`, and the mobile drawer uses Alpine `x-show`. The only gap: `.mw-signin` (Sign In text link) is `display:none` in mobile but the `@media` block in the trailing `<style>` already shows it at ≥1024px. No structural changes needed. Add one small fix: the announcement bar uses `display:flex` as an inline style — on narrow screens the dismiss button `position:absolute;right:16px` can overlap the text. Add a `@media(max-width:640px)` rule that adds `padding-right:48px` to the announcement bar's text span.  
  Files: `resources/views/partials/nav.blade.php`  
  Verify: On 320px the announcement bar text does not overlap the × button.

- [ ] 3. **Admin layout (`layouts/admin.blade.php`) — already has mobile sidebar**  
  Inspection confirms the admin layout has a full mobile sidebar implementation: `position:fixed;left:-260px` on `<= 1023px`, Alpine `sidebarOpen` toggle, overlay div, and `.sidebar-mobile-toggle` button that `display:none` on desktop and `display:flex` on mobile. The layout is functionally responsive. The only missing piece: the `.admin-content` padding `28px` is fine on desktop but cramped on very small phones. Add a `@media(max-width:639px)` rule inside the `<style>` block: `.admin-content { padding: 16px; }` and `.topbar { padding: 0 16px; }`.  
  Files: `resources/views/layouts/admin.blade.php`  
  Verify: Admin panel opens on a 375px screen, sidebar slides in/out, content has comfortable padding.

- [ ] 4. **Guest layout (`layouts/guest.blade.php`) — auth card already responsive**  
  The auth card already has `@media(max-width:480px) { .auth-card { padding:28px 20px; } }` and `max-width:440px;width:100%`. The `.social-grid` and `.role-grid` use `grid-template-columns:repeat(3,1fr)` which gets tight at 320px. Add a `@media(max-width:400px)` rule: `.social-grid { grid-template-columns:1fr; }` and `.role-grid { grid-template-columns:1fr 1fr; }`. Also add `padding: 16px 12px` to `.guest-header` on mobile.  
  Files: `resources/views/layouts/guest.blade.php`  
  Verify: Login/register pages render without horizontal overflow on a 320px screen.

- [ ] 5. **Footer partial (`partials/footer.blade.php`) — already responsive via class**  
  The footer grid uses `.mw-foot-grid` with `@media` breakpoints already defined at the bottom of the file (1fr → 2-col at 640px → 4-col at 1024px). The cookie banner uses `width:calc(100% - 32px);max-width:460px` and is already mobile-safe. The bottom bar uses `flex-wrap:wrap;gap:10px`. No changes needed.  
  Files: none — already correct.  
  Verify: Footer stacks into single column on 375px.

---

## Phase 2 — Public Pages

- [ ] 6. **Homepage (`pages/home.blade.php`) — fix hero booking widget and pricing grid**  
  Issues found:
  - Hero: the quick-booking form uses `flex-wrap:wrap` already — OK.
  - Pricing grid `.price-g` uses `repeat(2,1fr)` at mobile and `repeat(4,1fr)` at 768px — fine, but on 320px two cards can be too tight. The existing `<style>` block reads `.price-g{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:768px){...}`. Change the base rule to `grid-template-columns:1fr!important` and keep `repeat(2,1fr)` starting at 480px, `repeat(4,1fr)` at 768px.
  - The `.hero-vis` floating tutor-card section is already `display:none` (commented-out responsive toggle). No action needed.
  - Trust stats row uses `flex-wrap:wrap;gap:24px` — fine.
  - All section grids (`.feat-g`, `.steps-g`, `.ec-g`, `.tc-g`, `.test-g`) already have inline `<style>` blocks with mobile→desktop column progression. Verify they exist and add any missing mobile `1fr` base rules.  
  Files: `resources/views/pages/home.blade.php`  
  Verify: Pricing section shows 1 card per row at 320px, 2 at 480px, 4 at 768px+.

- [ ] 7. **Tutor listing (`pages/tutors/index.blade.php`) — filter bar and card grid**  
  Issues found:
  - Filter bar is a `flex-wrap:wrap` row — already adaptive. The `.w` container handles max-width.
  - Tutor cards `.tc` are rendered in a plain `div` without a grid class. The listing currently appears to use no explicit grid on the outer container, so cards stack vertically (1 per row). Add a wrapping grid: `display:grid;grid-template-columns:1fr;gap:20px` at mobile, `repeat(2,1fr)` at 640px, `repeat(3,1fr)` at 1024px. Apply via a `<style>` block targeting the tutor card container (give it a class `tc-grid`).
  - Duration pills `.dur-pills` inside each card use `repeat(4,1fr)` — on mobile this can overflow the card. Add `@media(max-width:400px){.dur-pills{grid-template-columns:repeat(2,1fr);}}`.
  - The hero band `.hero-band` section uses `padding:52px 0` — fine.  
  Files: `resources/views/pages/tutors/index.blade.php`  
  Verify: Tutor cards stack 1-col on mobile, 2-col on tablet, 3-col on desktop.

- [ ] 8. **Tutor profile (`pages/tutors/show.blade.php`) — session tier cards and booking panel**  
  Issues found:
  - The profile hero header uses `flex-wrap:wrap` — fine.
  - Tab strip `.ptab` buttons use `white-space:nowrap` — on mobile they can overflow the tab container. Wrap tabs in a `overflow-x:auto;display:flex;-webkit-overflow-scrolling:touch` container so they scroll horizontally on small screens.
  - The main layout below the hero uses a two-column grid (`grid-template-columns:1fr 360px` or similar). On mobile it needs to collapse to single-column. Add a `session-grid` class to the grid container and a responsive `<style>` block: `1fr` at mobile, `1fr 360px` at 1024px+.
  - Session tier cards are already `display:block;width:100%` — fine.
  - The inline `padding:0 24px @media(min-width:1024px){padding:0 48px}` is PHP-in-style-string — this is a malformed CSS rule. Fix by removing the at-rule from the inline style and instead using the `.w` container class from `partials/head.blade.php`.  
  Files: `resources/views/pages/tutors/show.blade.php`  
  Verify: Profile page renders in single-column below 1024px; tabs scroll horizontally on 375px.

---

## Phase 3 — Dashboards

All three dashboards (tutor, student, parent) share the same CSS pattern: fixed sidebar + main content. The mobile sidebar already works (confirmed by reading the CSS: `position:fixed;left:-256px` with Alpine `sidebarOpen` toggle). The main issues are the inner content grids.

- [ ] 9. **Tutor dashboard (`pages/dashboard/tutor.blade.php`) — content grids and DataTables**  
  Issues found:
  - `.stats-g` grid: already has `repeat(2,1fr)` at mobile and `repeat(4,1fr)` at 768px — fine.
  - `.quick-g` grid: same pattern — fine.
  - Bookings tab stat cards: `grid-template-columns:repeat(4,1fr)` hardcoded inline with no responsive override. Add a `<style>` block (or inline class): `1fr 1fr` at mobile, `repeat(4,1fr)` at 768px.
  - All DataTables (`#payouts-related`, session history) need their wrapper `<div style="overflow-x:auto;">` checked — the `table-wrap` / `tbl-wrap` CSS class already sets `overflow-x:auto` in `partials/head.blade.php`. Verify each table is wrapped in a `<div class="tbl-wrap">` or `<div style="overflow-x:auto;">`.
  - Payout request form: uses `flex-wrap:wrap` — fine.
  - Earnings tab shows `<div style="overflow-x:auto;">` already wrapping tables per the view's CSS.  
  Files: `resources/views/pages/dashboard/tutor.blade.php`  
  Verify: Tutor dashboard opens at 375px without horizontal overflow; booking stat grid stacks 2×2 on mobile.

- [ ] 10. **Student dashboard (`pages/dashboard/student.blade.php`) — session cards on mobile**  
  Issues found:
  - `.stats-g` and `.quick-g` already have mobile-responsive grids.
  - Upcoming session card rows use `display:flex;flex-wrap:wrap` — already adaptive.
  - Session history `<table id="student-sessions-table">` is wrapped in `<div style="overflow-x:auto;">` — confirmed in source. DataTables Responsive extension handles the rest.
  - The `@empty` block inside `#student-sessions-table` renders blank (`@empty` followed immediately by `@endforelse`), which triggers the DataTables TN/18 column count mismatch. Fix: inside `@empty`, add `<tr><td colspan="8" style="text-align:center;padding:32px;color:var(--muted);">No sessions yet</td></tr>`.
  - Same fix needed for `#student-payments-table` `@empty` block.  
  Files: `resources/views/pages/dashboard/student.blade.php`  
  Verify: Student dashboard shows "No sessions yet" message instead of DataTables warning when no records exist.

- [ ] 11. **Parent dashboard (`pages/dashboard/parent.blade.php`) — DataTables empty state**  
  Issues found:
  - Same sidebar/mobile pattern, already working.
  - `#parent-upcoming-sessions-table` `@empty` block is blank → DataTables TN/18. Fix: add `<tr><td colspan="9" ...>No sessions yet</td></tr>` in each `@empty` block.
  - Same for `#parent-payments-table` and `#parent-refunds-table`.
  - Children listing cards use `display:flex` with `flex-wrap` — fine on mobile.
  - Quick actions grid `.quick-g` already has `repeat(2,1fr)` → `repeat(4,1fr)` at 768px.  
  Files: `resources/views/pages/dashboard/parent.blade.php`  
  Verify: Parent dashboard with 0 bookings shows no DataTables warning.

---

## Phase 4 — Admin Pages

All admin pages extend `layouts/admin.blade.php` which already handles mobile sidebar. The inner page content uses the admin CSS design system. Key issues are fixed-width stat grids and DataTables empty states.

- [ ] 12. **Admin payouts (`pages/admin/payouts.blade.php`) — stat grid + DataTables empty state**  
  Issues found:
  - Summary stats: `grid-template-columns:repeat(4,1fr)` hardcoded inline. On mobile this creates 4 tiny boxes. Add a responsive wrapper: `1fr 1fr` at mobile, `repeat(4,1fr)` at 768px.
  - Amount summary row: `grid-template-columns:1fr 1fr` — fine on tablet+, but on mobile add `grid-template-columns:1fr` at 480px and below.
  - `#payouts-table` `@empty` block is blank → DataTables TN/18. The table has 6 columns (`<th>` count). Fix: add `<tr><td colspan="6" style="text-align:center;padding:32px;color:var(--muted);">No payout requests found</td></tr>` in `@empty`.  
  Files: `resources/views/pages/admin/payouts.blade.php`  
  Verify: Payouts page with 0 records: no DataTables column-count warning.

- [ ] 13. **Admin bookings (`pages/admin/bookings.blade.php`) — stat grid + DataTables empty state**  
  Issues found:
  - Status stat bar: `grid-template-columns:repeat(4,1fr)` inline. Add responsive: `1fr 1fr` at mobile, `repeat(4,1fr)` at 768px.
  - Filter form uses `flex-wrap:wrap` — already adaptive.
  - `#bookings-table` has 8 columns. Verify `@empty` block contains a colspan=8 row.  
  Files: `resources/views/pages/admin/bookings.blade.php`  
  Verify: Bookings page stats wrap to 2×2 grid on mobile.

- [ ] 14. **Admin blog (`pages/admin/blog.blade.php`) — stat grid + DataTables empty state**  
  Issues found:
  - Stats: `grid-template-columns:repeat(3,1fr)` inline. Add responsive: `1fr` at mobile, `repeat(3,1fr)` at 640px.
  - `#blog-table` has 6 columns. `@empty` block is blank → DataTables TN/18. Fix: add `<tr><td colspan="6" ...>No posts found</td></tr>`.
  - New-post modal uses `position:fixed;inset:0` with a centered card — already mobile-safe. Add `overflow-y:auto` to the modal card for very small screens.  
  Files: `resources/views/pages/admin/blog.blade.php`  
  Verify: Blog admin with 0 posts shows no DataTables warning.

- [ ] 15. **Admin group-sessions (`pages/admin/group-sessions/index.blade.php`) — stat grid + DataTables empty state**  
  Issues found:
  - `.stats-grid` is defined in `admin.blade.php` as `repeat(2,1fr)` → `repeat(4,1fr)` at 1200px. The page overrides it inline with `style="margin-bottom:20px;"` but inherits the `.stats-grid` class — that's fine.
  - `#group-sessions-table` has 7 columns. `@empty` block is blank → DataTables TN/18. Fix: add `<tr><td colspan="7" ...>No group sessions found</td></tr>`.  
  Files: `resources/views/pages/admin/group-sessions/index.blade.php`  
  Verify: Group sessions admin with 0 sessions shows no DataTables warning.

- [ ] 16. **Admin users, categories, subjects, clinics, settings, library, clinic-show (`pages/admin/*.blade.php`)**  
  Same pattern across all remaining admin pages:
  - Ensure every `@empty` block inside a DataTable's `<tbody>` has a `<tr><td colspan="N">No records found</td></tr>` where N matches the `<thead>` column count.
  - `pages/admin/users.blade.php` — 6 columns.
  - `pages/admin/categories.blade.php` — check thead count.
  - `pages/admin/subjects.blade.php` — check thead count.
  - `pages/admin/clinics.blade.php` — check thead count.
  - `pages/admin/clinic-show.blade.php` — check thead count.
  - `pages/admin/library.blade.php` — check thead count.
  - Any `grid-template-columns:repeat(3,1fr)` or `repeat(4,1fr)` stat grids get a `<style>` block wrapping to 1-col → 2-col → N-col at 480px / 768px.  
  Files: all listed above  
  Verify: Each admin page opens without horizontal scroll on 375px; no DataTables column-count warning when table is empty.

---

## Phase 5 — Booking / Session / Clinic

- [ ] 17. **Booking show (`pages/bookings/show.blade.php`) — hero and two-column layout**  
  Issues found:
  - Hero section: `display:flex;align-items:flex-start;justify-content:space-between;gap:24px;flex-wrap:wrap` — the session-ID box on the right can collide with the title text on mobile. The `flex-wrap:wrap` already handles this, but add `min-width:0` to the title div so it shrinks correctly.
  - Main layout `.session-grid` currently uses `grid-template-columns:1fr` (the file declares a single-column grid). The two-column layout at desktop is set via `session-grid` class — confirm the `<style>` block has `@media(min-width:1024px){.session-grid{grid-template-columns:1fr 380px;}}`. If missing, add it.
  - Code box `.code-text` uses `font-size:40px` and `letter-spacing:.12em` — this can overflow on a 320px screen. Add `@media(max-width:400px){.code-text{font-size:28px;letter-spacing:.06em;}}`.
  - Chat bubbles `.chat-mine/.chat-theirs` use `max-width:75%` — fine.
  - `#countdown` font size `32px` is fine on mobile.  
  Files: `resources/views/pages/bookings/show.blade.php`  
  Verify: Booking detail page opens without overflow at 375px; session code readable on 320px.

- [ ] 18. **Group sessions index (`pages/group-sessions/index.blade.php`) — hero and card grid**  
  Issues found:
  - Hero `.hero-g` already has `grid-template-columns:1fr` at base and `1fr 440px` at 1024px via the inline `<style>` block. Fine.
  - Session card listing grid (below the filter): needs inspection. Add responsive class `gs-grid` to the sessions container: `1fr` at mobile, `repeat(2,1fr)` at 640px, `repeat(3,1fr)` at 1024px.
  - Filter/search row uses `flex-wrap:wrap` — fine.
  - The `#group-sessions-table` in the **admin** page is handled in Phase 4. This is the public-facing page which uses cards, not a DataTable.  
  Files: `resources/views/pages/group-sessions/index.blade.php`  
  Verify: Group sessions listing shows 1 card per row on mobile.

- [ ] 19. **Examiner clinic (`pages/examiner-clinic.blade.php`) and clinic details (`pages/examiner-clinic-details.blade.php`)**  
  Issues found in `examiner-clinic.blade.php`:
  - `.hero-g` already responsive.
  - VS comparison grid `.vs-g` uses `grid-template-columns:1fr 1fr` inline with no responsive override. Add `<style>` at bottom: `@media(max-width:639px){.vs-g{grid-template-columns:1fr;}}`.
  - Upcoming clinic cards grid: add responsive class `ec-cards-grid`: `1fr` → `repeat(2,1fr)` at 640px → `repeat(3,1fr)` at 1024px.

  Issues found in `examiner-clinic-details.blade.php`:
  - `#clinic-detail-bookings-table` DataTables — confirm `@empty` has colspan row matching thead column count.
  - Any stat grids with `repeat(3,1fr)` or `repeat(4,1fr)` inline need responsive wrapping.  
  Files: `resources/views/pages/examiner-clinic.blade.php`, `resources/views/pages/examiner-clinic-details.blade.php`  
  Verify: Examiner clinic page and detail page open without overflow at 375px; VS grid stacks on mobile.

---

## Phase 6 — Library & Other

- [ ] 20. **Library (`pages/library/index.blade.php`) — sidebar filter and card grid**  
  Issues found:
  - The main `.layout` grid uses `grid-template-columns:260px 1fr` with a `@media(max-width:768px){.layout{grid-template-columns:1fr;}}` already in the file's `<style>` block — responsive is partially handled.
  - On mobile (768px and below) the sidebar (`<aside class="sidebar">`) stacks above the card grid, which is acceptable but the sidebar has `position:sticky;top:20px` — this should be disabled on mobile to not take up screen real-estate when scrolled. Add `@media(max-width:768px){.sidebar{position:static;}}`.
  - The hero search bar `.hero-search` uses `display:flex;max-width:480px;margin:0 auto` — on 320px the input+button can overflow. Add `@media(max-width:480px){.hero-search{flex-direction:column;}.hero-search input{border-radius:12px;}.hero-search button{border-radius:12px;height:46px;}}`.
  - Card grid `.grid` uses `repeat(auto-fill,minmax(280px,1fr))` — already responsive. Fine.  
  Files: `resources/views/pages/library/index.blade.php`  
  Verify: Library page shows sidebar above cards on mobile (stacked), no horizontal overflow at 375px.

- [ ] 21. **Datatables assets partial (`partials/datatables-assets.blade.php`) — mobile search input width**  
  The existing `@media(max-width:768px)` block already handles most DataTables mobile styling. One missing rule: `table.dataTable` itself needs `width:100%!important` to prevent inline-width from overriding. Add to the existing `@media(max-width:768px)` block: `table.dataTable { width: 100% !important; }`. No other changes needed — the Responsive extension handles column hiding.  
  Files: `resources/views/partials/datatables-assets.blade.php`  
  Verify: DataTables on mobile don't have a horizontal scrollbar wider than the viewport.

---

## Summary of DataTables Empty-State Fixes (TN/18 Bug)

The "Incorrect column count" error occurs when DataTables initializes against a `<tbody>` with zero `<tr>` elements — it cannot infer column count. The fix is identical for every affected table: add an `@empty` row with `colspan` equal to the exact number of `<th>` cells in that table's `<thead>`.

| Table ID | File | `<th>` count | Fix |
|---|---|---|---|
| `#payouts-table` | `admin/payouts.blade.php` | 6 | Add `colspan="6"` empty row |
| `#blog-table` | `admin/blog.blade.php` | 6 | Add `colspan="6"` empty row |
| `#group-sessions-table` | `admin/group-sessions/index.blade.php` | 7 | Add `colspan="7"` empty row |
| `#student-sessions-table` | `dashboard/student.blade.php` | 8 | Add `colspan="8"` empty row |
| `#student-payments-table` | `dashboard/student.blade.php` | verify | Add matching colspan |
| `#parent-upcoming-sessions-table` | `dashboard/parent.blade.php` | 9 | Add `colspan="9"` empty row |
| `#parent-payments-table` | `dashboard/parent.blade.php` | verify | Add matching colspan |
| `#parent-refunds-table` | `dashboard/parent.blade.php` | verify | Add matching colspan |
| `#bookings-table` | `admin/bookings.blade.php` | 8 | Add `colspan="8"` empty row |

---

## Countdown Timer Bug (Sessions showing wrong time)

**Root cause:** The `#countdown` JavaScript in `pages/bookings/show.blade.php` computes remaining time from the PHP-emitted `$sessionStart` Carbon timestamp. If the server timezone and client timezone differ, the countdown will be wrong. The fix is to pass the session time as a UTC ISO string and compute the diff client-side against `Date.now()`.

In `bookings/show.blade.php`, find the countdown `<script>` block and change:
```js
// BEFORE (using server-computed minutesUntil — timezone-fragile)
let secondsLeft = {{ $minutesUntil * 60 }};

// AFTER (client-side diff against UTC ISO string)
const sessionStart = new Date('{{ $sessionStart->toIso8601String() }}');
let secondsLeft = Math.floor((sessionStart - Date.now()) / 1000);
```
Keep the rest of the countdown interval logic unchanged. This is a pure JS change inside the existing `<script>` block — no PHP logic or element IDs change.

Files: `resources/views/pages/bookings/show.blade.php`  
Verify: Book a session for 30 minutes from now. The countdown on the booking show page should read approximately 30:00 and count down correctly regardless of browser timezone.

---

## Verification Commands

This is a plain Laravel + Blade project. There is no compiled asset pipeline (Tailwind CDN is used). Verification is manual browser testing:

1. Open Chrome DevTools → Toggle device toolbar → set to 375×812 (iPhone SE).
2. Visit each page listed in the plan and confirm no horizontal scrollbar appears, no content is clipped, all interactive elements (sidebar, hamburger, DataTables, dropdowns) work.
3. Set to 768px wide (iPad) and confirm grid transitions are correct.
4. Set to 1440px wide and confirm the desktop layout is unchanged from before.

For the DataTables TN/18 fixes specifically: ensure the relevant admin user has 0 records (e.g. delete all payouts in a local dev DB) and confirm no browser console error containing "Incorrect column count" appears.

For the countdown bug: create a test booking scheduled 30 minutes in the future. Open the booking show page and confirm the countdown reads the correct time remaining.
