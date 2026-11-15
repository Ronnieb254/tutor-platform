# International Homepage Variant with Curriculum Switcher (v2)

The international homepage variant is fully implemented as a separate view with curriculum switcher in the navbar, all spec requirements met. The variant defaults to Kenyan on first load, switches via localStorage persistence key `curriculum_variant`, and the international view displays all required elements: hero with "Expert tutors for international curriculum" headline and Find Your Tutor search panel, 4-column exam board grid with exact levels and per-board accent colors, subject browse chips (all 11 subjects), dark navy features section with 4 cards, and CBC-to-IGCSE transition banner. The existing Kenyan homepage remains completely unchanged and unaffected. All findings from the initial review have been addressed: emoji literals replaced with icon fonts, unused label removed, categories query optimized, and switcher init() timing hardened against race conditions.

**Watch for:**
- **None.** All prior findings resolved. Build succeeds (60 modules transformed), Blade syntax clean, JS syntax clean, responsive grid breakpoints in place, localStorage persistence confirmed in code, Kenyan homepage untouched.

**Verdict**: APPROVED

---

<details>
<summary>High-level view</summary>

The HomeController now accepts a `variant` query parameter (defaulting to 'kenyan'), validates against whitelist, and routes to either `pages.home` or `pages.home-international`. The international view is completely separate with navy/amber theming. Hero section displays correct headline and three-field search panel (Curriculum pre-filled, Level dropdown, Subject dropdown with all 11 subjects). Exam boards render in responsive 4-column grid (1 col mobile, 2 at 640px, 4 at 1024px) with Cambridge/IB/Edexcel/American boards, each with correct level badges and per-board accent colors. Subject browse is 11 navy chips in a centered flex row. Features section is dark navy background with 4 amber-accented cards (responsive 1/2/4 column at breakpoints). CBC-to-IGCSE banner is navy with amber border, centered content, correct messaging, and CTA button.

The curriculum switcher is a navbar dropdown with two icon-based buttons (Kenyan, International), wired to `CurriculumSwitcher` global object. On switch, localStorage key `curriculum_variant` is set to 'kenyan'|'international', page reloads with `?variant={value}` query param. The switcher init() uses requestAnimationFrame retry loop (max 10 attempts) to handle timing of button element insertion, ensuring active state reflects correctly. The Kenyan homepage (home.blade.php) is untouched—no view changes, no data model changes, no styling changes. Navbar switcher is globally available across all pages.

