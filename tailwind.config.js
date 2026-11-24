/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#F0F6FA',
          100: '#E1EDF5',
          500: '#2585C4',
          600: '#1D6FA5',
          700: '#155682',
          800: '#0E4062',
          900: '#0B3C5D',
        },
        accent: {
          400: '#FFBE2E',
          500: '#F2A900',
          600: '#D69400',
        },
        status: {
          good: '#2E9E5B',
          'good-bg': '#EAF7EE',
          watch: '#E0A100',
          'watch-bg': '#FEF7E6',
          critical: '#D64545',
          'critical-bg': '#FDF0F0',
        },
        dummy: {
          DEFAULT: '#8A63D2',
          light: '#F3EDFD',
          border: '#D8C3F8',
        },
        bg: '#F6F8FA',
        surface: '#FFFFFF',
        'text-main': '#1F2933',
        muted: '#6B7785',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
