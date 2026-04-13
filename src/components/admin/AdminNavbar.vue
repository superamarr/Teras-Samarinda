<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const authStore = useAuthStore()

const pageTitle = computed(() => {
  if (route.path.includes('/dashboard')) return 'Dashboard'
  if (route.path.includes('/analytics')) return 'Laporan Analytics'
  if (route.path.includes('/booking')) return 'Manajemen Booking'
  if (route.path.includes('/hero')) return 'Pengaturan Hero Beranda'
  if (route.path.includes('/about')) return 'Pengaturan Tentang Kami'
  if (route.path.includes('/activities')) return 'Manajemen Aktivitas'
  if (route.path.includes('/facilities')) return 'Manajemen Fasilitas'
  if (route.path.includes('/gallery')) return 'Manajemen Galeri'
  if (route.path.includes('/events')) return 'Manajemen Event'
  if (route.path.includes('/contact')) return 'Pengaturan Kontak'
  return route.name || 'TeraSamarinda Admin'
})
</script>

<template>
  <nav class="admin-navbar navbar bg-white border-bottom px-4">
    <div class="container-fluid p-0">
      <div class="d-flex align-items-center">
        <!-- Mobile Sidebar Toggle -->
        <button 
          class="btn d-lg-none me-3 shadow-none border-0 px-2 text-secondary" 
          @click="$emit('toggle-sidebar')"
        >
          <i class="bi bi-list fs-3"></i>
        </button>
        <!-- Heading -->
        <div class="d-none d-md-block">
          <h5 class="mb-0 fw-bold text-secondary">{{ pageTitle }}</h5>
        </div>
      </div>

      <!-- User Profile -->
      <div class="user-profile d-flex align-items-center ms-auto">
        <div class="text-end me-3 d-none d-sm-block">
          <p class="mb-0 fw-bold user-name">{{ authStore.user?.username || 'Admin' }}</p>
          <p class="mb-0 text-secondary small text-capitalize">{{ authStore.user?.role === 'superadmin' ? 'Super Administrator' : 'Administrator' }}</p>
        </div>
        <div class="avatar-wrapper">
          <div class="user-avatar bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm">
            <i class="bi bi-person-fill fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<style scoped>
.admin-navbar {
  height: 80px;
  border-color: #f0f0f0 !important;
}

.bg-light-gray {
  background-color: #f7f8f9;
}

.form-control:focus {
  background-color: #f0f2f4;
  box-shadow: none;
}

.user-avatar {
  width: 45px;
  height: 45px;
}

.user-name {
  color: #033d4a;
  font-size: 0.95rem;
}

@media (max-width: 576px) {
  .admin-navbar {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }
}
</style>
