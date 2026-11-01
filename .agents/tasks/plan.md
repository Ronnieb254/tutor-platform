# Implementation Plan: Apply DataTables to All Tables System-Wide

## Executive Summary

This plan covers the integration of DataTables 1.13+ (with Responsive and Buttons extensions) across **all 23+ table elements** identified throughout the Laravel 12 / Livewire 3.6 Blade application. The application uses three main layouts (app, admin, guest) and has tables in admin views, user dashboards, and public pages.

**Key Finding:** DataTables is not currently integrated. The app uses Alpine.js 3.x and SweetAlert2 loaded via CDN, plus Vite for asset compilation (no jQuery currently included).

**Critical Decision:** Since DataTables requires jQuery, we will add jQuery 3.7+ via CDN to avoid Vite rebuild complexity. DataTables will be loaded via CDN (CSS + JS + extensions) in a single shared partial (`resources/views/partials/datatables-assets.blade.php`) that both `layouts/admin.blade.php` and `layouts/app.blade.php` can include.

**Livewire Compatibility:** The app uses Livewire 3.6 primarily for search/filter forms (e.g., `livewire/tutors/tutor-search.blade.php`), not for table rendering. The tables themselves are server-rendered Blade loops with `@foreach`. **Therefore, Livewire/DataTables conflict is not an issue** — we simply apply DataTables to the server-rendered `<table>` elements.

---

## Table Inventory (23 tables identified)

### Admin Layout Tables (resources/views/pages/admin/)

1. **admin/bookings.blade.php** — Bookings management table
   - Columns: Student, Tutor, Subject, Scheduled, Duration, Amount, Status, Actions
   - Rows: ~50–500 (paginated in backend currently)
   - ID to add: `id="bookings-table"`
   - Column types: text, text, text, date, number, currency, badge/html, html (buttons)
   - Server-side: No (client-side OK for <1000 rows)

2. **admin/blog.blade.php** — Blog posts table
   - Columns: Title, Author, Published, Views, Actions
   - Rows: ~20–200
   - ID to add: `id="blog-table"`
   - Column types: text, text, date, number, html
   - Server-side: No

3. **admin/categories.blade.php** — Categories table
   - Columns: Icon, Name, Description, Subjects Count, Actions
   - Rows: ~10–30
   - ID to add: `id="categories-table"`
   - Column types: html, text, text, number, html
   - Server-side: No

4. **admin/clinic-show.blade.php** — Clinic bookings table (inside clinic detail view)
   - Columns: Student, Email, Phone, Booked At, Status, Actions
   - Rows: ~10–50 per clinic
   - ID to add: `id="clinic-bookings-table"`
   - Column types: text, text, text, date, badge/html, html
   - Server-side: No

5. **admin/clinics.blade.php** — Examiner clinics list table
   - Columns: Title, Type, Tutor, Scheduled, Duration, Max Students, Enrolled, Price, Status, Actions
   - Rows: ~20–100
   - ID to add: `id="clinics-table"`
   - Column types: text, text, text, date, number, number, number, currency, badge/html, html
   - Server-side: No

6. **admin/library.blade.php** — Library resources table
   - Columns: Resource (title+icon), Type, Author, Category, Grade, Downloads, Status, Actions
   - Rows: ~30–500
   - ID to add: `id="library-table"`
   - Column types: html, badge/html, text, text, text, number, badge/html, html
   - Server-side: Consider if >500 rows (initially client-side)

7. **admin/payouts.blade.php** — Tutor payout requests table
   - Columns: Tutor, Amount, Bank Details, Requested, Status, Actions
   - Rows: ~20–300
   - ID to add: `id="payouts-table"`
   - Column types: text, currency, text, date, badge/html, html
   - Server-side: No

8. **admin/subjects.blade.php** — Subjects table
   - Columns: Name, Category, Icon, Active, Tutors Count, Actions
   - Rows: ~50–200
   - ID to add: `id="subjects-table"`
   - Column types: text, text, html, badge/html, number, html
   - Server-side: No

9. **admin/settings.blade.php** — Session pricing multipliers table (read-only reference)
   - Columns: Duration, Multiplier, Example
   - Rows: 4 (fixed: 15min, 30min, 45min, 60min)
   - ID to add: `id="pricing-multipliers-table"`
   - Column types: text, text, text
   - Server-side: No
   - Special: read-only info table, no export needed (skip export buttons for this one)

10. **admin/group-sessions/index.blade.php** — Group sessions admin table
    - Columns: Session, Tutor, Scheduled, Duration, Price, Enrolled, Max, Status, Actions
    - Rows: ~20–200
    - ID to add: `id="group-sessions-table"`
    - Column types: text, text, date, number, currency, number, number, badge/html, html
    - Server-side: No

11. **admin/users.blade.php** — All users table
    - Columns: User, Email, Role, Registered, Status, Actions
    - Rows: ~100–2000 (may need server-side if >1000)
    - ID to add: `id="users-table"`
    - Column types: html, text, text, date, badge/html, html
    - Server-side: Yes if >1000 users (check in browser; initially client-side)

12. **admin/group-sessions/show.blade.php** — Session enrollments table (inside session detail)
    - Columns: Student, Email, Enrolled At, Payment Status, Actions
    - Rows: ~5–30 per session
    - ID to add: `id="session-enrollments-table"`
    - Column types: text, text, date, badge/html, html
    - Server-side: No

