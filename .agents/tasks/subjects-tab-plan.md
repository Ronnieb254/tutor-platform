# Implementation Plan: Integrate My Subjects Page into Tutor Dashboard

## Overview
Convert the standalone "My Subjects" page (at `tutor-subjects/index.blade.php`) into an internal tab within the tutor dashboard (`dashboard/tutor.blade.php`). The subjects tab will use the same content and functionality, but will be accessed via dashboard tab switching rather than a separate route.

## Design Decisions

1. **Data Passing in Controller**: The `DashboardController::tutorDashboard()` currently does not pass `$categories` to the view. We will add `$categories` to the view data in the controller. The `$profile` variable is already available in the dashboard view, so we can use it instead of `$tutorProfile`.

2. **Tab Integration**: The sidebar already has a "My Subjects" button that does `@click="tab='subjects';sidebarOpen=false"`, but the tab content is missing. We will add the subjects tab section in the same location as other tabs (overview, documents, bookings, etc.).

3. **JavaScript Namespace Conflict Prevention**: The subjects page uses global functions (`toggleSubject`, `deselectSubject`, `updatePreview`, `updateCount`, `updateMaxReachedState`) that must be namespaced to avoid conflicts with other dashboard tabs. All functions will be prefixed with `subj_` (e.g., `subj_toggleSubject`).

4. **Page Title Mapping**: The topbar displays the current tab name via `x-text` mapping. We will add `subjects:'My Subjects'` to the existing page title mapping.

5. **Sidebar Active State**: The "My Subjects" button in the sidebar needs `:class="tab==='subjects'?'active':''"` binding to highlight when active (similar to other tab buttons).

6. **Form Action**: The subjects form action will remain `route('tutor.subjects.store')` for consistency, though it will now submit from within the dashboard context.

7. **Save Button Style**: The FAB (floating action button) from the standalone page will be converted to an inline button at the bottom of the tab section to avoid overlapping other tabs.

8. **Onboarding Card Link**: The onboarding "Add Subjects" card (if present in the dashboard) should link to the tab via `@click="tab='subjects'"` instead of a full page route.

9. **CSS Styling**: CSS classes from the subjects page (`.subject-card`, `.subject-chip`, `.search-wrap`, `.search-input`, `.save-fab`) will be added to the dashboard stylesheet or scoped within the tab section to prevent conflicts.

## Implementation Steps

### Step 1: Update DashboardController to pass categories
**Files**: `app/Http/Controllers/DashboardController.php`
**Changes**: 
- Import the `Category` model at the top of the file
- In the `tutorDashboard()` method, add `$categories = Category::with('subjects')->orderBy('name')->get();` before the view return statement
- Add `'categories'` to the compact list passed to the view
**Verify**: Run `php artisan tinker` and call `app('App\Http\Controllers\DashboardController')->tutorDashboard()` to confirm no errors; check view receives the data

### Step 2: Add 'subjects' to the page title x-text mapping
**Files**: `resources/views/pages/dashboard/tutor.blade.php`
**Changes**:
- Find the `<div class="page-title" x-text="{...}[tab]` element (around line 420 in the topbar section)
- Add `subjects:'My Subjects',` to the object inside the x-text binding
**Verify**: Open the dashboard in a browser, click "My Subjects" in sidebar, and confirm the topbar page title changes to "My Subjects"

### Step 3: Add :class binding to the sidebar "My Subjects" button
**Files**: `resources/views/pages/dashboard/tutor.blade.php`
**Changes**:
- Find the sidebar button for "My Subjects" (currently has `@click="tab='subjects';sidebarOpen=false"`)
- Add `:class="tab==='subjects'?'active':''"` attribute to the button to make it highlight when active
**Verify**: Click "My Subjects" in sidebar and confirm the button highlights with `.sb-item.active` styling

### Step 4: Add CSS classes to dashboard stylesheet
**Files**: `resources/views/pages/dashboard/tutor.blade.php`
**Changes**:
- Add to the `<style>` section (before closing `</style>`) the following CSS classes from the subjects page:
  - `.subject-card`
  - `.subject-card:hover`
  - `.subject-card.selected`
  - `.subject-card input[type=checkbox]`
  - `.subject-chip`
  - `.subject-chip i`
  - `.subject-chip i:hover`
  - `.search-wrap`
  - `.search-wrap i`
  - `.search-input`
  - `.search-input:focus`
  - (Optional: `.save-fab` styles, but will convert to inline button)
**Verify**: View page source and confirm CSS classes are present in `<style>` block

