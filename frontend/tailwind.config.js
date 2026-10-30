/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        spotify: {
          DEFAULT: '#1DB954',
          green: '#1DB954',
          'green-light': '#1ed760',
          'green-dark': '#169c46',
          black: '#0a0a0a',
          dark: '#121212',
          surface: '#181818',
          'surface-hover': '#242424',
          border: '#282828',
          'border-light': '#383838',
          muted: '#a7a7a7',
          subtext: '#6a6a6a',
        },
      },
      fontFamily: {
        sans: [
          'Circular Std',
          'Plus Jakarta Sans',
          '-apple-system',
          'BlinkMacSystemFont',
          'Segoe UI',
          'Roboto',
          'sans-serif',
        ],
      },
      keyframes: {
        pulseGlow: {
          '0%, 100%': { opacity: '0.4' },
          '50%': { opacity: '0.8' },
        },
      },
      animation: {
        'pulse-glow': 'pulseGlow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
    },
  },
  plugins: [],
}
