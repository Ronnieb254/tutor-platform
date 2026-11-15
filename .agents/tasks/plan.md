# Implementation Plan: Curriculum-Specific Content on Five Pages

## Context
The tutor-platform Laravel app uses the `CurriculumVariant` middleware to share `$curriculum_variant`, `$isInternational`, and `$isKenyan` variables with every Blade view. The `curriculum-switcher.js` script is already published in `public/js/curriculum-switcher.js` and is included in `resources/views/partials/head.blade.php`. The middleware stores the selected variant in session and supports query parameter override.

The five target pages currently lack curriculum-specific content and do not include the curriculum-switcher.js script (except via partials on pages that include head.blade.php). The error "CurriculumSwitcher is not defined" on non-home pages is due to Alpine initialization race conditions and lack of explicit script loading on all pages.

## Key Decisions
1. **CurriculumSwitcher loading:** All five pages will explicitly load the curriculum-switcher.js script in their head or using @vite if available. This avoids reliance on includes and ensures single Alpine instance.
2. **Styling:** International variant uses navy (#1A2D3D) accents and amber (#F59E0B) highlights; Kenyan variant keeps existing cream/blue theme.
3. **Badges:** Curriculum type indicated with small-pill badges: "CBC" for Kenyan subjects, "IGCSE"/"Cambridge" etc. for international.
4. **Content:** Each page has Kenyan-focused and International-focused sections controlled by `@if($isKenyan)...@endif` and `@if($isInternational)...@endif`.

## Implementation Plan

- [ ] 1. Add curriculum-switcher.js and fix Alpine instance race conditions on all five pages.
      Ensure each page loads curriculum-switcher.js with correct timing. For pages that don't already include head.blade.php (tutor-subjects/index.blade.php, admin/subjects.blade.php, admin/library.blade.php), add the script tag directly. For pages that include head.blade.php (group-sessions/index.blade.php, examiner-clinic.blade.php), verify it's loaded correctly.
      Files: 
      - resources/views/pages/tutor-subjects/index.blade.php
      - resources/views/pages/group-sessions/index.blade.php
      - resources/views/pages/examiner-clinic.blade.php
      - resources/views/pages/admin/subjects.blade.php
      - resources/views/pages/admin/library.blade.php
      Verify: Run Laravel build commands to check there are no compilation errors. Load each page in browser and confirm `window.CurriculumSwitcher` is defined and `window.CurriculumSwitcher.getCurrent()` returns the current variant value. Check browser console for no "Alpine Expression Error" messages and no 404 on /js/curriculum-switcher.js.

- [ ] 2. Update tutor-subjects/index.blade.php: Add curriculum filter and curriculum badges.
      Add a "Curriculum" filter dropdown in the search section (above the subject categories). When filtering, display only subjects matching the current curriculum variant using localStorage value. Add a small badge (e.g., "CBC", "IGCSE") next to each subject card to indicate curriculum type. Subjects should have a curriculum_id or track field to determine which variant they belong to.
      Files: resources/views/pages/tutor-subjects/index.blade.php
      Verify: Render the page in artisan tinker or via route. Confirm subject cards display curriculum badges. Test filtering by curriculum variant. Confirm no Blade syntax errors.

- [ ] 3. Update group-sessions/index.blade.php: Change hero messaging for International variant.
      In the hero section, keep Kenyan messaging "Learn together, pay less" (current). For International variant, replace with "Study IGCSE, Cambridge & International Exams in group cohorts" and update tagline. Update the features list and description to mention exam boards (Cambridge, IB, Edexcel, American) for international. Change accent color from gold (#EEBE15) to amber (#F59E0B) when `$isInternational` is true. Update the "How group sessions work" info box messaging to mention international exam boards.
      Files: resources/views/pages/group-sessions/index.blade.php
      Verify: Navigate to the page and switch between Kenyan and International variants using the navbar switcher. Confirm messaging, accent colors, and content change appropriately. Render and check no syntax errors.

- [ ] 4. Update examiner-clinic.blade.php: Change clinic descriptions for International variant.
      Kenyan variant: Current messaging focuses on KNEC examiners, past papers (PP1, PP2, PP3), and Kenyan marking criteria.
      International variant: Replace descriptions to focus on Cambridge, IB, Edexcel, and American exam board examiners; change "past papers" messaging to "marking schemes across different exam boards"; update "Learn from the experts who set and mark the exams" to include international context (e.g., "Learn from Cambridge, IB, Edexcel, and American exam experts..."). Change accent colors to navy (#1A2D3D) and amber (#F59E0B) for international. Update the comparison section ("Why the Examiner's Clinic?") to reflect international focus (e.g., "Direct insight into Cambridge/IB marking criteria" instead of "Direct insight into KNEC marking criteria").
      Files: resources/views/pages/examiner-clinic.blade.php
      Verify: Switch between Kenyan and International variants and confirm messaging, accent colors, and focus on exam boards change. Render and check no syntax errors.

- [ ] 5. Update admin/subjects.blade.php: Add curriculum filter in search/filters section.
      Add a "Curriculum" dropdown filter in the search form (alongside Category and Status filters). Group subjects displayed in the results table by curriculum variant (Kenyan CBC, International IGCSE, Cambridge, IB, Edexcel, American). Add a new column or badge in the subjects table to show which curriculum variant each subject belongs to. When a curriculum filter is selected, display only subjects for that variant. Ensure the filter persists across pagination.
      Files: resources/views/pages/admin/subjects.blade.php
      Verify: Load the admin page, confirm the Curriculum filter dropdown appears. Filter by curriculum and verify only matching subjects display. Test pagination and filter persistence. Render and check no syntax errors.

- [ ] 6. Update admin/library.blade.php: Add curriculum filter for resources.
      Add a "Curriculum" dropdown filter in the filters section (alongside Search, Type, Category, Status). Resources table should display a "Curriculum" column showing which curriculum variant the resource is tagged for. When a curriculum filter is selected, display only resources relevant to that variant (Kenyan resources for CBC, international exam resources for international). Ensure the filter persists and works with other filters (search, type, status, category).
      Files: resources/views/pages/admin/library.blade.php
      Verify: Load the admin page, confirm the Curriculum filter dropdown appears. Filter by curriculum and verify only matching resources display. Test combined filters (e.g., curriculum + type). Render and check no syntax errors.

## Verification (Coder must complete)
1. **Blade Compilation:** Run `php artisan view:clear` and then `php artisan tinker` and render each view to confirm no syntax errors:
   ```php
   echo view('pages.tutor-subjects.index')->render();
   echo view('pages.group-sessions.index')->render();
   echo view('pages.examiner-clinic')->render();
   echo view('pages.admin.subjects')->render();
   echo view('pages.admin.library')->render();
   ```

2. **CurriculumSwitcher Availability:** Load each page in browser DevTools console and confirm:
   ```javascript
   window.CurriculumSwitcher.getCurrent() // returns 'kenyan' or 'international'
   ```

3. **Visual Testing:** Switch between Kenyan and International using navbar switcher on each page and confirm:
   - Content updates appropriately
   - Accent colors change (international uses navy #1A2D3D and amber #F59E0B)
   - Badges display correctly
   - Filters work for both variants
   - No console errors or 404s

4. **Record Results:** Add a note to commit message with exact commands run and outcomes. Document any findings that may affect the reviewer's verification.

---

## Notes for Coder
- Do not modify middleware, routes, or database schema unless critical.
- All curriculum logic should use the Blade variables `$curriculum_variant`, `$isInternational`, `$isKenyan` already available in views.
- Curriculum switcher script is already in public/js and published; just ensure it loads on each page.
- If a page does not include `resources/views/partials/head.blade.php`, add the script tag explicitly.
- When in doubt about styling, follow the pattern from home-international.blade.php for international variant styling.
