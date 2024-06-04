const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./vendor/tales-from-a-dev/flowbite-bundle/templates/**/*.html.twig"
  ],
  theme: {
    extend: {
      colors: {
        'primary': '#5146CE',
        'secondary': '#9D95EA',
        'accent': '#6B5DEA',
        'ternary': 'rgba(158,150,234,0.3)',
        'destructive': '#B90000',
        'other': '#0808110C',
        'white': '#F8F8FC',
        'foreground': '#080811',
        'muted': '#616161',
        'input': '#D1D5DB',
      },
      fontFamily: {
        'sans': ['Inter', ...defaultTheme.fontFamily.sans],
      },
      screens: {
        lg: '992px',
      },
    },
  },
  plugins: [

  ],
}
