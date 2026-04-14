<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import EventCard from '@/components/ui/EventCard.vue'
import { eventService } from '@/api/events'
import { resolveMediaUrl } from '@/utils/media'

gsap.registerPlugin(ScrollTrigger)

const events = ref([])
const isLoading = ref(true)

const heroSettings = ref({
  page_hero_title: 'Kegiatan & Event Teras Samarinda',
  page_hero_subtitle: 'Intip keceriaan pengunjung dan keindahan arsitektur Teras Samarinda',
  page_hero_background_url: '',
})

const fetchEvents = async () => {
  isLoading.value = true
  try {
    const [eventsRes, settingsRes] = await Promise.all([
      eventService.getAll(),
      eventService.getSettings(),
    ])

    if (eventsRes.data.success) {
      events.value = eventsRes.data.data || []
    }

    if (settingsRes.data.success && settingsRes.data.data) {
      const d = settingsRes.data.data
      heroSettings.value = {
        page_hero_title: d.page_hero_title || heroSettings.value.page_hero_title,
        page_hero_subtitle: d.page_hero_subtitle || heroSettings.value.page_hero_subtitle,
        page_hero_background_url: d.page_hero_background_url || '',
      }
    }
  } catch (error) {
    console.error('Failed to fetch events:', error)
  } finally {
    isLoading.value = false
    await nextTick()
    animateHero()
  }
}

const animateHero = () => {
  // Hero background parallax
  gsap.to('.hero-bg', {
    yPercent: -30,
    ease: 'none',
    scrollTrigger: {
      trigger: '.events-hero',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    },
  })

  // Hero content parallax
  gsap.to('.hero-content', {
    yPercent: -40,
    ease: 'none',
    scrollTrigger: {
      trigger: '.events-hero',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    },
  })
}

const isVideoHero = (url) => {
  if (!url) return false
  return url.match(/\.(mp4|webm|ogg)$/i)
}

onMounted(() => {
  fetchEvents()
})
</script>

<template>
  <div class="events-page page-shell">
    <Navbar initial-light current-page="event-page" />

    <!-- Events Hero Section -->
    <section class="events-hero entrance-section">
      <div class="hero-bg-wrapper">
        <template v-if="heroSettings.page_hero_background_url">
          <video
            v-if="isVideoHero(heroSettings.page_hero_background_url)"
            :src="resolveMediaUrl(heroSettings.page_hero_background_url)"
            autoplay
            muted
            loop
            playsinline
            class="hero-bg"
          ></video>
          <img
            v-else
            :src="resolveMediaUrl(heroSettings.page_hero_background_url)"
            class="hero-bg"
            alt="Events Hero"
          />
        </template>
        <div v-else class="hero-bg-placeholder bg-dark w-100 h-100"></div>
        <div class="hero-overlay"></div>
      </div>
      <div
        class="container-fluid px-3 px-md-4 px-lg-5 h-100 d-flex flex-column justify-content-center align-items-center text-center"
      >
        <div class="hero-content position-relative">
          <h1
            v-entrance="{ y: 60, blur: 10 }"
            class="hero-title mb-4"
            v-html="heroSettings.page_hero_title"
          ></h1>
          <p v-entrance="{ y: 40, blur: 10, delay: 200 }" class="hero-subtitle">
            {{ heroSettings.page_hero_subtitle }}
          </p>
        </div>
      </div>
    </section>

    <!-- Events Grid Section -->
    <section class="events-content page-section-pad section-stack-over">
      <div class="container-fluid px-3 px-md-4 px-lg-5 py-lg-0">
        <!-- Loading State -->
        <div v-if="isLoading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status"></div>
        </div>

        <!-- Events Grid -->
        <div v-else-if="events.length > 0" class="row g-4">
          <div v-for="event in events" :key="event.id" class="col-lg-4 col-md-6">
            <EventCard
              :image="resolveMediaUrl(event.image)"
              :date="event.date"
              :title="event.name"
              :description="event.description"
              :link="`/events/${event.id}`"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-5">
          <p class="text-secondary fs-5">Belum ada event yang tersedia saat ini.</p>
        </div>
      </div>
    </section>

    <div class="position-relative" style="z-index: 10">
      <Footer />
    </div>
  </div>
</template>

<style scoped>
.events-page {
  background-color: #ffffff;
}

/* Hero Section Styles */
.events-hero {
  position: sticky;
  top: 0;
  z-index: var(--z-hero-sticky);
  min-height: 100vh;
  min-height: 100svh;
  min-height: 100dvh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-bg-wrapper {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
}

.hero-bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 150%;
  object-fit: cover;
  object-position: center;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(3, 61, 74, 0.75);
  z-index: 1;
}

.hero-content {
  position: relative;
  z-index: 2;
}

.hero-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: var(--type-hero-display);
  font-weight: 400;
  line-height: 1.08;
  color: #ffffff;
}

:deep(.hero-title span),
.hero-title :deep(span) {
  font-style: italic;
}

.hero-subtitle {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: var(--type-hero-sub);
  max-width: var(--prose-max-width);
  margin-left: auto;
  margin-right: auto;
  opacity: 0.9;
  line-height: 1.55;
  color: #ffffff;
}

.events-content {
  background-color: #ffffff;
}

.hero-bg-placeholder {
  background: linear-gradient(135deg, #033d4a 0%, #0791b0 100%);
}
</style>
