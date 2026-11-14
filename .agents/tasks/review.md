# International Homepage Variant with Curriculum Switcher

The international homepage variant is added as a new view route with a curriculum switcher in the navbar. The variant defaults to Kenyan on first load, switches via localStorage persistence, and the international view displays all spec elements: hero with search panel, 4-column exam board grid, subject chips, navy features section, and CBC-to-IGCSE transition banner. The existing Kenyan homepage remains unchanged and unaffected.

**Watch for:**
- **Emoji rendering inconsistency** (likely) — buttons use raw emoji literals in HTML attributes; fallback needed for older browsers or devices with poor emoji support.
- **Switcher button label hidden** (confirmed) — the `.cs-label` span is set to `display:none` and never shown; button only displays globe icon.
- **Unnecessary data fetched for international variant** (confirmed) — `categories` queried but unused in home-international view.
- **First-visit experience always Kenyan** (likely) — localStorage is only set after a switch click; first-time visitors always see Kenyan regardless of spec intent.

**Verdict**: NEEDS_CHANGES

---

<details>
<summary>High-level view</summary>

The HomeController now accepts a `variant` query parameter (defaulting to 'kenyan'), validates it, and routes to either `pages.home` or `pages.home-international`. The international view loads separately with navy/amber theming, matching the spec closely: hero section has correct headline and search panel with dropdowns, exam boards render in a responsive 4-column grid with per-board accent colors (Cambridge blue, IB teal, Edexcel amber, American indigo), subject browse includes all 11 required subjects, features section is dark navy with amber icons, and CBC-to-IGCSE transition banner is present with correct messaging.

The curriculum switcher JavaScript module manages localStorage under key `curriculum_variant` with values 'kenyan'/'international', and exposes a global `CurriculumSwitcher` object with `switchVariant()` and `init()` methods. The switcher is added to the navbar as a dropdown with two buttons to switch between variants. On switch, the page reloads with the new variant as a query parameter, and localStorage is updated so the choice persists across the site.

The Kenyan homepage (home.blade.php) is untouched—no modifications to file, no view changes, no data model changes. The navbar is shared between both variants; the switcher is added globally to all pages, so users can switch variants from anywhere.

