<script setup>
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'
import { systemService } from '@/api/system'

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
  <RouterView />
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

/* Smooth scroll behavior for anchor links */
html {
  scroll-behavior: smooth;
}

/* Scroll margin for sections to account for fixed navbar */
section[id] {
  scroll-margin-top: 80px;
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
