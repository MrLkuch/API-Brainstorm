/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        background: '#E5E8F4',
        primary: '#1C49A4',
        secondary: '#DCBB00',
        tertiary: '#800000',
      },
    },
  },
  plugins: [],
}
