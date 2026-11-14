/**
 * Curriculum Switcher
 * Manages localStorage persistence and page reload with curriculum variant
 */

const CurriculumSwitcher = {
  STORAGE_KEY: 'curriculum_variant',
  DEFAULT_VARIANT: 'kenyan',

  /**
   * Get the current variant from localStorage
   */
  getCurrent() {
    return localStorage.getItem(this.STORAGE_KEY) || this.DEFAULT_VARIANT;
  },

  /**
   * Switch to a new variant and reload the page
   */
  switchVariant(variant) {
    if (!['kenyan', 'international'].includes(variant)) {
      console.warn(`Invalid variant: ${variant}`);
      return;
    }

    localStorage.setItem(this.STORAGE_KEY, variant);
    
    // Reload with the variant query parameter
    const url = new URL(window.location);
    url.searchParams.set('variant', variant);
    window.location.href = url.toString();
  },

  /**
   * Initialize the switcher UI
   */
  init() {
    const current = this.getCurrent();
    
    // Update switcher UI to show current variant as active
    const curriculumBtns = document.querySelectorAll('[data-curriculum-switch]');
    curriculumBtns.forEach((btn) => {
      const variant = btn.dataset.curriculumSwitch;
      if (variant === current) {
        btn.style.background = 'rgba(26, 45, 61, 1)';
        btn.style.color = '#fff';
      } else {
        btn.style.background = 'rgba(26, 45, 61, 0.08)';
        btn.style.color = 'var(--navy)';
      }
    });
  },
};

// Export for use in other modules or inline scripts
window.CurriculumSwitcher = CurriculumSwitcher;

// Auto-init on load
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => CurriculumSwitcher.init());
} else {
  CurriculumSwitcher.init();
}
