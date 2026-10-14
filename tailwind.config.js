/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Livewire/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        ink:    '#15281F',
        cream:  '#FBF5E6',
        violet: '#6D28D9',
        coral:  '#E8522A',
        gold:   '#D4A017',
        mint:   '#10B981',
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
      },
    },
  },
  plugins: [],
};
