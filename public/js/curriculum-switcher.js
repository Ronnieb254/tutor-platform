/**
 * CurriculumSwitcher
 *
 * Single source of truth: the SERVER SESSION.
 * localStorage is used as a fast-read cache only.
 *
 * On every page load:
 *  1. The server emits window.__SERVER_CURRICULUM with the value it knows.
 *  2. If localStorage disagrees, we POST /curriculum/sync to update the
 *     server — this covers the case where the user switched on another page
 *     and the session picked it up, or where the session expired but
 *     localStorage still has their preference.
 *  3. The server value always wins on page render (Blade templates read it).
 *     The JS only syncs for the *next* request.
 *
 * To switch curriculum: call CurriculumSwitcher.switchVariant('international')
 * This saves to localStorage, POSTs to the server, then reloads the page.
 */

const CurriculumSwitcher = {
  STORAGE_KEY: 'curriculum_variant',
  SYNC_URL:    '/curriculum/sync',
  DEFAULT:     'kenyan',

  /** Read from localStorage (fast, no network). */
  getLocal() {
    try {
      return localStorage.getItem(this.STORAGE_KEY) || null;
    } catch {
      return null;
    }
  },

  /** Write to localStorage. */
  setLocal(variant) {
    try {
      localStorage.setItem(this.STORAGE_KEY, variant);
    } catch {
      // Private browsing may disallow storage — not fatal.
    }
  },

  /**
   * Get the CSRF token from the meta tag (required by Laravel).
   */
  csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  },

  /**
   * POST to /curriculum/sync to update the server session.
   * Silent — does not block rendering. Returns a Promise.
   */
  async syncToServer(variant) {
    try {
      await fetch(this.SYNC_URL, {
        method:  'POST',
        headers: {
          'Content-Type':     'application/json',
          'Accept':           'application/json',
          'X-CSRF-TOKEN':     this.csrfToken(),
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ variant }),
      });
    } catch {
      // Network failure — the server will fall back to its session on next load.
    }
  },

  /**
   * Switch curriculum: saves locally, tells the server, reloads.
   */
  async switchVariant(variant) {
    if (!['kenyan', 'international'].includes(variant)) {
      console.warn('[CurriculumSwitcher] Invalid variant:', variant);
      return;
    }

    this.setLocal(variant);
    await this.syncToServer(variant);

    // Reload the current page so Blade picks up the new server value.
    // Strip any stale ?variant= param before reloading.
    const url = new URL(window.location.href);
    url.searchParams.delete('variant');
    window.location.href = url.toString();
  },

  /**
   * Apply active-state styles to [data-curriculum-switch] buttons.
   */
  applyButtonStyles(current) {
    document.querySelectorAll('[data-curriculum-switch]').forEach(btn => {
      const v = btn.dataset.curriculumSwitch;
      if (v === current) {
        btn.style.background   = 'rgba(26, 45, 61, 0.15)';
        btn.style.borderColor  = 'rgba(26, 45, 61, 0.3)';
        btn.setAttribute('aria-pressed', 'true');
      } else {
        btn.style.background   = 'rgba(26, 45, 61, 0.08)';
        btn.style.borderColor  = 'rgba(21, 40, 31, 0.12)';
        btn.setAttribute('aria-pressed', 'false');
      }
    });
  },

  /**
   * Initialise on page load.
   * - Reads server value (always authoritative for this render).
   * - Writes it back to localStorage so the next page load has a fast read.
   * - If localStorage had a *different* value (e.g. set in a previous session
   *   that the server doesn't know about), sync it to the server so the NEXT
   *   request sees it — but do NOT reload; the current page already rendered
   *   with the server value.
   */
  init() {
    // The server emits this variable in partials/head.blade.php.
    const serverVariant = (typeof window.__SERVER_CURRICULUM === 'string')
      ? window.__SERVER_CURRICULUM
      : this.DEFAULT;

    const localVariant  = this.getLocal();

    // Always trust the server for this render.
    const current = serverVariant;

    // Keep localStorage in sync with what the server just told us.
    this.setLocal(current);

    // If localStorage had something different (preference set elsewhere),
    // silently push it to the server so the next page load picks it up.
    // We only do this when the SERVER has the default and localStorage has
    // something explicit — i.e. the user has a preference the server doesn't
    // know yet (new browser session, session expired, etc.).
    if (
      localVariant &&
      localVariant !== serverVariant &&
      serverVariant === this.DEFAULT
    ) {
      // Don't await — fire-and-forget so we don't block the page.
      this.syncToServer(localVariant);
    }

    // Set data attributes on <html> and <body> for CSS hooks.
    document.documentElement.setAttribute('data-curriculum', current);
    if (document.body) {
      document.body.setAttribute('data-curriculum', current);
    }

    // Style the switcher buttons.
    this.applyButtonStyles(current);
  },
};

// ── Expose globally ─────────────────────────────────────────────────────────
window.CurriculumSwitcher = CurriculumSwitcher;

// ── Auto-init ────────────────────────────────────────────────────────────────
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => CurriculumSwitcher.init());
} else {
  CurriculumSwitcher.init();
}

// Handle Alpine.js (some pages init Alpine after DOMContentLoaded).
document.addEventListener('alpine:init', () => CurriculumSwitcher.applyButtonStyles(
  window.__SERVER_CURRICULUM || CurriculumSwitcher.DEFAULT
));