### Parent/Student Dashboard Tables (resources/views/pages/dashboard/)

13. **dashboard/parent.blade.php** — THREE tables:
    - **Upcoming sessions table**
      - Columns: Tutor, Child, Subject, Date, Duration, Paid, Status, Review, Actions
      - ID: `id="parent-upcoming-sessions-table"`
      - Column types: text, text, text, date, number, currency, badge/html, html, html
    - **Refund requests table**
      - Columns: Booking, Tutor, Reason, Amount, Status, Date
      - ID: `id="parent-refunds-table"`
      - Column types: text, text, text, currency, badge/html, date
    - **Payment history table**
      - Columns: Tutor, Reference, Date, Session, Amount, Status, Actions
      - ID: `id="parent-payments-table"`
      - Column types: text, text, date, text, currency, badge/html, html
    - Rows: ~10–200 per table
    - Server-side: No

14. **dashboard/student.blade.php** — TWO tables:
    - **Upcoming sessions table**
      - Columns: Tutor, Subject, Date, Duration, Paid, Status, Review, Actions
      - ID: `id="student-sessions-table"`
      - Column types: text, text, date, number, currency, badge/html, html, html
    - **Payment history table**
      - Columns: Tutor, Reference, Date, Session, Amount, Status, Actions
      - ID: `id="student-payments-table"`
      - Column types: text, text, date, text, currency, badge/html, html
    - Rows: ~10–150 per table
    - Server-side: No

15. **dashboard/admin.blade.php** — Recent bookings table (summary)
    - Columns: Student, Tutor, Subject, Date, Amount, Status, Actions
    - Rows: ~20 (paginated backend, but small subset)
    - ID: `id="admin-recent-bookings-table"`
    - Column types: html, text, text, date, currency, badge/html, html
    - Server-side: No

### Tutor-Facing Tables

16. **tutor/library/index.blade.php** — Tutor's uploaded resources table
    - Columns: Resource, Type, Submitted, Downloads, Status, Actions
    - Rows: ~5–100 per tutor
    - ID: `id="tutor-library-table"`
    - Column types: html, badge/html, date, number, badge/html, html
    - Server-side: No

### Examiner Dashboard

17. **examiner-dashboard.blade.php** — Clinic bookings table (tutor/examiner view)
    - Columns: Student, Email, Phone, Session, Booked At, Status
    - Rows: ~10–50
    - ID: `id="examiner-bookings-table"`
    - Column types: text, text, text, text, date, badge/html
    - Server-side: No

18. **examiner-clinic-details.blade.php** — Clinic session bookings table (public detail view)
    - Columns: Student, Email, Phone, Booked At, Status
    - Rows: ~10–30
    - ID: `id="clinic-detail-bookings-table"`
    - Column types: text, text, text, date, badge/html
    - Server-side: No

### Public/Marketing Pages

19. **how-it-works.blade.php** — Pricing comparison table (informational, no data)
    - Columns: Feature, Individual, Group Session
    - Rows: ~8 (fixed content)
    - ID: `id="pricing-comparison-table"`
    - Column types: text, html, html
    - Server-side: No
    - Special: Informational table; export not critical but include for consistency

20. **legal/cookie-policy.blade.php** — Cookie types table (fixed content)
    - Columns: Cookie Name, Purpose, Expiry
    - Rows: ~6 (fixed)
    - ID: `id="cookies-table"`
    - Column types: text, text, text
    - Server-side: No
    - Special: Legal/info table; export optional

### Email Templates (excluded from DataTables)

- **emails/booking-confirmed-tutor.blade.php** and **emails/booking-confirmed-student.blade.php** contain `<table>` tags for email layout (HTML email structure), NOT data tables. **Do not apply DataTables to email templates.**

---

## Integration Approach

### 1. Shared Infrastructure

**File:** `resources/views/partials/datatables-assets.blade.php`

This partial will include:
- jQuery 3.7.1 (slim build, from CDN)
- DataTables 1.13.11 CSS + JS (from CDN)
- DataTables Responsive extension CSS + JS
- DataTables Buttons extension CSS + JS
- JSZip (required for Excel export)
- pdfmake + vfs_fonts (required for PDF export)
- HTML5 export buttons
- A single shared `initDataTable(selector, options)` function that:
  - Merges default config (responsive, buttons, page length options, language)
  - Wraps init in try/catch to prevent one table's failure from breaking others
  - Logs errors to console

**File:** `resources/views/partials/datatables-init.blade.php`

A Blade partial that can be `@include`d at the bottom of any view with tables. It will accept a parameter like `@include('partials.datatables-init', ['tables' => ['#bookings-table' => ['order' => [[3, 'desc']]], ...]])` and call `initDataTable()` for each.

### 2. Layout Integration

**Admin Layout (`resources/views/layouts/admin.blade.php`)**
- Add `@include('partials.datatables-assets')` in the `<head>` section after the existing CDN scripts (Alpine, SweetAlert2).

**App Layout (`resources/views/layouts/app.blade.php`)**
- Add `@include('partials.datatables-assets')` in the `<head>` section.

**Guest Layout (`resources/views/layouts/guest.blade.php`)**
- Add `@include('partials.datatables-assets')` in the `<head>` only if needed (currently guest layout is for auth pages, no tables; but the cookie-policy.blade.php might extend guest or a similar layout — check and add if needed).

### 3. Styling Integration