CSS theme variables use navy (#1A2D3D), amber (#F59E0B), and white backgrounds with Fraunces serif headlines. Responsive breakpoints are implemented: 1 column on mobile, 2 columns at 640px, 4 columns at 1024px for both exam boards and features grids.

</details>

<details>
<summary>Issues (5)</summary>

1. **Emoji literals in button attributes** — The navbar switcher uses raw emoji characters (🇰🇪, 🌍) in inline HTML strings. These may not render consistently on older browsers, accessibility overlays, or screen readers. Use CSS or icon fonts instead.

2. **Switcher button label always hidden** — The `.cs-label` span in the switcher button is hard-coded to `display:none` and is never revealed. The button only shows the globe icon. This appears unintended; either the label should be visible or the markup should be removed.

3. **Unused data query in international view** — The HomeController fetches `categories` for both views, but home-international.blade.php doesn't use it (no category selection in hero form or display). Either remove the query for international requests or use the data in the view.

4. **localStorage state lag on first visit** — First-time visitors always see the Kenyan variant because localStorage is empty. The switcher only sets localStorage after a user clicks to switch. If the brief intends for the international variant to be accessible without pre-set localStorage, the HomeController should offer a way to deep-link to international (via query param, which works), but this isn't documented.

5. **Switcher initialization may race with page load** — The `CurriculumSwitcher.init()` runs on DOMContentLoaded, and updates button styles inline. If buttons are rendered or styled after JS runs, or if Alpine's event handlers fire before or after JS setup, button active state may not reflect correctly. No race condition detected in the code, but the pattern is fragile.

</details>

<details>
<summary>Details</summary>

### Controller routing and variant handling

The `HomeController::index()` now reads a `variant` query parameter, defaults to 'kenyan', and validates against a whitelist `['kenyan', 'international']`. Invalid values reset to 'kenyan'. The controller then selects the view (`pages.home` or `pages.home-international`) and passes `$variant` along with the data. This is a safe, stateless routing pattern—each request is independent, and the variant is determined by query string or localStorage on the client.

The data payload (categories, tutors, posts, clinics) is identical for both views. The international view doesn't use `categories`, so this is a minor optimization opportunity but not a functional issue.

### International homepage spec compliance

**Hero section:** headline is "Expert tutors for international curriculum." Badge shows "International Curriculum" with globe icon. Tagline connects qualified tutors with international exams. Two CTAs: "Find an international tutor" (links to `tutors.index?curriculum=international`) and "Explore exam boards" (jumps to #exam-boards anchor). Find-your-tutor panel on the right has Curriculum (pre-filled "International"), Level dropdown (Primary, Lower Secondary, Upper Secondary, Advanced), and Subject dropdown with all 11 subjects (Mathematics, Physics, Chemistry, Biology, English, Business Studies, Economics, Computer Science, French, History, Geography). Form submits to tutors.index. Layout is single column on mobile, side-by-side (1fr 480px) on desktop via `.hero-intl` media query.

**Exam boards grid:** 4-column responsive layout (1 column mobile, 2 at 640px, 4 at 1024px). Each board has a branded icon box, title, description, level badges, and two buttons (Browse tutors, Waitlist). Cambridge (blue #1D4F8C), IB (teal #003D7A), Edexcel (amber #F59E0B), American (indigo #1E40AF). Levels match spec exactly: Cambridge Primary/Lower Secondary/IGCSE/AS & A Level; IB PYP/MYP/DP & EE/Support; Edexcel IGCSE/A Level/Past Papers/Library; American High School/AP/SAT & ACT/Support. Hover effects change border color to the board's accent and add shadow.

**Subject browse:** 11 chips in a flex row, centered, navy background with white text, hover inverts to transparent background with navy text. Each chip links to `tutors.index?subject={subject}&curriculum=international`.

**Features section:** dark navy background with 4 cards (1 column mobile, 2 at 640px, 4 at 1024px). Each card has an amber icon, title (Fraunces serif), and description. Features: Exam Board Expertise, Past Papers & Mark Schemes, Exam-Series Intensives, Clear Pricing. Cards hover to a lighter navy background.

**Transition banner:** navy background with 2px amber border, centered content, badge "Curriculum Transition" with emoji icon, h2 "Switching from CBC to IGCSE?", description paragraph, secondary button "See the transition guide" (no href; placeholder link).

### Curriculum switcher: client-side state management

`CurriculumSwitcher` JavaScript module uses localStorage key `curriculum_variant` with default 'kenyan'. The `switchVariant(variant)` method validates input, sets localStorage, and reloads the page with `?variant={variant}` appended to the URL. The `init()` method runs on page load, reads localStorage, queries for all buttons with `[data-curriculum-switch]` attribute, and styles them: active button gets `background:rgba(26,45,61,1)` (opaque navy) and white text; inactive buttons get `background:rgba(26,45,61,0.08)` (light navy) and navy text.

The module is imported in `app.js` and exported to `window.CurriculumSwitcher` so Alpine event handlers can call it. Alpine's `@click` event on buttons triggers the switch.

**Implementation notes:**
- Module auto-initializes on DOMContentLoaded or immediately if DOM is already loaded (via `document.readyState` check).
- No validation of variant state across page navigation; each page independently checks query string and localStorage. This is stateless and safe.
- localStorage is set only when user explicitly switches; first visit has no localStorage entry, so `getCurrent()` returns the default 'kenyan'.

### Navbar switcher UI

The dropdown is added to `partials/nav.blade.php` in the Right Actions section, placed before the notification bell. Alpine component with `x-data="{ csopen: false }"` manages dropdown state. Button shows globe icon and a hidden label span (`.cs-label` with `display:none`). Two options: Kenyan (CBC) and International. Both buttons have `data-curriculum-switch` attribute for JS targeting.

**Issues with this implementation:**
- Emoji characters (🇰🇪, 🌍) are embedded directly in HTML text and inline style strings. This works but is fragile for cross-browser compatibility and accessibility.
- Label span is never shown; appears to be incomplete or left over from design iteration.
- Alpine dropdown uses `x-transition` for smooth reveal, but no viewport adjustment if dropdown would overflow screen edge (minor UX issue).

### CSS theme variables

International view defines custom properties in a `<style>` block:
```
--navy: #1A2D3D
--amber: #F59E0B
--white: #FFFFFF
--cambridge: #1D4F8C
--ib: #003D7A
--edexcel: #F59E0B
--american: #1E40AF
--muted: rgba(26,45,61,.6)
--light: rgba(26,45,61,.1)
```

Kenyan view (home.blade.php) uses its own palette (ink, cream, trust-blue, coral, gold, mint). The international view is completely separate; no shared CSS classes (except utility classes like `.w` for wrapper). This is safe but means no theme inheritance between variants.

### Responsive design

Exam boards and features grids use CSS media queries with `!important` to override inline styles:
```css
.exam-boards-grid { grid-template-columns: 1fr !important; }
@media(min-width:640px) { .exam-boards-grid { grid-template-columns: repeat(2, 1fr) !important; } }
@media(min-width:1024px) { .exam-boards-grid { grid-template-columns: repeat(4, 1fr) !important; } }
```

Hero section uses `.hero-intl` class with media query to switch from single column (mobile) to side-by-side (1fr 480px grid) at 1024px+. All spacing, font sizes, and gaps use relative units or `clamp()` for smooth scaling.

### Kenyan homepage unchanged

The original `home.blade.php` is not modified. The `HomeController` conditionally loads it when `variant !== 'international'`. No changes to styling, data, or structure. Users who don't interact with the switcher or who refresh without a variant query parameter will always see the Kenyan cream-and-blue homepage.

### Missing test coverage

No visible test files for the switcher logic, variant routing, localStorage persistence, or responsive layout. The brief does not specify tests as a requirement, so this is informational only.

</details>

<details>
<summary>File map</summary>

- `app/Http/Controllers/HomeController.php` — Added `Request $request` parameter to `index()`, variant query parsing with validation, conditional view selection, and `$variant` compact.
- `resources/views/pages/home-international.blade.php` — New file. Complete international homepage with navy/amber theme, hero, exam board grid, subject chips, features, transition banner.
- `resources/views/partials/nav.blade.php` — Added curriculum switcher dropdown to right actions section (before notification bell).
- `resources/js/curriculum-switcher.js` — New file. Client-side module managing localStorage and page reload on variant switch.
- `resources/js/app.js` — Added import for `./curriculum-switcher`.
- `tailwind.config.js` — Quoted `trust-blue` color name to fix Tailwind config syntax (minor fix).
- `resources/views/pages/home.blade.php` — No changes.

Full diff: `git diff HEAD~1 HEAD` from commit `f0abea4`.

</details>
