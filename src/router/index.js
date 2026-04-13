import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

import LandingView from '../views/LandingView.vue'
import GalleryView from '../views/GalleryView.vue'
import EventsView from '../views/EventsView.vue'
import EventDetailView from '../views/EventDetailView.vue'
import MaintenanceView from '../views/MaintenanceView.vue'
import { systemService } from '@/api/system'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth',
      }
    }
    return { top: 0 }
  },
  routes: [
    {
      path: '/',
      name: 'home',
      component: LandingView,
    },
    {
      path: '/galeri',
      name: 'gallery',
      component: GalleryView,
    },
    {
      path: '/events',
      name: 'events',
      component: EventsView,
    },
    {
      path: '/events/:id',
      name: 'event-detail',
      component: EventDetailView,
    },
    {
      path: '/tentang',
      name: 'about-detail',
      component: () => import('../views/AboutDetailView.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
      meta: { guest: true },
    },
    {
      path: '/maintenance',
      name: 'maintenance',
      component: MaintenanceView
    },
    {
      path: '/admin',
      component: () => import('../layouts/AdminLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          redirect: '/admin/dashboard',
        },
        {
          path: 'dashboard',
          name: 'admin-dashboard',
          component: () => import('../views/admin/DashboardView.vue'),
        },
        {
          path: 'analytics',
          name: 'admin-analytics',
          component: () => import('../views/admin/AnalyticsView.vue'),
        },
        {
          path: 'booking',
          name: 'admin-booking',
          component: () => import('../views/admin/BookingView.vue'),
        },
        {
          path: 'konten/hero',
          name: 'konten-hero',
          component: () => import('../views/admin/content/ContentHeroView.vue'),
        },
        {
          path: 'konten/about',
          name: 'konten-about',
          component: () => import('../views/admin/content/ContentAboutView.vue'),
        },
        {
          path: 'konten/activities',
          name: 'konten-activities',
          component: () => import('../views/admin/content/ActivitiesListView.vue'),
        },
        {
          path: 'konten/activities/:id',
          name: 'konten-activity-detail',
          component: () => import('../views/admin/content/ActivityDetailView.vue'),
        },
        {
          path: 'konten/facilities',
          name: 'konten-facilities',
          component: () => import('../views/admin/content/FacilitiesListView.vue'),
        },
        {
          path: 'konten/facilities/:id',
          name: 'konten-facility-detail',
          component: () => import('../views/admin/content/FacilityDetailView.vue'),
        },
        {
          path: 'konten/gallery',
          name: 'konten-gallery',
          component: () => import('../views/admin/content/ContentGalleryView.vue'),
        },
        {
          path: 'konten/events',
          name: 'konten-events',
          component: () => import('../views/admin/content/EventsListView.vue'),
        },
        {
          path: 'konten/events/:id',
          name: 'konten-event-detail',
          component: () => import('../views/admin/content/EventDetailView.vue'),
        },
        {
          path: 'konten/contact',
          name: 'konten-contact',
          component: () => import('../views/admin/content/ContentContactView.vue'),
        },
        {
          path: 'system/settings',
          name: 'system-settings',
          component: () => import('../views/admin/system/SystemSettingsView.vue'),
          meta: { requiresSuperAdmin: true }
        },
        {
          path: 'system/users',
          name: 'system-users',
          component: () => import('../views/admin/system/UserManagementView.vue'),
          meta: { requiresSuperAdmin: true }
        },
      ],
    },
  ],
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  const isAuthenticated = authStore.isAuthenticated

  if (to.meta.requiresAuth) {
    if (!isAuthenticated) {
      const valid = await authStore.checkAuth()
      if (!valid) {
        return next('/login')
      }
    }
  }

  if (to.meta.guest && isAuthenticated) {
    return next('/admin/dashboard')
  }

  // Check Superadmin Role
  if (to.meta.requiresSuperAdmin) {
    if (authStore.user?.role !== 'superadmin') {
      return next('/admin/dashboard')
    }
  }

  // Maintenance Guard (only check for public pages, not admin and not login)
  if (!to.meta.requiresAuth && to.name !== 'login') {
    try {
      const settingsRes = await systemService.getSettings()
      if (settingsRes.data.success) {
        const isMaintenance = settingsRes.data.data.maintenance_mode === 1
        
        if (to.name === 'maintenance') {
          if (!isMaintenance) {
            return next('/')
          }
        } else {
          if (isMaintenance) {
            return next('/maintenance')
          }
        }
      }
    } catch (e) {
      // ignore
    }
  }

  // Force session logout if user navigates away to public view (Security feature as requested)
  if (!to.path.startsWith('/admin') && to.name !== 'login') {
    authStore.logout()
  }

  next()
})

export default router
