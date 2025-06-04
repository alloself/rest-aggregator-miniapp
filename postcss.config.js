/** @type {import('postcss-load-config').Config} */
import tailwindcss from '@tailwindcss/postcss'
import nested from 'postcss-nested'
import autoprefixer from 'autoprefixer'

export default {
  plugins: [
    tailwindcss(),
    nested(),
    autoprefixer(),
  ],
}; 