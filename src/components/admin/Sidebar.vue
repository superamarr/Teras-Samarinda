<script setup>
import { useRouter } from 'vue-router'
import { computed } from 'vue'
import logoUrl from '@/assets/icons/logo.svg'
import { useAuthStore } from '@/stores/auth'
import { useSwal } from '@/composables/useSwal'

const router = useRouter()
const authStore = useAuthStore()
const { confirmDanger } = useSwal()

const menuItems = computed(() => {
  const baseMenu = [
    {
      group: 'MAIN',
      items: [
        { name: 'Dashboard', icon: 'bi-grid-1x2-fill', path: '/admin/dashboard' },
        { name: 'Analytics', icon: 'bi-bar-chart-fill', path: '/admin/analytics' },
      ],
    },
    {
      group: 'TRANSAKSI',
      items: [{ name: 'Booking', icon: 'bi-ticket-perforated-fill', path: '/admin/booking' }],
    },
    {
      group: 'KONTEN',
      items: [
        { name: 'Hero', icon: 'bi-card-image', path: '/admin/konten/hero' },
        { name: 'About', icon: 'bi-info-circle-fill', path: '/admin/konten/about' },
        { name: 'Activities', icon: 'bi-activity', path: '/admin/konten/activities' },
        { name: 'Facilities', icon: 'bi-building', path: '/admin/konten/facilities' },
        { name: 'Gallery', icon: 'bi-image-fill', path: '/admin/konten/gallery' },
        { name: 'Events', icon: 'bi-calendar-event-fill', path: '/admin/konten/events' },
        { name: 'Contact', icon: 'bi-geo-alt-fill', path: '/admin/konten/contact' },
      ],
    },
  ];

  if (authStore.user?.role === 'superadmin') {
    baseMenu.push({
      group: 'SISTEM',
      items: [
        { name: 'Pengaturan Website', icon: 'bi-sliders', path: '/admin/system/settings' },
        { name: 'Manajemen User', icon: 'bi-people-fill', path: '/admin/system/users' },
      ]
    });
  }

  return baseMenu;
})

const handleLogout = async () => {
  const result = await confirmDanger('Keluar Akun', 'Apakah anda benar-benar yakin ingin keluar dari dashboard admin?', 'Ya, Keluar!')
  if (result.isConfirmed) {
    await authStore.logout()
    router.push('/login')
  }
}
</script>

<template>
  <div class="admin-sidebar d-flex flex-column p-4 h-100">
    <!-- Logo -->
    <div class="sidebar-brand d-flex align-items-center mb-5">
      <img :src="logoUrl" alt="Logo" class="brand-icon me-2" />
      <span class="brand-text fw-bold">TeraSamarinda</span>
    </div>

    <!-- Menu -->
    <div class="sidebar-menu flex-grow-1">
      <div v-for="group in menuItems" :key="group.group" class="menu-group mb-4">
        <p class="menu-group-title text-uppercase fw-bolder small mb-3">
          {{ group.group }}
        </p>
        <ul class="nav flex-column gap-2">
          <li v-for="item in group.items" :key="item.name" class="nav-item">
            <router-link
              :to="item.path"
              class="nav-link d-flex align-items-center rounded-3 p-3 transition-all"
              active-class="active"
            >
              <i :class="['bi me-3', item.icon]"></i>
              <span>{{ item.name }}</span>
            </router-link>
          </li>
        </ul>
      </div>
    </div>

    <!-- Bottom Action -->
    <div class="sidebar-footer mt-auto pt-4 border-top">
      <div class="bg-white rounded-3 p-1 transition-all logout-card shadow-sm">
        <button
          @click="handleLogout"
          class="btn text-danger text-decoration-none d-flex align-items-center w-100 px-3 py-2 fw-bold border-0 bg-transparent"
        >
          <i class="bi bi-box-arrow-left me-3 fs-5"></i>
          <span>Keluar Akun</span>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');

.admin-sidebar {
  width: 280px;
  height: 100vh;
  position: sticky;
  top: 0;
  background-color: #033D4A;
  border-right: none;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  flex-shrink: 0;
}

.sidebar-menu {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding-right: 4px;
}

.sidebar-menu::-webkit-scrollbar {
  width: 4px;
}

.sidebar-menu::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar-menu::-webkit-scrollbar-thumb {
  background-color: #cbd5e0;
  border-radius: 4px;
}

.sidebar-menu::-webkit-scrollbar-thumb:hover {
  background-color: #a0aec0;
}

.brand-icon {
  width: 32px;
  height: 32px;
}

.brand-text {
  font-size: 1.25rem;
  color: #ffffff;
}

.menu-group-title {
  letter-spacing: 1.5px;
  font-size: 0.8rem;
  color: #e2e8f0 !important;
}

.nav-link {
  color: #cbd5e1;
  font-weight: 500;
  transition: all 0.2s ease;
}

.nav-link:hover {
  background-color: rgba(255,255,255,0.05);
  color: #ffffff;
}

.nav-link.active {
  background-color: #0791b0;
  color: #ffffff;
  font-weight: 600;
}

.nav-link i {
  font-size: 1.1rem;
}

.transition-all {
  transition: all 0.2s ease;
}

.sidebar-footer {
  border-color: rgba(255,255,255,0.1) !important;
}

@media (max-width: 991px) {
  .admin-sidebar {
    position: fixed;
    left: -280px;
    z-index: 1050;
  }
  .admin-sidebar.show {
    left: 0;
  }
}

.logout-card:hover {
  background-color: rgba(220, 38, 38, 0.15) !important;
  transform: translateY(-2px);
}
</style>