DataTables comes with default themes. We will:
- Use the "default" DataTables theme CSS (loads from CDN).
- Add a small `<style>` block in `datatables-assets.blade.php` to override DataTables' default colors/fonts to match the app's design system:
  - Use `--ink`, `--trust-blue`, `--hairline`, `--muted` CSS variables (already defined in admin.blade.php and app.blade.php).
  - Ensure buttons use the app's `.btn` classes or custom styles matching the brand.
  - Responsive table design: DataTables Responsive extension handles mobile automatically (hides columns, shows + icon to expand).

### 4. Export Button Styling

DataTables Buttons extension provides copy, CSV, Excel, PDF, and print buttons. We will:
- Style them to match the app's existing button design (`.btn`, `.btn-primary`, `.btn-outline`).
- Position them in the top-right of each table's container (DataTables places them in `div.dt-buttons` by default).
- Override button CSS in the shared partial to use:
  - Border-radius: `10px` (app's standard button radius).
  - Colors: `#0A64BC` (trust-blue) for primary, white background with border for outline.
  - Font: `'DM Sans'` to match the app.

---

## Implementation Steps (Ordered by Dependency)

### Step 1: Create Shared DataTables Assets Partial

- [ ] **1.1** Create `resources/views/partials/datatables-assets.blade.php`.
  - Include jQuery 3.7.1 slim (CDN: `https://code.jquery.com/jquery-3.7.1.slim.min.js`).
  - Include DataTables 1.13.11 CSS (CDN: `https://cdn.datatables.net/1.13.11/css/jquery.dataTables.min.css`).
  - Include DataTables 1.13.11 JS (CDN: `https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js`).
  - Include Responsive extension CSS (CDN: `https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css`).
  - Include Responsive extension JS (CDN: `https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js`).
  - Include Buttons extension CSS (CDN: `https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css`).
  - Include Buttons extension JS (CDN: `https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js`).
  - Include JSZip (CDN: `https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js`).
  - Include pdfmake (CDN: `https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js`).
  - Include pdfmake fonts (CDN: `https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js`).
  - Include Buttons HTML5 export (CDN: `https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js`).
  - Include Buttons print (CDN: `https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js`).
  - Add `<style>` block to override DataTables styles:
    - Table borders: use `var(--hairline)`.
    - Header background: `rgba(21,40,31,.03)`.
    - Row hover: `#FAFCFA`.
    - Pagination buttons: match `.btn` style.
    - Search input: match `.form-input` style from admin layout (height 40px, border-radius 10px, border 1.5px solid var(--hairline)).
    - Button colors: trust-blue for primary actions.
  - Add `<script>` block with `initDataTable(selector, customOptions)` function:
    ```javascript
    function initDataTable(selector, customOptions = {}) {
      try {
        const defaultOptions = {
          responsive: true,
          pageLength: 25,
          lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
          dom: '<"dt-top"<"dt-length"l><"dt-buttons"B><"dt-search"f>>rt<"dt-bottom"<"dt-info"i><"dt-pagination"p>>',
          buttons: [
            { extend: 'copy', text: '<i class="fa-solid fa-copy fa-xs"></i> Copy', className: 'btn-dt' },
            { extend: 'csv', text: '<i class="fa-solid fa-file-csv fa-xs"></i> CSV', className: 'btn-dt' },
            { extend: 'excel', text: '<i class="fa-solid fa-file-excel fa-xs"></i> Excel', className: 'btn-dt' },
            { extend: 'pdf', text: '<i class="fa-solid fa-file-pdf fa-xs"></i> PDF', className: 'btn-dt' },
            { extend: 'print', text: '<i class="fa-solid fa-print fa-xs"></i> Print', className: 'btn-dt' }
          ],
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries found",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
              first: "First",
              last: "Last",
              next: "Next",
              previous: "Prev"
            }
          }
        };
        const options = { ...defaultOptions, ...customOptions };
        $(selector).DataTable(options);
      } catch (error) {
        console.error('DataTables init failed for', selector, error);
      }
    }
    ```
  - **Files:** `resources/views/partials/datatables-assets.blade.php`
  - **Verify:** Open the file in an editor and confirm all CDN URLs are present, and the `initDataTable` function is defined.

---

### Step 2: Add DataTables Assets to Layouts

- [ ] **2.1** Modify `resources/views/layouts/admin.blade.php`:
  - Locate the `<head>` section (around line 13, after SweetAlert2 script).
  - Add `@include('partials.datatables-assets')` immediately after the SweetAlert2 line.
  - **Files:** `resources/views/layouts/admin.blade.php`
  - **Verify:** Open admin.blade.php and confirm the `@include` directive is present in `<head>`.

- [ ] **2.2** Modify `resources/views/layouts/app.blade.php`:
  - Locate the `<head>` section (around line 10–12, after Font Awesome and SweetAlert2).
  - Add `@include('partials.datatables-assets')` after SweetAlert2.
  - **Files:** `resources/views/layouts/app.blade.php`
  - **Verify:** Open app.blade.php and confirm the `@include` directive is present in `<head>`.

- [ ] **2.3** Check if `resources/views/pages/legal/cookie-policy.blade.php` or `resources/views/pages/how-it-works.blade.php` extend `layouts.guest`:
  - Read the first few lines of both files to see `@extends('layouts.guest')` or similar.
  - If yes, modify `resources/views/layouts/guest.blade.php` to add `@include('partials.datatables-assets')` in `<head>`.
  - If no (they extend a different layout or are standalone), handle them in their specific view (see step 6).
  - **Files:** `resources/views/layouts/guest.blade.php` (conditional)
  - **Verify:** Confirm by reading the layout directive in cookie-policy.blade.php and how-it-works.blade.php.

---

### Step 3: Admin Tables — Add IDs and Initialize DataTables

Apply DataTables to all admin tables in priority order: bookings → users → group-sessions → payouts → library → blog → clinics → subjects → categories → settings.

- [ ] **3.1** `resources/views/pages/admin/bookings.blade.php`:
  - Locate `<table>` (line ~70).
  - Add `id="bookings-table"` to the `<table>` tag.
  - At the bottom of the file, before `@endsection`, add:
    ```blade
    @push('scripts')
    <script>
    $(document).ready(function() {
      initDataTable('#bookings-table', {
        order: [[3, 'desc']], // Scheduled column descending
        columnDefs: [
          { type: 'date', targets: 3 }, // Scheduled
          { type: 'num', targets: 4 },  // Duration
          { type: 'num', targets: 5 },  // Amount
          { orderable: false, targets: 7 } // Actions
        ]
      });
    });
    </script>
    @endpush
    ```
  - **Files:** `resources/views/pages/admin/bookings.blade.php`
  - **Verify:** Run `php artisan view:cache` (or clear: `php artisan view:clear`), then load `/admin/bookings` in a browser. Confirm DataTables controls (search, pagination, export buttons) appear, sorting/search work, and console has no JS errors.

- [ ] **3.2** `resources/views/pages/admin/users.blade.php`:
  - Locate `<table>` (line ~61).
  - Add `id="users-table"`.
  - Add initialization script in `@push('scripts')`:
    ```javascript
    initDataTable('#users-table', {
      order: [[3, 'desc']], // Registered column descending
      columnDefs: [
        { type: 'date', targets: 3 }, // Registered
        { orderable: false, targets: 5 } // Actions
      ]
    });
    ```
  - **Files:** `resources/views/pages/admin/users.blade.php`
  - **Verify:** Load `/admin/users` in browser, confirm DataTables active, no JS errors.

- [ ] **3.3** `resources/views/pages/admin/group-sessions/index.blade.php`:
  - Locate `<table>` (line ~72).
  - Add `id="group-sessions-table"`.
  - Initialize:
    ```javascript
    initDataTable('#group-sessions-table', {
      order: [[2, 'asc']], // Scheduled ascending
      columnDefs: [
        { type: 'date', targets: 2 },
        { type: 'num', targets: [3, 4, 5, 6] }, // Duration, Price, Enrolled, Max
        { orderable: false, targets: 8 } // Actions
      ]
    });
    ```
  - **Files:** `resources/views/pages/admin/group-sessions/index.blade.php`
  - **Verify:** Load `/admin/group-sessions`, confirm DataTables.

- [ ] **3.4** `resources/views/pages/admin/payouts.blade.php`:
  - Locate `<table>` (line ~63).
  - Add `id="payouts-table"`.
  - Initialize:
    ```javascript
    initDataTable('#payouts-table', {
      order: [[3, 'desc']], // Requested date descending
      columnDefs: [
        { type: 'num', targets: 1 }, // Amount
        { type: 'date', targets: 3 }, // Requested
        { orderable: false, targets: 5 } // Actions
      ]
    });
    ```
  - **Files:** `resources/views/pages/admin/payouts.blade.php`
  - **Verify:** Load `/admin/payouts`, confirm DataTables.

- [ ] **3.5** `resources/views/pages/admin/library.blade.php`:
  - Locate `<table>` (line ~87).
  - Add `id="library-table"`.
  - Initialize:
    ```javascript
    initDataTable('#library-table', {
      order: [[5, 'desc']], // Downloads descending
      columnDefs: [
        { type: 'num', targets: 5 }, // Downloads
        { orderable: false, targets: 7 } // Actions
      ]
    });
    ```
  - **Files:** `resources/views/pages/admin/library.blade.php`
  - **Verify:** Load `/admin/library`, confirm DataTables.

- [ ] **3.6** `resources/views/pages/admin/blog.blade.php`:
  - Locate `<table>` (line ~40).
  - Add `id="blog-table"`.
  - Initialize:
    ```javascript
    initDataTable('#blog-table', {
      order: [[2, 'desc']], // Published descending
      columnDefs: [
        { type: 'date', targets: 2 },
        { type: 'num', targets: 3 }, // Views
        { orderable: false, targets: 4 } // Actions
      ]
    });
    ```
  - **Files:** `resources/views/pages/admin/blog.blade.php`
  - **Verify:** Load `/admin/blog`, confirm DataTables.

- [ ] **3.7** `resources/views/pages/admin/clinics.blade.php`:
  - Locate `<table>` (line ~87).
  - Add `id="clinics-table"`.
  - Initialize:
    ```javascript
    initDataTable('#clinics-table', {
      order: [[3, 'asc']], // Scheduled ascending
      columnDefs: [
        { type: 'date', targets: 3 },
        { type: 'num', targets: [4, 5, 6, 7] }, // Duration, Max, Enrolled, Price
        { orderable: false, targets: 9 } // Actions
      ]
    });
    ```
  - **Files:** `resources/views/pages/admin/clinics.blade.php`
  - **Verify:** Load `/admin/clinics`, confirm DataTables.

- [ ] **3.8** `resources/views/pages/admin/subjects.blade.php`:
  - Locate `<table>` (line ~83).
  - Add `id="subjects-table"`.
  - Initialize:
    ```javascript
    initDataTable('#subjects-table', {
      order: [[0, 'asc']], // Name ascending
      columnDefs: [
        { type: 'num', targets: 4 }, // Tutors Count
        { orderable: false, targets: 5 } // Actions
      ]
    });
    ```
  - **Files:** `resources/views/pages/admin/subjects.blade.php`
  - **Verify:** Load `/admin/subjects`, confirm DataTables.

- [ ] **3.9** `resources/views/pages/admin/categories.blade.php`:
  - Locate `<table>` (line ~32).
  - Add `id="categories-table"`.
  - Initialize:
    ```javascript
    initDataTable('#categories-table', {
      order: [[1, 'asc']], // Name ascending
      columnDefs: [
        { type: 'num', targets: 3 }, // Subjects Count
        { orderable: false, targets: 4 } // Actions
      ]
    });
    ```
  - **Files:** `resources/views/pages/admin/categories.blade.php`
  - **Verify:** Load `/admin/categories`, confirm DataTables.

- [ ] **3.10** `resources/views/pages/admin/settings.blade.php`:
  - Locate the pricing multipliers `<table>` (line ~124).
  - Add `id="pricing-multipliers-table"`.
  - Initialize with **no export buttons** (info table only):
    ```javascript
    initDataTable('#pricing-multipliers-table', {
      paging: false,
      searching: false,
      info: false,
      buttons: [] // No export buttons for this info table
    });
    ```
  - **Files:** `resources/views/pages/admin/settings.blade.php`
  - **Verify:** Load `/admin/settings`, confirm table is sortable but no pagination/search/export (clean, minimal).

- [ ] **3.11** `resources/views/pages/admin/clinic-show.blade.php`:
  - Locate `<table>` (line ~82).
  - Add `id="clinic-bookings-table"`.
  - Initialize:
    ```javascript
    initDataTable('#clinic-bookings-table', {
      order: [[3, 'desc']], // Booked At descending
      columnDefs: [
        { type: 'date', targets: 3 },
        { orderable: false, targets: 5 } // Actions
      ]
    });
    ```
  - **Files:** `resources/views/pages/admin/clinic-show.blade.php`
  - **Verify:** Load a clinic detail page, confirm DataTables.

- [ ] **3.12** `resources/views/pages/admin/group-sessions/show.blade.php`:
  - Locate `<table>` (line ~70).
  - Add `id="session-enrollments-table"`.
  - Initialize:
    ```javascript
    initDataTable('#session-enrollments-table', {
      order: [[2, 'desc']], // Enrolled At descending
      columnDefs: [
        { type: 'date', targets: 2 },
        { orderable: false, targets: 4 } // Actions
      ]
    });
    ```
  - **Files:** `resources/views/pages/admin/group-sessions/show.blade.php`
  - **Verify:** Load a group session detail page, confirm DataTables.

---

### Step 4: User Dashboard Tables — Parent, Student, Admin Dashboards

- [ ] **4.1** `resources/views/pages/dashboard/parent.blade.php` — THREE tables:
  - **Table 1:** Upcoming sessions table (line ~303).
    - Add `id="parent-upcoming-sessions-table"`.
    - At bottom of view, add:
      ```javascript
      initDataTable('#parent-upcoming-sessions-table', {
        order: [[3, 'asc']], // Date ascending
        columnDefs: [
          { type: 'date', targets: 3 },
          { type: 'num', targets: 4 }, // Duration
          { orderable: false, targets: [7, 8] } // Review, Actions
        ]
      });
      ```
  - **Table 2:** Refund requests table (line ~433).
    - Add `id="parent-refunds-table"`.
    - Initialize:
      ```javascript
      initDataTable('#parent-refunds-table', {
        order: [[5, 'desc']], // Date descending
        columnDefs: [
          { type: 'num', targets: 3 }, // Amount
          { type: 'date', targets: 5 }
        ]
      });
      ```
  - **Table 3:** Payment history table (line ~482).
    - Add `id="parent-payments-table"`.
    - Initialize:
      ```javascript
      initDataTable('#parent-payments-table', {
        order: [[2, 'desc']], // Date descending
        columnDefs: [
          { type: 'date', targets: 2 },
          { type: 'num', targets: 4 }, // Amount
          { orderable: false, targets: 6 } // Actions
        ]
      });
      ```
  - **Files:** `resources/views/pages/dashboard/parent.blade.php`
  - **Verify:** Load parent dashboard, confirm all 3 tables have DataTables active, no JS errors.

- [ ] **4.2** `resources/views/pages/dashboard/student.blade.php` — TWO tables:
  - **Table 1:** Upcoming sessions (line ~447).
    - Add `id="student-sessions-table"`.
    - Initialize:
      ```javascript
      initDataTable('#student-sessions-table', {
        order: [[2, 'asc']], // Date ascending
        columnDefs: [
          { type: 'date', targets: 2 },
          { type: 'num', targets: 3 }, // Duration
          { orderable: false, targets: [6, 7] } // Review, Actions
        ]
      });
      ```
  - **Table 2:** Payment history (line ~609).
    - Add `id="student-payments-table"`.
    - Initialize:
      ```javascript
      initDataTable('#student-payments-table', {
        order: [[2, 'desc']], // Date descending
        columnDefs: [
          { type: 'date', targets: 2 },
          { type: 'num', targets: 4 }, // Amount
          { orderable: false, targets: 6 } // Actions
        ]
      });
      ```
  - **Files:** `resources/views/pages/dashboard/student.blade.php`
  - **Verify:** Load student dashboard, confirm both tables have DataTables.

- [ ] **4.3** `resources/views/pages/dashboard/admin.blade.php`:
  - Locate `<table>` (line ~113).
  - Add `id="admin-recent-bookings-table"`.
  - Initialize:
    ```javascript
    initDataTable('#admin-recent-bookings-table', {
      order: [[3, 'desc']], // Date descending
      columnDefs: [
        { type: 'date', targets: 3 },
        { type: 'num', targets: 4 }, // Amount
        { orderable: false, targets: 6 } // Actions
      ]
    });
    ```
  - **Files:** `resources/views/pages/dashboard/admin.blade.php`
  - **Verify:** Load admin dashboard, confirm table active.

---

### Step 5: Tutor and Examiner Tables

- [ ] **5.1** `resources/views/pages/tutor/library/index.blade.php`:
  - Locate `<table>` (line ~139).
  - Add `id="tutor-library-table"`.
  - Initialize:
    ```javascript
    initDataTable('#tutor-library-table', {
      order: [[2, 'desc']], // Submitted descending
      columnDefs: [
        { type: 'date', targets: 2 },
        { type: 'num', targets: 3 }, // Downloads
        { orderable: false, targets: 5 } // Actions
      ]
    });
    ```
  - **Files:** `resources/views/pages/tutor/library/index.blade.php`
  - **Verify:** Load tutor library page, confirm DataTables.

- [ ] **5.2** `resources/views/pages/examiner-dashboard.blade.php`:
  - Locate `<table>` (line ~230).
  - Add `id="examiner-bookings-table"`.
  - Initialize:
    ```javascript
    initDataTable('#examiner-bookings-table', {
      order: [[4, 'desc']], // Booked At descending
      columnDefs: [
        { type: 'date', targets: 4 }
      ]
    });
    ```
  - **Files:** `resources/views/pages/examiner-dashboard.blade.php`
  - **Verify:** Load examiner dashboard, confirm table active.

- [ ] **5.3** `resources/views/pages/examiner-clinic-details.blade.php`:
  - Locate `<table>` (line ~90).
  - Add `id="clinic-detail-bookings-table"`.
  - Initialize:
    ```javascript
    initDataTable('#clinic-detail-bookings-table', {
      order: [[3, 'desc']], // Booked At descending
      columnDefs: [
        { type: 'date', targets: 3 }
      ]
    });
    ```
  - **Files:** `resources/views/pages/examiner-clinic-details.blade.php`
  - **Verify:** Load clinic detail page (public), confirm table active.

---

### Step 6: Public/Marketing Tables

- [ ] **6.1** `resources/views/pages/how-it-works.blade.php`:
  - Locate `<table>` (line ~262).
  - Add `id="pricing-comparison-table"`.
  - Check which layout it extends (`@extends('layouts.XXX')`). If it's not `layouts.guest` or `layouts.app`, ensure DataTables assets are loaded (add `@include('partials.datatables-assets')` in the view's `@section('head')` or similar).
  - Initialize (minimal: paging off, export optional):
    ```javascript
    initDataTable('#pricing-comparison-table', {
      paging: false,
      searching: false,
      info: false,
      order: [[0, 'asc']] // Feature column ascending
    });
    ```
  - **Files:** `resources/views/pages/how-it-works.blade.php`
  - **Verify:** Load `/how-it-works`, confirm table is sortable, no pagination (static content).