### Step 5: Add subjects tab content section to dashboard
**Files**: `resources/views/pages/dashboard/tutor.blade.php`
**Changes**:
- Find the end of the last tab section (e.g., after `</div>` that closes the profile tab content)
- Add a new tab section:
  ```blade
  {{-- ----------------------- SUBJECTS TAB ----------------------- --}}
  <div x-show="tab==='subjects'" x-cloak>
    {{-- Extract and adapt content from tutor-subjects/index.blade.php --}}
    {{-- Selected subjects preview card --}}
    {{-- Search input --}}
    {{-- Subject categories form --}}
    {{-- Save Changes button (inline, not FAB) --}}
  </div>
  ```
- The content will be identical to `resources/views/pages/tutor-subjects/index.blade.php` except:
  - Remove the "Back to Dashboard" button (no longer needed)
  - Remove the outer HTML structure (layout, head, body tags) — extract only the inner form content
  - Change all JavaScript function references from `toggleSubject()`, `deselectSubject()`, `updatePreview()`, `updateCount()`, `updateMaxReachedState()` to their namespaced versions: `subj_toggleSubject()`, `subj_deselectSubject()`, `subj_updatePreview()`, `subj_updateCount()`, `subj_updateMaxReachedState()`
  - Use `$profile` instead of `$tutorProfile` (both refer to the same object in context)
  - Convert the FAB button from `class="save-fab"` to `class="btn btn-mint"` (inline button at bottom of form)
  - Change the button `type="submit"` to remain the same, just update the styling
**Verify**: Run `php artisan blade:check` to confirm Blade template syntax is valid

### Step 6: Add namespaced JavaScript functions for subjects tab
**Files**: `resources/views/pages/dashboard/tutor.blade.php`
**Changes**:
- At the end of the `<div class="content">` section (before closing tags), add a `<script>` block with all the subjects functionality
- Copy all JavaScript from `resources/views/pages/tutor-subjects/index.blade.php` (from `<script>` to `</script>`)
- Rename all functions to use `subj_` prefix:
  - `toggleSubject` → `subj_toggleSubject`
  - `deselectSubject` → `subj_deselectSubject`
  - `updatePreview` → `subj_updatePreview`
  - `updateCount` → `subj_updateCount`
  - `updateMaxReachedState` → `subj_updateMaxReachedState`
- Update all function calls within the script to use the new namespaced names
- Ensure the `selectedSubjects` Set, `subjectNames` object, and event listeners are scoped within this script block
**Verify**: Open dashboard, click "My Subjects" tab, select/deselect subjects, and confirm:
  - Selected subjects preview updates
  - Subject count updates
  - Max 2 subjects limit is enforced
  - Cards become disabled when max is reached
  - Search filters subjects correctly
  - Save button submits the form

### Step 7: Update form action and validation
**Files**: `resources/views/pages/dashboard/tutor.blade.php` (subjects tab)
**Changes**:
- Ensure the form `method="POST"` and `action="{{ route('tutor.subjects.store') }}"` remain unchanged
- The form must have `id="subjects-form"` for the JavaScript event listener
- Ensure `@csrf` token is included in the form
**Verify**: Submit the form with selected subjects and confirm the redirect includes success message

### Step 8: (Optional) Update onboarding card if it links to subjects route
**Files**: `resources/views/pages/dashboard/tutor.blade.php`
**Changes**:
- If there is an onboarding "Add Subjects" card with an `href="{{ route('tutor.subjects') }}"` link, change it to `@click="tab='subjects'"` 
- This ensures the onboarding card switches to the subjects tab instead of navigating away
**Verify**: Confirm onboarding card (if visible) switches to subjects tab when clicked

## Verification Summary

Run the following to verify the full implementation:

1. **Blade template syntax**: `php artisan blade:check`
2. **No PHP errors**: Access the tutor dashboard in browser and check browser console for JS errors
3. **Tab navigation**: Click "My Subjects" button in sidebar and confirm tab switches and title updates
4. **Form functionality**: Select subjects, save, and confirm the page reflects changes (check database or re-render)
5. **Search functionality**: Type in the search box and confirm subject cards filter
6. **Max subjects limit**: Select 2 subjects and confirm remaining cards become disabled
7. **Responsive layout**: Check mobile view and confirm tab content displays correctly

## Notes

- The standalone route `tutor.subjects` will remain available for backward compatibility but will not be used by tutors in normal workflow
- The sidebar "My Subjects" button already had the `@click="tab='subjects'"` logic in place but lacked the active state styling and tab content
- No database changes are required; all data relationships already exist (tutor → subjects through pivot table)
- The form submission and backend validation remain unchanged in `TutorSubjectController::store()`
