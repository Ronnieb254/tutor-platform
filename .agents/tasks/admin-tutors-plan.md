# Admin Tutors Page Enhancement — Implementation Plan

## Overview
Enhance the current admin tutors card-grid view to show ALL tutors (pending, approved, suspended) in a single unified card layout. Keep the existing card design intact and add:
1. Email column (clickable mailto: links) to each card
2. Status badge display (Pending/Approved/Suspended) with color coding
3. Server-side status filter (`?status=all|pending|approved|suspended`) 
4. Client-side live search (name, email, tutor ID)
5. Search/filter UI controls above card grid
6. Responsive card grid that adapts to mobile screens

## Design Decisions

### 1. Layout: Keep Card Grid
**Decision:** Maintain existing card grid layout (not convert to table).
**Rationale:** User prefers the card design. Cards already show useful information. Conversion to table unnecessary. Add email + status filter controls above cards.

### 2. Backend Approach: Keep Query in View (Small Change)
**Decision:** Keep the @php query in the view rather than moving to controller.
**Rationale:** The existing view already contains inline @php queries. The new query is straightforward (fetch all tutors with filtering params) and stays consistent with the current pattern. No need to refactor the entire flow for a small query.

### 3. Status Filtering: URL Query Parameters (Server-Side)
**Decision:** Filter on server-side using URL params: `?status=all|pending|approved|suspended`.
**Rationale:** Cleaner bookmarking, cleaner browser history, works without JavaScript. All tutors are likely < 5000 so server filtering is fast. Keeps pagination server-side.

### 4. Search Implementation: Client-Side Live Filter
**Decision:** Add client-side JavaScript search/filter (name, email, tutor ID) on the card rows.
**Rationale:** < 500 tutors makes client-side filtering snappy. Avoids server round-trips for every keystroke. Cards remain responsive. Combine with server-side status filter.

### 5. Pagination: 25 Per Page
**Decision:** Use Laravel's `->paginate(25)` as specified.
**Rationale:** Good balance between page load and scans. Existing pattern in the codebase.

### 6. Bulk Email Handling
**Decision:** Keep existing "Email All Tutors" route (sends to all), add checkboxes on card corners; when >= 1 checked: "Email Selected (N)" button appears in the top action bar.
**Rationale:** Minimal breakage of existing UX. Backward compatible. Gradual migration path.

### 7. Responsive Design
**Decision:** Cards stack responsively using existing `grid-template-columns: repeat(auto-fill, minmax(400px, 1fr))`. On mobile (< 640px), adjust to 1-column layout.
**Rationale:** Already built into the existing card grid system. No new breakpoints needed beyond what's already there.

### 8. CSS Strategy
**Decision:** Use existing CSS variables (--ink, --muted, --hairline, badge-amber/green/red/blue) already defined in admin.blade.php. Add minimal new styles in a `<style>` block in the view for card-specific checkbox and search UI.
**Rationale:** Matches existing design system. No new dependencies. Keeps styling close to markup for maintainability.

## Files to Change

### Primary Change
- **resources/views/pages/admin/tutors.blade.php** — Update view logic while keeping card grid:
  - Replace the toggle view button with a status filter dropdown (URL param based: `?status=all|pending|approved|suspended`)
  - Add search bar above card grid (client-side live filter)
  - Update inline @php to fetch ALL tutors (not just pending or approved), filter by status param
  - Add email column + email link to each card (mailto:)
  - Add status badge to each card (Pending=amber, Approved=green, Suspended=red)
  - Add checkbox to top-right corner of each card for bulk selection
  - Update "Email All Tutors" button to toggle to "Email Selected (N)" when checkboxes are checked
  - Keep all existing card content, document review modal, single email modal, reject modal
  - Keep all card action buttons (View, Email, Approve/Suspend)

### Secondary Change (Minor)
- **app/Http/Controllers/Admin/TutorApprovalController.php** — No change needed. Routes already exist.
  - Existing routes `admin.tutors.approve`, `admin.tutors.suspend`, `admin.documents.approve`, `admin.documents.reject` stay the same.

- **routes/web.php** — Likely no change needed. Routes already mapped:
  - `admin.tutors` → points to view `pages.admin.tutors`
  - All action routes already exist

## Query Strategy (In View)

```php
@php
use App\Models\TutorProfile;
use App\Models\User;

$status = request('status', 'all'); // all|pending|approved|suspended
$search = request('search', ''); // Client-side search will filter additionally

$query = TutorProfile::with(['user', 'subjects', 'documents']);

// Status filter
if ($status === 'pending') {
    $query->where('is_approved', false);
} elseif ($status === 'approved') {
    $query->where('is_approved', true);
}
// 'all' shows everything (no additional filter)

// Order by created_at descending (most recent first)
$query->orderBy('created_at', 'desc');

$tutors = $query->paginate(25);

// Counts for stats
$totalTutors = TutorProfile::count();
$approvedCount = TutorProfile::where('is_approved', true)->count();
$pendingCount = TutorProfile::where('is_approved', false)->count();
@endphp
```