- [ ] **6.2** `resources/views/pages/legal/cookie-policy.blade.php`:
  - Locate `<table>` (line ~89).
  - Add `id="cookies-table"`.
  - Check layout extends directive. If `layouts.guest`, ensure DataTables assets are included in guest layout (done in step 2.3). If standalone, add assets include.
  - Initialize:
    ```javascript
    initDataTable('#cookies-table', {
      paging: false,
      searching: false,
      info: false,
      order: [[0, 'asc']] // Cookie Name ascending
    });
    ```
  - **Files:** `resources/views/pages/legal/cookie-policy.blade.php`
  - **Verify:** Load `/cookie-policy`, confirm table sortable, no pagination.

---

### Step 7: Verification and Testing

- [ ] **7.1** Run frontend build:
  - Execute `npm run build` in `c:/Users/Administrator/Downloads/mediacity.co.in-2026-10-13/mediacity.co.in-2026-10-13/tutor-platform/`.
  - Confirm no build errors.
  - **Command:** `npm run build`
  - **Expected:** "build complete" or similar success message.

- [ ] **7.2** Clear Laravel view cache and recompile:
  - Execute `php artisan view:clear` to clear cached views.
  - Execute `php artisan view:cache` to recompile (or just clear and let Laravel compile on-demand).
  - **Commands:** `php artisan view:clear && php artisan view:cache`
  - **Expected:** No syntax errors reported.

