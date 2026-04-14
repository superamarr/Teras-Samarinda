<script setup>
import { computed, onMounted } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import { systemService } from '@/api/system'

const route = useRoute()

/** Halaman publik (landing, galeri, login, …) — skala tipografi global; admin dikecualikan */
const isPublicSite = computed(() => !route.path.startsWith('/admin'))

onMounted(async () => {
  try {
    const res = await systemService.getSettings()
    if (res.data.success && res.data.data.website_title) {
      document.title = res.data.data.website_title
    }
  } catch (error) {
    console.error('Failed to load system settings:', error)
  }
})
</script>

<template>
  <div class="app-root" :class="{ 'public-site': isPublicSite }">
    <RouterView />
  </div>
</template>

<style>
/* Full bleed: cegah strip kosong kiri/kanan & scroll horizontal */
html,
body {
  width: 100%;
  max-width: 100%;
  margin: 0;
  /* overflow-x: clip lebih aman untuk sticky daripada hidden pada beberapa browser */
  overflow-x: clip; 
}

#app {
  width: 100%;
  max-width: 100%;
  /* Jangan gunakan overflow: hidden di sini karena akan mematikan position: sticky */
  overflow: visible;
}

.app-root {
  width: 100%;
  max-width: 100%;
  min-height: 100%;
}

/* Smooth scroll behavior for anchor links */
html {
  scroll-behavior: smooth;
}

/* Scroll margin untuk anchor — menyesuaikan tinggi navbar di mobile */
section[id] {
  scroll-margin-top: clamp(4rem, 14vw, 5.25rem);
}

/* Global Glassmorphism Card */
.glass-card {
  width: 100%;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
}
</style>

<style scoped></style>
