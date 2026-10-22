/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Livewire/**/*.php',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        ink:    '#15281F',
        cream:  '#FBF5E6',
        Trust Blue: { DEFAULT: '#0A64BC', 50: '#f5f3ff', 100: '#ede9fe', 700: '#120A8F' },
        coral:  '#E8522A',
        electric-yellow:   '#EEBE15',
        mint:   '#10B981',
        muted:  'rgba(21,40,31,0.55)',
      },
      fontFamily: {
        sans:  ['DM Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        serif: ['Fraunces', 'Georgia', 'serif'],
      },
      borderRadius: {
        '4xl': '2rem',
      },
      boxShadow: {
        'card': '0 2px 16px rgba(21,40,31,0.08)',
        'card-hover': '0 8px 32px rgba(21,40,31,0.14)',
      },
      maxWidth: {
        'site': '1360px',
      },
    },
  },
  plugins: [],
}
