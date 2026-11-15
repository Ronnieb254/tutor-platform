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
    try {
      return localStorage.getItem(this.STORAGE_KEY) || this.DEFAULT_VARIANT;
    } catch (e) {
      return this.DEFAULT_VARIANT;
    }
  },

  /**
   * Switch to a new variant and reload the page
   */
  switchVariant(variant) {
    if (!['kenyan', 'international'].includes(variant)) {
      console.warn(`Invalid variant: ${variant}`);
      return;
    }

    try {
      localStorage.setItem(this.STORAGE_KEY, variant);
    } catch (e) {
      console.warn('Could not save curriculum preference');
    }
    
    // Reload with the variant query parameter
    const url = new URL(window.location);
    url.searchParams.set('variant', variant);
    window.location.href = url.toString();
  },

  /**
   * Initialize the switcher UI and apply curriculum context
   * Ensures buttons are found and styled before any race conditions
   */
  init() {
    const current = this.getCurrent();
    let attempts = 0;
    const maxAttempts = 10;
    
    const updateStyles = () => {
      const curriculumBtns = document.querySelectorAll('[data-curriculum-switch]');
      
      if (curriculumBtns.length === 0 && attempts < maxAttempts) {
        attempts++;
        requestAnimationFrame(updateStyles);
        return;
      }
      
      curriculumBtns.forEach((btn) => {
        const variant = btn.dataset.curriculumSwitch;
        if (variant === current) {
          btn.style.background = 'rgba(26, 45, 61, 0.15)';
          btn.style.borderColor = 'rgba(26, 45, 61, 0.3)';
        } else {
          btn.style.background = 'rgba(26, 45, 61, 0.08)';
          btn.style.borderColor = 'rgba(21, 40, 31, 0.12)';
        }
      });
    };
    
    // Start the update process
    updateStyles();
    
    // Apply curriculum class to body for global styling
    document.documentElement.setAttribute('data-curriculum', current);
    if (document.body) {
      document.body.setAttribute('data-curriculum', current);
    }
  },
};

// Export for use in other modules or inline scripts
window.CurriculumSwitcher = CurriculumSwitcher;

// Auto-init on load - wait for DOM to be ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => CurriculumSwitcher.init());
} else {
  // If script loads after DOMContentLoaded, init immediately
  CurriculumSwitcher.init();
}

// Also init on Alpine initialization to handle timing issues
if (window.Alpine) {
  window.Alpine.onElUiCreated(() => CurriculumSwitcher.init());
}
