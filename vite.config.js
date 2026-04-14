import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

const API_BASE_URL = 'https://taufikramadhani.web.id/backend/public'

// https://vite.dev/config/
export default defineConfig(({ mode }) => ({
  plugins: [vue(), vueDevTools()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  define: {
    'import.meta.env.VITE_API_BASE_URL': JSON.stringify(API_BASE_URL),
  },
  build: {
    // Keep both standard and -webkit backdrop-filter in output CSS.
    cssMinify: 'esbuild',
    // Inline environment variables to avoid .env dependency at runtime
    rollupOptions: {
      output: {
        manualChunks: undefined,
      },
    },
  },
  base: mode === 'production' ? '/' : '/',
}))
