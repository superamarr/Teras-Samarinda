import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import { Features } from 'lightningcss'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  build: {
    cssMinify: 'lightningcss',
  },
  css: {
    lightningcss: {
      // Nonaktifkan vendor-prefixing bawaan LightningCSS agar
      // autoprefixer (PostCSS) yang menangani prefix.
      // Ini mencegah LightningCSS menghapus backdrop-filter unprefixed.
      include: Features.Nesting,
    },
  },
})
