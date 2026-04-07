import { createRouter, createWebHistory } from 'vue-router'

import LandingView from '../views/LandingView.vue'
import GalleryView from '../views/GalleryView.vue'
import EventsView from '../views/EventsView.vue'
import EventDetailView from '../views/EventDetailView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
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
      name: 'about',
      component: () => import('../views/AboutDetailView.vue'),
    },
  ],
})

export default router
