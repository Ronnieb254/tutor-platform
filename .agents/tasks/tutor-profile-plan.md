# Tutor Profile Page Professional Redesign Implementation Plan

## Overview
Redesign `resources/views/pages/tutors/show.blade.php` with a modern 2-column desktop layout, improved typography, better visual hierarchy, and a sticky sidebar. Mobile remains single-column. All existing functionality preserved.

## Key Design Decisions

**1. Layout Architecture**: Switch from single-column to 2-column desktop (1fr 380px) with responsive fallback. The sticky sidebar on desktop contains quick stats, CTA button, and session type prices. The full booking form stays in the tab-book panel to avoid duplication.

**2. Spacing & Typography**: Use 24px base spacing (Tailwind-inspired) with better hierarchy. Keep Fraunces for headers (h1–h3), DM Sans for body. Improve line-height and letter-spacing.

**3. Color & Contrast**: Leverage the design system (--trust-blue, --mint, --coral, --gold from head.blade.php). Ensure WCAG AA contrast on all text.

**4. Responsive Strategy**: Mobile: single-column (hero → About → Availability → Reviews → Book). Tablet (≥640px): improved spacing, 2-col subjects grid. Desktop (≥1024px): full 2-col with sticky sidebar.

**5. Remove "Years Experience"**: Strip any display of years_of_experience field if present in the model.

**6. CSS Organization**: Group styles by component (hero, tabs, about, subjects, availability, reviews, sidebar, booking, forms). Use media queries at the end of each section, not globally.

---

## Implementation Steps

### Step 1: Refactor Hero Section
**What**: Improve hero visual hierarchy, cleaner badge/verification display, better stats row with consistent spacing.

**Files**:
- `resources/views/pages/tutors/show.blade.php` (replace `.profile-hero` section and header markup)

**Details**:
- Increase breadcrumb color contrast (currently `rgba(255,255,255,.5)` → `rgba(255,255,255,.7)`)
- Improve avatar sizing and border (96px → 100px, use design system colors)
- Name title: ensure `clamp()` works on mobile; add better line-height (1.1 → 1.2)
- Badge/verification: use design system badge classes (`.badge-m` for TSC, `.badge-g` for National Examiner)
- Stats row: replace flex/wrap with clearer spacing; use consistent 20px gaps; improve icon and text alignment
- Desktop price badge: keep hidden on desktop for now (sidebar will show prices)

**Verify**: 
```bash
# Visual inspection in browser at: https://tutor-platform.test/tutors/{id}
# Check: breadcrumb readable, name hierarchy clear, badges properly styled, stats row aligned
```

---

### Step 2: Create Sticky Sidebar for Desktop
**What**: Build a 380px sticky sidebar that appears to the right of main content on desktop. Contains quick stats card, Book Session CTA, and session type prices.

**Files**:
- `resources/views/pages/tutors/show.blade.php` (add sidebar markup after tab content)

**Details**:
- Sidebar wraps a `<div>` positioned at `position: sticky; top: 96px;` (below nav)
- On mobile/tablet: sidebar hidden (display: none)
- On desktop (≥1024px): sidebar visible with `grid-template-columns: 1fr 380px`
- Contains:
  1. **Quick Stats Card**: 3 vertical rows (sessions, reviews, response time) using existing colors
  2. **Book Session CTA Button**: Large, full-width button that scrolls to #tab-book when clicked
  3. **Session Types List**: Compact 3-item list showing duration, name, price (20/40/60 min); use tier-card styling but compact

**Verify**: 
```bash
# Check desktop (1024px+) layout: sidebar appears right, is sticky during scroll
# Check mobile (< 1024px): sidebar hidden, content full width
# Check CTA button clicks to booking tab and scrolls
```

---

### Step 3: Improve About Section Styling
**What**: Better typography, spacing, enhanced languages display, professional card styling with shadows.

**Files**:
- `resources/views/pages/tutors/show.blade.php` (refactor About tab markup)

**Details**:
- Increase body text font-size (16px → 17px) and improve line-height (1.9 → 2)
- Improve section header styling: use `.sec-title` or similar pattern; add icon with color
- Languages: change from pill-style flex wrap to a cleaner layout:
  - Label: use `.field-label` or similar (12px, uppercase, muted color)
  - Pills: increase padding, softer background (rgba → solid color from design system)
  - Better spacing between pills (6px → 8px)
- Remove "years experience" if displayed anywhere in bio section
- Ensure prof-card has subtle hover shadow (existing CSS is good, keep it)

**Verify**: 
```bash
# Visual inspection: About section readable, good spacing, languages display clean
# Check that "years experience" is not shown
```