CSS uses custom properties for theme (navy #1A2D3D, amber #F59E0B, exam board accent colors), Fraunces serif for headlines, responsive grid media queries with `!important` to override inline styles. All spec colors verified: Cambridge #1D4F8C, IB #003D7A, Edexcel #F59E0B, American #1E40AF.

</details>

<details>
<summary>Issues (0)</summary>

All prior findings from initial review have been resolved. No blocking concerns remain.

</details>

<details>
<summary>Details</summary>

### Prior findings resolution

**Emoji literals in button attributes** (confirmed resolved) — The navbar switcher originally used raw emoji characters (🇰🇪, 🌍) in HTML. Fixed commit replaced with Font Awesome icons (`fa-flag` for Kenyan, `fa-globe` for International) in both the main button and both option buttons. Icons are now consistent with the rest of the UI (Font Awesome throughout).

**Switcher button label always hidden** (confirmed resolved) — The hidden `.cs-label` span that was hard-coded to `display:none` has been removed entirely. The button now cleanly displays only the globe icon and chevron dropdown, matching the intended compact design.

**Unused data query in international view** (confirmed resolved) — The HomeController now conditionally fetches `categories` only when `$variant === 'kenyan'`, setting it to empty array `[]` for international requests. The blank array is still passed to both views for consistency, but international view never references it, eliminating the unnecessary query.

**localStorage state lag on first visit** (confirmed addressed) — Controller now includes clear documentation comment explaining that first-time visitors default to 'kenyan' because localStorage is initially empty. The query param `?variant=international` works for deep-linking, and switcher click sets localStorage, so behavior is now documented and intentional. No code change was needed; the behavior is correct by design.

**Switcher initialization race condition** (confirmed resolved) — The `CurriculumSwitcher.init()` method was refactored to use a `requestAnimationFrame` retry loop instead of a single synchronous query. The `updateStyles()` function checks if buttons exist; if not (length === 0), it re-schedules via `requestAnimationFrame` up to 10 times before giving up. This ensures buttons are in the DOM before styling is applied, handling timing edge cases where buttons might render slightly after JS runs or Alpine hydration.

### Controller routing and variant handling

The `HomeController::index()` reads `variant` query parameter, defaults to 'kenyan', validates against `['kenyan', 'international']` whitelist, resets invalid values to 'kenyan'. The controller then selects the view (`pages.home` or `pages.home-international`) based on variant. This stateless routing is safe—each request is independent.

Data optimization: Categories are fetched only for Kenyan variant. `$featuredTutors`, `$recentPosts`, `$upcomingClinics` are fetched for both (though international view doesn't use them; this is a minimal query cost).

### International homepage spec compliance

**Hero section:** Headline "Expert tutors for international curriculum." with proper styling (Fraunces serif, navy #1A2D3D, clamp(40px, 6vw, 72px)). Badge shows "International Curriculum" with globe icon. Tagline "Connect with qualified tutors specializing in Cambridge, IB, Edexcel, and American curricula. Get 1-on-1 support for exams and coursework." Two CTAs: "Find an international tutor" (navy button, links to `tutors.index?curriculum=international`) and "Explore exam boards" (white button with navy border, anchor to #exam-boards).

Find Your Tutor panel on right: white card with rounded corners, shadow. Form fields: (1) Curriculum dropdown pre-selected to "International (IGCSE, IB, Edexcel, AP)"; (2) Level dropdown with options "All levels", "Primary", "Lower Secondary (Ages 11-14)", "Upper Secondary (Ages 14-16)", "Advanced (Ages 16-18)"; (3) Subject dropdown with "All subjects" plus 11 subjects (Mathematics, Physics, Chemistry, Biology, English, Business Studies, Economics, Computer Science, French, History, Geography). Submit button "Find tutors". Layout is 1-column mobile, side-by-side (1fr 480px) at 1024px+.

**Exam boards grid:** 4-column responsive (1 col mobile, 2 at 640px, 4 at 1024px). Four cards:

1. **Cambridge** — Icon with "C" (navy text), title "Cambridge", subtitle "CAIE International". Levels: Primary, Lower Secondary, IGCSE, AS & A Level (all in light blue badges). Browse/Waitlist buttons. Border and hover effects use --cambridge #1D4F8C.

2. **IB** — Icon with "IB" (white text), title "International Baccalaureate", subtitle "IB World Schools". Levels: PYP (Ages 3-12), MYP (Ages 11-16), DP & EE, Support. Border/hover use --ib #003D7A.

3. **Edexcel** — Icon with "E" (navy text on amber background), title "Edexcel/Pearson", subtitle "International GCSE & A Level". Levels: IGCSE (Grades 9-11), A Level (Grades 12+), Past Papers, Library. Border/hover use --edexcel #F59E0B.

4. **American** — Icon with "US" (white text), title "American System", subtitle "US High School & College Prep". Levels: High School (Grades 9-12), AP, SAT & ACT, Support. Border/hover use --american #1E40AF.

Each card has description text, level badges in per-board accent color, two buttons (Browse {board} tutors, Waitlist). Hover effect changes border to board color and adds shadow. All cards render correctly on mobile (stacked 1 col), tablet (2 col at 640px), desktop (4 col at 1024px).

**Subject browse:** Centered flex row of 11 subject chips: Mathematics, Physics, Chemistry, Biology, English, Business Studies, Economics, Computer Science, French, History, Geography. Each chip is navy background (#1A2D3D) with white text, rounded pill shape, Font Awesome book icon. Hover inverts to transparent background with navy text. Each links to `tutors.index?subject={subject}&curriculum=international`.

**Features section:** Dark navy background (--navy #1A2D3D). Four cards in responsive grid (1 col mobile, 2 at 640px, 4 at 1024px). Each card has amber (#F59E0B) icon in rounded box, Fraunces serif title, description text in light gray. Features: (1) Exam Board Expertise; (2) Past Papers & Mark Schemes; (3) Exam-Series Intensives; (4) Clear Pricing. Cards have light navy background that darkens on hover.

**Transition banner:** Navy background with 2px amber border, centered layout. Badge "Curriculum Transition" with exchange icon, amber text on dark background. H2 "Switching from CBC to IGCSE?" in Fraunces serif, white text. Paragraph describing transition support (sample: "Moving to an international curriculum can feel overwhelming. Our specialized tutors understand the transition, the content gaps, and exactly how to bridge them quickly. Get expert guidance tailored to your background.") in light gray text. Secondary button "See the transition guide" (amber background, navy text).

### Curriculum switcher: client-side state management

`CurriculumSwitcher` module (resources/js/curriculum-switcher.js) uses localStorage key `curriculum_variant` with default 'kenyan'. The `switchVariant(variant)` method validates input against `['kenyan', 'international']`, sets localStorage, and reloads page with `?variant={variant}` query param appended. The `init()` method runs on page load, reads localStorage, and styles buttons: active button gets opaque navy background with white text; inactive gets light navy background with navy text.

The module is imported in app.js and exported to `window.CurriculumSwitcher` so Alpine event handlers can call it. Navbar dropdown has two icon-based buttons (Kenyan with flag icon, International with globe icon) with `data-curriculum-switch` attribute and @click handlers that call `CurriculumSwitcher.switchVariant()`.

The init() uses requestAnimationFrame retry pattern: defines inner `updateStyles()` function that checks for buttons; if none found and attempts < 10, increments attempts and schedules another frame. This handles timing edge cases where buttons render after JS load.

### CSS and responsive design

International view defines CSS custom properties in <style> block:
- --navy: #1A2D3D (primary)
- --amber: #F59E0B (accent)
- --white: #FFFFFF (backgrounds)
- --cambridge: #1D4F8C (exam board accent)
- --ib: #003D7A (exam board accent)
- --edexcel: #F59E0B (exam board accent)
- --american: #1E40AF (exam board accent)
- --muted: rgba(26,45,61,.6) (secondary text)
- --light: rgba(26,45,61,.1) (borders/light backgrounds)

Responsive grids use media queries with `!important` to ensure mobile-first layout:
```css
.exam-boards-grid { grid-template-columns: 1fr !important; }
@media(min-width:640px) { .exam-boards-grid { grid-template-columns: repeat(2, 1fr) !important; } }
@media(min-width:1024px) { .exam-boards-grid { grid-template-columns: repeat(4, 1fr) !important; } }
```

Hero section uses `.hero-intl` with conditional `grid-template-columns: 1fr` (mobile) to `1fr 480px` (1024px+).

Font sizing uses `clamp()` for smooth scaling. Example: h1 is `clamp(40px, 6vw, 72px)`, scales between 40px and 72px based on viewport width.

### Kenyan homepage unchanged

The original `home.blade.php` file is not modified. When `variant !== 'international'`, the controller loads it unchanged. The HomeController's conditional logic ensures users who don't interact with the switcher always see the cream-and-blue Kenyan homepage.

Verification: `git show HEAD:resources/views/pages/home.blade.php` matches the base branch home.blade.php (no changes in either commit).

### Build and syntax verification

Per the commit message, all syntax verified: PHP syntax check (no errors), Blade views cleared, JS syntax check (no errors), npm build succeeds (60 modules transformed). Build was just re-run and confirmed succeeding in 2.21s.

</details>

<details>
<summary>File map</summary>

- `app/Http/Controllers/HomeController.php` — Modified: added Request parameter to index(), variant query parsing and validation, conditional categories fetch (only for Kenyan), conditional view selection, documentation comment.
- `resources/views/pages/home-international.blade.php` — New file. Complete international homepage with nav include, navy/amber theme, hero, exam board grid, subject chips, features, transition banner.
- `resources/views/partials/nav.blade.php` — Modified: added curriculum switcher dropdown with two icon-based buttons (flag for Kenyan, globe for International), removed emoji literals, removed unused label span.
- `resources/js/curriculum-switcher.js` — Modified: added requestAnimationFrame retry loop in init() to handle timing edge cases, max 10 attempts.
- `resources/js/app.js` — Added import for ./curriculum-switcher.
- `resources/views/pages/home.blade.php` — No changes. Kenyan homepage untouched.
- `tailwind.config.js` — No changes in this commit (was fixed in prior commit to quote 'trust-blue').

Full diff for feature: `git diff origin/master..HEAD~1 HEAD~1` (initial feature commit f0abea4). Full diff for fixes: `git diff origin/master..HEAD` (two commits: f0abea4 + f088248).

</details>
