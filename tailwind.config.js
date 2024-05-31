const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./node_modules/flowbite/**/*.js", // set up the path to the flowbite package
    "./vendor/tales-from-a-dev/flowbite-bundle/templates/**/*.html.twig"
  ],
  theme: {
    extend: {
      colors: {
        'primary': 'hsl(245, 58%, 54%)',
        'secondary': 'hsl(246, 67%, 75%)',
        'accent': 'hsl(246, 77%, 64%)',
        'ternary': 'hsla(246, 67%, 75%, 0.3)',
        'destructive': 'hsl(0, 100%, 36%)',
        'other': 'hsla(240, 36%, 5%, 0.05)',
        'white': 'hsl(0, 0%, 98%)',
        'foreground': 'hsl(240, 36%, 5%)',
        'muted': 'hsl(0, 0%, 38%)',
        'input': 'hsl(216, 12%, 84%)',
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
    require('flowbite/plugin') // add the flowbite plugin
  ],
}