- [ ] **7.3** Syntax-check modified Blade files:
  - Run `php -l` on each modified Blade file to catch PHP syntax errors. For example:
    ```
    php -l resources/views/pages/admin/bookings.blade.php
    ```
  - Repeat for all 23+ modified files.
  - **Expected:** "No syntax errors detected" for each file.

- [ ] **7.4** Browser verification (manual, by user):
  - Open the application in a browser (Chrome/Edge recommended).
  - Navigate to each page with a table (start with `/admin/bookings`).
  - For each table, verify:
    - DataTables controls (search box, page length dropdown, pagination) appear.
    - Export buttons (Copy, CSV, Excel, PDF, Print) appear and are styled correctly.
    - Sorting works on each column (click column headers).
    - Search/filter works (type in search box, table filters).
    - Pagination works (click next/previous, change page length).
    - Export buttons work (click CSV, confirm download; click PDF, confirm download).
    - Responsive: resize browser to mobile width, confirm table becomes responsive (+ icon to expand rows, or horizontal scroll with responsive plugin).
    - Console (F12 → Console) has no JavaScript errors related to DataTables.
  - **Test priority order:**
    1. `/admin/bookings` (most critical admin table)
    2. `/admin/users` (large dataset potential)
    3. Parent dashboard tables (user-facing, high visibility)
    4. Other admin tables
    5. Public/marketing tables (lower priority, static content)