---

### Step 4: Redesign Subjects Section Layout
**What**: Grid layout with category badges, better visual hierarchy, improved iconography.

**Files**:
- `resources/views/pages/tutors/show.blade.php` (refactor "Subjects Taught" card)

**Details**:
- Convert to 3-column grid on desktop (instead of 2), with proper responsive fallback:
  - Mobile: 1 column
  - Tablet (≥640px): 2 columns
  - Desktop (≥1024px): 3 columns
- Subject items: replace simple pill layout with better cards:
  - Background: use light background (similar to current)
  - Icon/dot: keep colored dot (8px circle) but position left
  - Subject name: bold, good contrast
  - Category badge: move right, smaller, muted color
  - Add subtle border and hover effect (border-color, slight lift)
- Spacing: 14px gap between items

**Verify**: 
```bash
# Visual: grid is responsive (1 col mobile, 2 tablet, 3 desktop)
# Check: subject cards have borders, hover effect works, category badges visible
```

---

### Step 5: Modernize Availability Section
**What**: Color-coded availability status, compact scannable format, better visual structure.

**Files**:
- `resources/views/pages/tutors/show.blade.php` (refactor Availability tab)

**Details**:
- Keep the existing 7-day layout but improve styling:
  - Available days: background `rgba(10,100,188,.06)` (currently `.04`), border `rgba(10,100,188,.18)` (currently `.15`)
  - Off days: background `#F9FAF9`, border `rgba(21,40,31,.08)` (keep current)
  - Day name: bold, consistent 96px width
  - Time: color `--trust-blue` (#0A64BC), smaller font (13px)
  - Badge: use `.badge-m` for Available, `.badge-dark` for Off
  - Spacing: 8px padding, 7px margin-bottom (compact scannable)

**Verify**: 
```bash
# Visual: available days clearly distinguished by color, scannable format
# Check: responsive spacing on mobile
```

---

### Step 6: Redesign Reviews Section
**What**: Rating overview with visual improvements, review card redesign with better spacing, improved star display.

**Files**:
- `resources/views/pages/tutors/show.blade.php` (refactor Reviews tab)

**Details**:
- Rating overview card:
  - Left side (rating number + stars): keep 64px font but ensure better padding (currently good)
  - Right side (bar chart): improve spacing, clearer labels
  - Ensure rating number and bar chart align better (vertical center)
- Review cards:
  - Avatar: keep but ensure background is vibrant (use assigned color from design system)
  - Header: improve spacing between name, date, and star rating
  - Comment: better line-height (1.8), comfortable reading
  - Card spacing: 16px bottom margin (instead of 16px)
  - Hover effect: subtle lift (transform translateY, shadow increase)
- Star styling: ensure consistent `#EEBE15` for filled, `#E5E7EB` for empty

**Verify**: 
```bash
# Visual: ratings clearly displayed, review cards have proper spacing
# Check: review card hover effect works, stars render correctly
```

---

### Step 7: Improve Booking Section (3-Step Flow)
**What**: Better visual hierarchy in session type cards, cleaner date/time picker, improved form fields, refined buttons.

**Files**:
- `resources/views/pages/tutors/show.blade.php` (refactor "Book Session" tab)

**Details**:
- Progress indicator: keep 3-step flow, improve dot styling and connecting lines
- Step 1 (Choose Session):
  - Session type cards (tier-card): increase padding (20px → 24px), better spacing between items
  - Icon + text alignment: improve with flexbox and consistent gaps
  - Price display: ensure readable on mobile (font-size 18px on mobile, 20px+ desktop)
  - Description text: lighter color (currently good)
  - Responsive: 1 col mobile, 2 col tablet (≥640px), 3 col desktop (≥768px)
- Step 1 (Pick Date & Time):
  - Date input: height 46px, better focus state (border + shadow)
  - Time slots: 3 col on mobile, 4 on tablet, 5 on desktop (already in CSS, ensure it works)
  - Subject selector: width 100%, styling consistent with other inputs
  - Notes textarea: 2 rows, better placeholder, validation message styling
  - Continue button: full width, 88px height, gradient, responsive text
- Step 2 (Review & Pay):
  - Summary rows: improve spacing, better backgrounds (currently good)
  - Paybill instructions: ensure readable, good contrast
  - M-Pesa code input: monospace font, uppercase, good focus state
  - Submit button: full width, responsive, loading state (spinner animation)
- Step 3 (Confirmed):
  - Success icon: green background, centered
  - Session code: large, monospace, readable
  - Confirmation steps: use checkmarks, good spacing

**Verify**: 
```bash
# Run booking flow: select session, pick date, check slots load, submit payment
# Check: all form fields have proper focus states, responsive on mobile/tablet/desktop
# Check: progress indicator updates correctly, step navigation works
```

---

### Step 8: Consolidate & Reorganize CSS
**What**: Group page-level styles by component with logical comment sections and media queries.

**Files**:
- `resources/views/pages/tutors/show.blade.php` (refactor `<style>` section)

**Details**:
- Organize as follows:
  ```
  /* Hero Section */
  .profile-hero { ... }
  .profile-hero::before { ... }
  /* [media queries] */
  
  /* Tab Navigation */
  .ptab { ... }
  /* [media queries] */
  
  /* Sidebar (Desktop) */
  .sidebar-sticky { ... }
  /* [media queries] */
  
  /* About Section */
  /* [styles] */
  /* [media queries] */
  
  /* Subjects Grid */
  /* [styles] */
  /* [media queries] */
  
  /* Availability */
  /* [styles] */
  /* [media queries] */
  
  /* Reviews */
  /* [styles] */
  /* [media queries] */
  
  /* Booking Section */
  /* [styles] */
  /* [media queries] */
  
  /* Forms & Inputs */
  .pay-field { ... }
  /* [media queries] */
  
  /* Animations */
  @keyframes spin { ... }
  ```
- Move grid-template-columns media queries inline with each component
- Use consistent breakpoints: 640px (tablet), 1024px (desktop)
- Ensure all colors reference design system CSS vars (--trust-blue, etc.)

**Verify**: 
```bash
# Code review: CSS is organized logically, no duplicate rules
# Check: all media queries work at correct breakpoints
```

---

### Step 9: Responsive Refinement & Testing
**What**: Verify all breakpoints (mobile 320px, tablet 640px, tablet 1024px) render correctly with proper spacing and alignment.

**Files**:
- `resources/views/pages/tutors/show.blade.php` (final polish)

**Details**:
- Mobile (320px–639px):
  - Hero: full-width, title responsive with clamp(), stats single-column
  - Sidebar: hidden
  - Tabs: scrollable on small screens
  - Content: full width with 24px padding
  - Booking: form fields full-width, buttons large enough to tap
- Tablet (640px–1023px):
  - Hero: improved spacing
  - Sidebar: still hidden
  - Subjects: 2-column grid
  - Content: max-width maintained, better padding
- Desktop (1024px+):
  - 2-column layout with sticky sidebar (380px)
  - Sidebar contains quick stats, CTA, session prices
  - Hero: full styling with price badge optional

**Verify**: 
```bash
# Device testing at 3 breakpoints: inspect responsive layout
# Run full browser dev tools responsive mode: 320px, 640px, 1024px
# Ensure no horizontal scrolling on mobile, all content accessible
```

---

### Step 10: Preserve All Existing Functionality
**What**: Confirm all interactive features work after redesign.

**Files**:
- `resources/views/pages/tutors/show.blade.php` (entire file)

**Details**:
- Keep all Alpine.js data binding (`x-data`, `x-model`, `x-show`, `@click`, etc.)
- Keep all Blade directives (`@auth`, `@foreach`, `@if`, etc.)
- Keep all route() calls unchanged
- Keep processPayment() function and fetchSlots() API call
- Keep showTab() JS function for tab switching
- Keep @include('partials.head'), @include('partials.nav'), @include('partials.footer')
- Keep @livewireScripts
- Keep Jitsi Meet external API script tag

**Verify**: 
```bash
# Functional testing:
# 1. Click tabs: About → Availability → Reviews → Book (tabs switch)
# 2. Booking flow: select session duration, pick date, select time slot
# 3. Enter notes, click "Continue to Review"
# 4. Review payment summary, enter M-Pesa code (or use free flow)
# 5. Submit payment, see pending or confirmed state
# 6. Check that error handling works (missing fields, etc.)
# Visual verification:
# 7. Breadcrumb navigation works
# 8. All badges render correctly
# 9. Responsive layout works on mobile/tablet/desktop
```

---

## Summary

This plan improves the tutor profile page from a basic single-column layout to a professional 2-column desktop design with:
- Cleaner hero section with better visual hierarchy
- Sticky sidebar (desktop only) with CTA and session types
- Improved About, Subjects, Availability, and Reviews sections
- Better responsive design across mobile, tablet, and desktop
- Professional typography, spacing, and color usage
- All existing functionality preserved

**Estimated effort**: 4–6 hours of implementation, 1–2 hours of testing and refinement.
