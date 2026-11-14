# Implementation Plan: International Curriculum Homepage Variant

## Design Decisions

1. **Variant Delivery**: Rather than modify the existing `home.blade.php` with conditional rendering, create a parallel `home-international.blade.php` and render it via a route parameter or controller logic. This keeps the Kenyan variant byte-for-byte intact and easier to maintain. The existing `HomeController::index` will remain unchanged for the Kenyan default; switching happens via localStorage + JavaScript on the client side, which toggles between showing/hiding variants via CSS or re-renders the page with `?variant=international` query param.

   **Decision**: Use client-side toggle with localStorage (key `curriculum_variant`, values `'kenyan'|'international'`) to switch the page. The controller remains single-entry. The switcher in the navbar sets localStorage and reloads the page with the preference. Each variant (Kenyan `home.blade.php`, International `home-international.blade.php`) is a complete page file.

2. **Theme Variables**: Add CSS custom properties to `resources/css/app.css` for international theme colors (navy `#1A2D3D`, amber `#F59E0B`, white, etc.) under a `.theme-international` class applied to `<html>` or `<body>` when the variant is international.

3. **Curriculum Switcher in Navbar**: Add a small "Curriculum" dropdown next to the logo or in the right actions area of the navbar partial. It shows the current variant (🇰🇪 Kenyan or 🌍 International) and a dropdown menu to switch. On selection, update localStorage, apply theme, and optionally reload or use client-side theme switching.

4. **Exam Board Accent Colors**:
   - Cambridge: `#1D4F8C` (deep blue)
   - IB: `#003D7A` (teal-navy)
   - Edexcel: `#F59E0B` (amber, matching primary)
   - American: `#1E40AF` (indigo)

5. **Fraunces Font**: Already loaded in the global CSS via Google Fonts; used for international headlines.

## Implementation Steps

### Step 1: Create the international homepage view
**File**: `c:/Users/Administrator/Downloads/mediacity.co.in-2026-10-13/mediacity.co.in-2026-10-13/tutor-platform/resources/views/pages/home-international.blade.php`