## Card Changes

### Email Addition
- Add email row to card header (below name)
- Display as clickable mailto: link
- Example: `<i class="fa-solid fa-envelope"></i> tutor@example.com`
- Styled like the existing phone row in the card

### Status Badge Addition
- Display status badge in card header (top-right, near avatar)
- Pending: `badge-amber`
- Approved: `badge-green`
- Note: Suspended tutors are those with `is_approved=false` (same visual treatment as pending, but distinct in logic)

### Checkbox Addition
- Small checkbox in top-right corner of each card
- Toggled by JavaScript
- When checked: `opacity: 1`, when unchecked: subtle styling
- Triggers "Email Selected (N)" button appearance

## UI Controls Above Card Grid

### 1. Search Bar
- Input field with magnifying glass icon
- Placeholder: "Search by name, email, or tutor ID…"
- Client-side real-time filter against card rows
- Clear button (X) when text entered
- Non-URL-based (local filtering only)

### 2. Status Filter Dropdown
- Button/select showing current filter (e.g., "All", "Pending", "Approved", "Suspended")
- On click: opens dropdown menu
- Options: All (shows all), Pending (is_approved=false), Approved (is_approved=true)
- Clicking an option sets URL param `?status=X` and refreshes page

### 3. Email Buttons Container
- "Email All Tutors" button (always shown)
- When >= 1 checkbox checked: "Email Selected (N)" button also shown
- Both buttons open the existing email modal
- "Email All" sends to all (existing behavior)
- "Email Selected" sends only to selected profile IDs

## Card Content Structure

**Existing card structure (keep as is):**
- Header: Avatar, Name, Subjects count stat, Documents stat, Rejected stat
- Status row: Status badge (NEW), Applied date

**NEW additions:**
- Email row: `<icon> <email@domain.com>` (clickable mailto:)
- Checkbox: Top-right corner of card

**Actions (keep as is):**
- Subjects button
- Review button (modal)
- Email button (modal)
- Approve button (if conditions met) or Suspend button

## Verification Steps

1. **View renders without errors**
   - Navigate to `/admin/tutors` — page loads
   - No console errors (F12)
   - Verify cards show all tutors (pending + approved combined)

2. **Status filter works**
   - Use status dropdown, select "Approved" — URL changes to `?status=approved`
   - Cards show only approved tutors
   - Repeat for "Pending", "All"
   - Verify stats in header update

3. **Search filter works**
   - Type a tutor's name in search box
   - Card rows filter in real-time (client-side)
   - Clear search, verify all cards return

4. **Email column visible**
   - Each card shows email with mailto: link
   - Click email — opens default email client

5. **Status badge visible**
   - Approved tutors show green badge
   - Pending tutors show amber badge
   - Badge text: "Approved" or "Pending"

6. **Bulk email selection works**
   - Check 2 tutor card checkboxes
   - "Email Selected (2)" button appears in top bar
   - Click button, email modal opens
   - Uncheck all — button disappears, "Email All Tutors" remains

7. **Existing modals intact**
   - Click "View" on any card — review modal opens with documents, subjects, actions
   - Verify Approve/Suspend buttons work
   - Click document thumbnail — document viewer modal opens
   - Verify Email button in modal works
   - Verify Reject Document modal works

8. **Responsiveness**
   - Desktop (> 1024px): cards show 2-3 per row
   - Tablet (768-1024px): cards show 1-2 per row
   - Mobile (< 640px): cards show 1 per row, full width
   - All cards remain clickable and functional

9. **Pagination**
   - 26+ tutors show pagination at bottom
   - Click next page — loads next 25 tutors
   - Verify status filter + search persist across pages

## Implementation Notes

- **Preserve card layout:** Keep all existing card styling, grid layout, and responsive behavior. Only add email row, status badge, and checkbox.
- **Preserve modals:** The three existing modals (document review, single email, reject doc) and the email-all modal remain unchanged. CSS IDs and JavaScript functions stay the same.
- **No new routes:** Use existing routes (`admin.tutors.approve`, `admin.tutors.suspend`, etc.). Email bulk functionality uses existing `/admin/tutors/send-email` endpoint.
- **Database:** No migrations needed. Query uses existing columns (`is_approved`, `created_at`, etc.).
- **Performance:** With 25-per-page pagination, even 1000 tutors loads fast. Client-side search is instant. No new indexes needed.
- **Backward compatible:** Existing buttons and actions remain unchanged. Just adding email, status, and checkbox UI.