- [ ] **7.5** Accessibility check (basic):
  - Confirm DataTables search input has `aria-label` (DataTables should add this by default, but verify).
  - Confirm pagination links are keyboard-navigable (tab through them, press Enter).
  - Confirm screen reader can read table headers and data (use browser's accessibility inspector or NVDA/JAWS if available).
  - **Note:** Full WCAG compliance requires manual testing with assistive tech; this is a basic smoke test.

---

### Step 8: Error Handling and Edge Cases

- [ ] **8.1** Verify try/catch in `initDataTable()` function:
  - Open `resources/views/partials/datatables-assets.blade.php`.
  - Confirm the `initDataTable()` function wraps `$(selector).DataTable(options)` in `try/catch` and logs errors to console.
  - **Expected:** If a table ID is misspelled or a table doesn't exist, console shows error but other tables still initialize.

- [ ] **8.2** Test with empty tables:
  - Navigate to a page where a table might be empty (e.g., `/admin/categories` if no categories exist, or a user dashboard with no bookings).
  - Confirm DataTables still initializes and shows "No entries found" or similar message.
  - Confirm export buttons are present (exporting an empty table should produce an empty CSV/Excel file).

- [ ] **8.3** Test with large datasets (if applicable):
  - If `/admin/users` has >1000 rows, check performance:
    - Load the page, measure time to render (should be <3 seconds on a modern browser).
    - If slow, consider switching to server-side processing: modify the initialization to add `serverSide: true` and `ajax: { url: '/admin/users/data', type: 'POST' }`, then implement a Laravel controller method to return DataTables JSON. (This is a future optimization, not required for initial rollout if <1000 users.)

---

## Column Type Hints and Export Configuration

DataTables auto-detects column types (text, number, date) but explicit `columnDefs` improve accuracy. The plan above specifies `columnDefs` for each table:
- `{ type: 'date', targets: [col_index] }` for date columns (e.g., "Scheduled At").
- `{ type: 'num', targets: [col_index] }` for numeric columns (e.g., "Amount", "Duration").
- `{ orderable: false, targets: [col_index] }` for action columns (buttons/links that shouldn't be sorted).

**Date Format:** If dates are in `M j, Y` format (e.g., "Jan 15, 2026"), DataTables will parse them correctly. If they are in a non-standard format, we may need a custom sorting plugin (defer to testing; most common formats work out of the box).

**Currency:** Amounts displayed as "KSh 1,000" will be treated as text by default. To sort correctly, we can:
- Use `{ type: 'num', targets: [amount_col] }` and ensure the `<td>` has a `data-order` attribute with the numeric value, e.g., `<td data-order="1000">KSh 1,000</td>`. (Apply this in the Blade `@foreach` loop for amount columns.)
- **Implementation note:** In step 3.1 (bookings table) and similar, when modifying the Blade file, wrap currency cells like:
  ```blade
  <td data-order="{{ $booking->total_amount }}">KSh {{ number_format($booking->total_amount) }}</td>
  ```
  This ensures sorting is numeric, not alphabetic.

---

## Livewire Compatibility Note

**Livewire components in this app** (e.g., `livewire/tutors/tutor-search.blade.php`) use `wire:model` for **form inputs** (search, filters), not for table rendering. The tables themselves are server-rendered Blade `@foreach` loops, so there is **no Livewire/DataTables conflict**.

If in the future a table is refactored to be Livewire-driven (rows updated via Livewire wire actions), the initialization script must listen for Livewire's `livewire:load` and `livewire:update` events to reinitialize DataTables after Livewire re-renders the table. Example:
```javascript
Livewire.on('dataUpdated', () => {
  $('#table-id').DataTable().destroy();
  initDataTable('#table-id', options);
});
```
**For this plan, no Livewire-driven tables exist, so this is not required.**

---

## Server-Side Processing (Future Consideration)

If any table exceeds 1000 rows and client-side performance degrades, implement server-side processing:
1. Modify the table's initialization to add `serverSide: true` and `ajax: { url: '/api/datatables/users', type: 'POST' }`.
2. Create a Laravel route (e.g., `POST /api/datatables/users`) that accepts DataTables' request parameters (`draw`, `start`, `length`, `search`, `order`) and returns JSON in DataTables' expected format.
3. Use a package like `yajra/laravel-datatables` to simplify server-side response generation.

**Defer to testing.** Initially, all tables use client-side processing.

---

## Summary of Files to Create/Modify

### Create:
1. `resources/views/partials/datatables-assets.blade.php` — Shared CDN assets + initDataTable function + styling.

### Modify (add DataTables initialization):
1. `resources/views/layouts/admin.blade.php` — Add `@include('partials.datatables-assets')` in `<head>`.
2. `resources/views/layouts/app.blade.php` — Add `@include('partials.datatables-assets')` in `<head>`.
3. `resources/views/layouts/guest.blade.php` — Add `@include('partials.datatables-assets')` if needed (check step 2.3).
4. `resources/views/pages/admin/bookings.blade.php` — Add ID + script.
5. `resources/views/pages/admin/users.blade.php` — Add ID + script.
6. `resources/views/pages/admin/group-sessions/index.blade.php` — Add ID + script.
7. `resources/views/pages/admin/payouts.blade.php` — Add ID + script.
8. `resources/views/pages/admin/library.blade.php` — Add ID + script.
9. `resources/views/pages/admin/blog.blade.php` — Add ID + script.
10. `resources/views/pages/admin/clinics.blade.php` — Add ID + script.
11. `resources/views/pages/admin/subjects.blade.php` — Add ID + script.
12. `resources/views/pages/admin/categories.blade.php` — Add ID + script.
13. `resources/views/pages/admin/settings.blade.php` — Add ID + script (no export buttons).
14. `resources/views/pages/admin/clinic-show.blade.php` — Add ID + script.
15. `resources/views/pages/admin/group-sessions/show.blade.php` — Add ID + script.
16. `resources/views/pages/dashboard/parent.blade.php` — Add IDs + scripts (3 tables).
17. `resources/views/pages/dashboard/student.blade.php` — Add IDs + scripts (2 tables).
18. `resources/views/pages/dashboard/admin.blade.php` — Add ID + script.
19. `resources/views/pages/tutor/library/index.blade.php` — Add ID + script.
20. `resources/views/pages/examiner-dashboard.blade.php` — Add ID + script.
21. `resources/views/pages/examiner-clinic-details.blade.php` — Add ID + script.
22. `resources/views/pages/how-it-works.blade.php` — Add ID + script.
23. `resources/views/pages/legal/cookie-policy.blade.php` — Add ID + script.

**Total:** 1 new file, 23+ modified files.

---

## Build and Test Commands

**Frontend build:**
```bash
cd c:/Users/Administrator/Downloads/mediacity.co.in-2026-10-13/mediacity.co.in-2026-10-13/tutor-platform
npm run build
```

**Laravel view cache:**
```bash
php artisan view:clear
php artisan view:cache
```

**Syntax check (example for one file):**
```bash
php -l resources/views/pages/admin/bookings.blade.php
```

**Browser testing:**
- Open `http://localhost:8000` (or the app's URL).
- Navigate to each table page.
- Verify DataTables controls, sorting, search, pagination, export, responsive behavior.
- Open browser console (F12), confirm no JavaScript errors.

---

## Risk Mitigation

1. **jQuery Conflicts:** Since Alpine.js is already loaded, and we're adding jQuery, there's a risk of `$` conflicts. Mitigation: jQuery is loaded **before** Alpine, and we use `$(document).ready()` to ensure DOM is ready. Alpine uses `x-data`, not `$`, so no direct conflict. Test thoroughly.

2. **CSS Conflicts:** DataTables' default CSS might conflict with the app's existing table styles. Mitigation: The shared partial includes custom CSS overrides that use the app's CSS variables (`--ink`, `--trust-blue`, etc.) to ensure visual consistency.

3. **Performance:** Large tables (>500 rows) may be slow with client-side processing. Mitigation: Start with client-side; monitor performance in testing. If slow, implement server-side processing for that specific table (deferred to post-rollout optimization).

4. **Livewire:** Future Livewire-driven tables will need special handling. Mitigation: Document the Livewire reinit pattern above; implement only if/when needed.

5. **Export Button Overload:** Too many export buttons might clutter the UI. Mitigation: The shared config includes 5 buttons (Copy, CSV, Excel, PDF, Print). For info-only tables (e.g., pricing multipliers), we set `buttons: []` to hide them. If users find buttons overwhelming, we can reduce to 3 (CSV, Excel, PDF) in a future iteration.

---

## Success Criteria

- [ ] All 23+ tables have DataTables ID assigned.
- [ ] All tables show DataTables search, pagination, and export controls.
- [ ] Sorting works on all sortable columns.
- [ ] Search/filter works across all tables.
- [ ] Export buttons (CSV, Excel, PDF, Print) work and produce correct files.
- [ ] Responsive: tables adapt to mobile viewports (horizontal scroll or column hiding).
- [ ] No JavaScript console errors on any table page.
- [ ] Build and syntax checks pass (`npm run build`, `php artisan view:cache`, `php -l`).
- [ ] Accessibility: keyboard navigation and screen reader compatibility verified (basic check).
- [ ] Existing functionality (inline JS, forms, Livewire components) remains intact — no regressions.

---

## Post-Implementation Notes

- **Documentation:** After rollout, add a brief section to the project README or a new `docs/datatables.md` file explaining how to add DataTables to a new table (ID, script, columnDefs).
- **Maintenance:** If DataTables is updated to a new version, update the CDN URLs in `datatables-assets.blade.php` and test thoroughly.
- **Server-Side Processing:** If any table grows beyond 1000 rows and performance degrades, implement server-side processing for that table using the pattern described above (defer to future sprint).

---

**End of Plan**