Build the complete international homepage with all sections per spec:
- Hero: left side with headline ("Expert tutors for international curriculum.") and "Find your tutor" search panel (Curriculum, Level, Subject dropdowns + 2 CTA buttons)
- Exam boards 4-column grid (Cambridge, IB, Edexcel, American) with their levels, accent colors, and "Browse" / "Join waitlist" links
- Subject chip row (11 subjects: Mathematics, Physics, Chemistry, Biology, English, Business Studies, Economics, Computer Science, French, History, Geography)
- Dark navy (#1A2D3D) features section (4 icons: exam-board expertise, past papers, exam-series intensives, clear pricing)
- "Switching from CBC to IGCSE?" transition banner with description and "See the transition guide" button
- Reuse navbar and footer partials (@include)
- Inline CSS with navy, white, and amber theme; DM Sans for body, Fraunces for headlines

### Step 2: Extend home.blade.php to support variant switcher logic
**File**: `c:/Users/Administrator/Downloads/mediacity.co.in-2026-10-13/mediacity.co.in-2026-10-13/tutor-platform/resources/views/pages/home.blade.php`

Add JavaScript at the end of the file (before closing `</body>`) that:
- On page load, reads localStorage key `curriculum_variant` (default: `'kenyan'`)
- If set to `'international'`, redirects to the page with `?variant=international` query param (routed via controller or custom middleware)
- Ensures the Kenyan variant is the default and no changes to existing Blade markup

**Alternative approach (simpler)**: Modify `HomeController::index` to check for a `variant` query param or the session/cookie set by the switcher, and render `pages.home-international` if `variant=international` is detected. Keep `home.blade.php` untouched for the Kenyan default.

**Decision**: Implement the simpler controller approach: modify `HomeController::index` to accept an optional `variant` query parameter and render either view accordingly. Store the preference in localStorage on the frontend.

### Step 3: Modify HomeController to render the international variant
**File**: `c:/Users/Administrator/Downloads/mediacity.co.in-2026-10-13/mediacity.co.in-2026-10-13/tutor-platform/app/Http/Controllers/HomeController.php`

Update the `index()` method to:
- Accept a `variant` query parameter (values: `'kenyan'` or `'international'`; default: `'kenyan'`)
- Render `pages.home` for Kenyan or `pages.home-international` for International
- Pass the current variant to the view so the switcher knows which is active

### Step 4: Add curriculum switcher dropdown to navbar
**File**: `c:/Users/Administrator/Downloads/mediacity.co.in-2026-10-13/mediacity.co.in-2026-10-13/tutor-platform/resources/views/partials/nav.blade.php`

Insert a new dropdown button in the navbar (positioned left of the notification bell or in a new section after the desktop links). The dropdown shows:
- Current curriculum variant icon + label (e.g., "🇰🇪 Kenyan")
- Two options: "Kenyan (CBC, 8-4-4)" and "International (Cambridge, IB, Edexcel, American)"
- On click, the option calls `curriculum-switcher.js` to set localStorage and reload the page with the appropriate variant

### Step 5: Create curriculum-switcher.js
**File**: `c:/Users/Administrator/Downloads/mediacity.co.in-2026-10-13/mediacity.co.in-2026-10-13/tutor-platform/resources/js/curriculum-switcher.js`

Implement a lightweight module that:
- On load: reads localStorage key `curriculum_variant` (default: `'kenyan'`)
- Exposes a `switchVariant(variant)` function that: sets localStorage, adds a URL query param, and reloads the page
- Toggles the active state indicator in the navbar dropdown
- Works on mobile and desktop (no DOM assumptions)

### Step 6: Import curriculum-switcher.js into the asset pipeline
**File**: `c:/Users/Administrator/Downloads/mediacity.co.in-2026-10-13/mediacity.co.in-2026-10-13/tutor-platform/resources/js/app.js`

Add an import statement: `import './curriculum-switcher';` (or load it inline in the navbar dropdown's `@click` handler via Alpine.js)

### Step 7: Add international theme CSS variables
**File**: `c:/Users/Administrator/Downloads/mediacity.co.in-2026-10-13/mediacity.co.in-2026-10-13/tutor-platform/resources/css/app.css`

Add a `:root.theme-international` CSS rule with variables:
- `--navy: #1A2D3D;`
- `--amber: #F59E0B;`
- `--white: #FFFFFF;`
- `--cambridge-blue: #1D4F8C;`
- `--ib-navy: #003D7A;`
- `--edexcel-amber: #F59E0B;`
- `--american-indigo: #1E40AF;`

These can be used in `home-international.blade.php` inline styles where needed.

## File Summary

| File | Action | Purpose |
|------|--------|---------|
| `resources/views/pages/home-international.blade.php` | Create | International homepage with hero, exam boards, subjects, features, transition banner |
| `resources/views/partials/nav.blade.php` | Modify | Add curriculum switcher dropdown |
| `app/Http/Controllers/HomeController.php` | Modify | Accept `variant` query param; render appropriate view |
| `resources/js/curriculum-switcher.js` | Create | Handle localStorage read/write and page reload on variant switch |
| `resources/js/app.js` | Modify | Import curriculum-switcher.js |
| `resources/css/app.css` | Modify | Add theme CSS variables for international colors |
| `resources/views/pages/home.blade.php` | No change | Remains the Kenyan variant; no modifications |

## Verification Approach

1. **Blade Syntax**: Run `php artisan view:clear && php artisan view:cache` to compile all views; check for syntax errors.
2. **Route & Controller**: Verify `HomeController::index` responds to `GET / ?variant=international` and renders `home-international.blade.php`.
3. **Switcher Functionality**: 
   - Manually inspect the navbar dropdown in a browser; click "International" and confirm localStorage key `curriculum_variant` is set to `'international'`.
   - Reload the page and confirm the international variant persists.
   - Switch back to "Kenyan" and confirm the Kenyan variant persists.
4. **Responsive Design**: 
   - Test on desktop (full width): exam board grid is 4 columns, hero layout is side-by-side.
   - Test on tablet (≥768px): exam board grid reduces to 2 columns.
   - Test on mobile (<768px): exam board grid is 1 column, hero stacks vertically.
5. **CSS Validation**: Check that no inline styles have syntax errors; colors render correctly.
6. **Kenyan Variant Regression**: Load `GET / ?variant=kenyan` (or without the param) and visually inspect the Kenyan homepage; compare to a baseline to confirm no unintended changes.

## Success Criteria

✅ Switcher appears in navbar (both desktop and mobile)  
✅ Clicking "International" loads the international design  
✅ Design matches professional international tutoring standards (navy + amber palette)  
✅ Responsive on mobile (1-column tablet, 4-column desktop for exam boards)  
✅ localStorage persists choice across page loads  
✅ No visual regressions on the Kenyan variant  
✅ Fraunces serif headlines load and render on the international page  
✅ All exam board accent colors display correctly  

