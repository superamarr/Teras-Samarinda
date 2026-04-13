<script setup>
import { ref } from 'vue'
import Sidebar from '@/components/admin/Sidebar.vue'
import AdminNavbar from '@/components/admin/AdminNavbar.vue'

const isSidebarOpen = ref(false)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}
</script>

<template>
  <div class="admin-layout d-flex">
    <!-- Overlay for mobile -->
    <div 
      v-if="isSidebarOpen" 
      class="sidebar-overlay d-lg-none" 
      @click="toggleSidebar"
    ></div>

    <!-- Sidebar -->
    <Sidebar :class="{ 'show': isSidebarOpen }" />

    <!-- Main Content -->
    <div class="main-wrapper flex-grow-1 min-vh-100 d-flex flex-column bg-light-soft">
      <AdminNavbar @toggle-sidebar="toggleSidebar" />
      
      <main class="page-content p-4 p-lg-5">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<style scoped>
.admin-layout {
  height: 100vh;
  overflow: hidden;
}

.main-wrapper {
  overflow-y: auto;
  overflow-x: hidden;
}

.bg-light-soft {
  background-color: #FAFAFA;
}

.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.4);
  z-index: 1040;
}

/* Page Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@media (max-width: 991px) {
  .page-content {
    padding: 1.5rem !important;
  }
}
</style>
