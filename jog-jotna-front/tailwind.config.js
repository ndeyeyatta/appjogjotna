/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        jog: {
          blue:   '#1F4E79',
          teal:   '#0D9488',
          red:    '#B91C1C',
        },
      },
    },
  },
  plugins: [],
};
