<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import ActionButton from '@/components/ui/ActionButton.vue'
import { eventService } from '@/api/events'
import { resolveMediaUrl } from '@/utils/media'

gsap.registerPlugin(ScrollTrigger)

const route = useRoute()
const event = ref(null)
const isLoading = ref(true)

const fetchEventDetail = async () => {
  isLoading.value = true
  try {
    const res = await eventService.getById(route.params.id)
    if (res.data.success) {
      event.value = res.data.data
    }
  } catch (error) {
    console.error('Failed to fetch event detail:', error)
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
      trigger: '.event-hero',
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
      trigger: '.event-hero',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    },
  })
}

onMounted(() => {
  fetchEventDetail()
})
</script>

<template>
  <div class="event-detail-page page-shell">
    <Navbar initial-light current-page="event-page" />

    <!-- Loading State -->
    <div v-if="isLoading" class="vh-100 d-flex align-items-center justify-content-center bg-white">
      <div class="spinner-border text-primary" role="status"></div>
    </div>

    <template v-else-if="event">
      <!-- Event Detail Hero -->
      <section class="event-hero entrance-section">
        <div class="hero-bg-wrapper">
          <img :src="resolveMediaUrl(event.image)" :alt="event.name" class="hero-bg" />
          <div class="hero-overlay"></div>
        </div>
        <div
          class="container-fluid px-3 px-md-4 px-lg-5 h-100 d-flex flex-column justify-content-center align-items-center text-center"
        >
          <div class="hero-content text-white position-relative">
            <div
              v-entrance="{ y: 30, blur: 10 }"
              class="event-status d-flex align-items-center gap-2 mb-4 mx-auto"
            >
              <span
                class="status-dot"
                :class="{ 'bg-secondary shadow-none': event.status !== 'Aktif' }"
              ></span>
              <span class="status-text fw-bold">EVENT {{ event.status.toUpperCase() }}</span>
            </div>

            <h1 v-entrance="{ y: 60, blur: 10, delay: 100 }" class="event-title mb-4">
              {{ event.name }}
            </h1>

            <div
              v-entrance="{ y: 40, blur: 10, delay: 200 }"
              class="event-meta d-flex flex-wrap gap-4 justify-content-center"
            >
              <div class="meta-item d-flex align-items-center gap-2">
                <i class="bi bi-calendar3"></i>
                <span>{{ event.date }}</span>
              </div>
              <div class="meta-item d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt"></i>
                <span>{{ event.location || 'Teras Samarinda' }}</span>
              </div>
              <div v-if="event.category" class="meta-item d-flex align-items-center gap-2">
                <i class="bi bi-tag"></i>
                <span>{{ event.category }}</span>
              </div>
            </div>

            <p
              v-if="event.highlights"
              v-entrance="{ y: 30, blur: 10, delay: 300 }"
              class="hero-description mt-4 mx-auto"
            >
              {{ event.highlights }}
            </p>
          </div>
        </div>
      </section>

      <!-- Event Content Section -->
      <section class="event-content page-section-pad section-stack-over">
        <div class="container-fluid px-3 px-md-4 px-lg-5 py-lg-0">
          <div class="row justify-content-center">
            <div class="col-lg-10">
              <div class="row g-5">
                <!-- Left: Main Content -->
                <div class="col-lg-8">
                  <h2 class="content-title mb-4 border-bottom pb-3">
                    Tentang <span class="text-italic">Event</span>
                  </h2>
                  <div class="content-text mb-5 text-prose">
                    <p v-for="(p, idx) in event.description.split('\n')" :key="idx">
                      {{ p }}
                    </p>
                  </div>
                </div>

                <!-- Right: Quick Info Box -->
                <div class="col-lg-4">
                  <div
                    class="info-sidebar-card p-4 rounded-4 bg-light shadow-sm sticky-lg-top"
                    style="top: 100px; z-index: 5"
                  >
                    <h5 class="fw-bold mb-4">Informasi Tambahan</h5>

                    <div class="info-item mb-3 pb-3 border-bottom">
                      <span class="d-block x-small text-secondary text-uppercase fw-bold mb-1"
                        >Tiket / Biaya</span
                      >
                      <span class="fw-bold text-dark fs-5">{{
                        event.ticket_price || 'Gratis / Terbuka'
                      }}</span>
                    </div>

                    <div v-if="event.end_date" class="info-item mb-3 pb-3 border-bottom">
                      <span class="d-block x-small text-secondary text-uppercase fw-bold mb-1"
                        >Berakhir Pada</span
                      >
                      <span class="fw-bold text-dark">{{ event.end_date }}</span>
                    </div>

                    <div class="info-item">
                      <span class="d-block x-small text-secondary text-uppercase fw-bold mb-1"
                        >Lokasi Presisi</span
                      >
                      <span class="text-dark">{{ event.location || 'Area Teras Samarinda' }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Booking Box -->
              <div
                class="booking-box p-4 p-lg-5 rounded-5 d-flex flex-column flex-md-row align-items-center justify-content-between gap-4 mt-5 shadow-lg overflow-hidden"
              >
                <div
                  class="booking-content text-white text-md-start text-center position-relative z-1"
                >
                  <h3 class="booking-title mb-3">Wujudkan Event Anda di Teras Samarinda</h3>
                  <p class="booking-subtitle mb-0">
                    Jadikan Teras Samarinda sebagai tempat terbaik untuk menggelar acara Anda. Mulai
                    dari pertunjukan seni, event komunitas, hingga hiburan publik — hadirkan
                    pengalaman tak terlupakan di ruang terbuka yang ikonik.
                  </p>
                </div>
                <div class="booking-action flex-shrink-0 position-relative z-1">
                  <ActionButton
                    text="HUBUNGI KAMI"
                    variant="light"
                    gap="0px"
                    href="https://wa.me/6281522650048"
                  />
                </div>
                <!-- Abstract Decor -->
                <div class="decor-circle position-absolute"></div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </template>

    <!-- 404 State -->
    <div
      v-else
      class="vh-100 d-flex flex-column align-items-center justify-content-center bg-white"
    >
      <h2 class="fw-bold text-dark mb-3">Event Tidak Ditemukan</h2>
      <router-link to="/events" class="btn btn-primary px-4 py-2 rounded-pill"
        >Kembali ke Daftar Event</router-link
      >
    </div>

    <div class="position-relative" style="z-index: 10">
      <Footer />
    </div>
  </div>
</template>

<style scoped>
.event-detail-page {
  background-color: #ffffff;
}

/* Hero Section Styles */
.event-hero {
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
  background-color: rgba(3, 61, 74, 0.7);
  z-index: 1;
}

.hero-content {
  position: relative;
  z-index: 2;
  max-width: 900px;
}

.event-status {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  padding: 6px 16px;
  border-radius: 100px;
  width: fit-content;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.status-dot {
  width: 8px;
  height: 8px;
  background-color: #3fe082;
  border-radius: 50%;
  box-shadow: 0 0 12px #3fe082;
}

.status-text {
  font-size: 0.75rem;
  letter-spacing: 1.5px;
}

.event-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: var(--type-hero-display);
  font-weight: 400;
  line-height: 1.08;
  color: #ffffff;
}

.event-title :deep(.text-italic),
:deep(.event-title span) {
  font-style: italic;
}

.meta-item {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: 1.1rem;
  font-weight: 500;
  color: #ffffff;
}

.meta-item i {
  color: rgba(255, 255, 255, 0.6);
}

.hero-description {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: var(--type-hero-sub);
  line-height: 1.55;
  opacity: 0.9;
  max-width: 700px;
  color: #ffffff;
}

.event-content {
  position: relative;
  z-index: 10;
  background-color: #ffffff;
}

.content-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: 2.5rem;
  font-weight: 400;
  color: #1a1a1a;
}

.content-title .text-italic {
  font-style: italic;
}

.text-prose p {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: 1.15rem;
  line-height: 1.75;
  color: #334155;
  margin-bottom: 1.5rem;
}

/* Sidebar Info Card */
.info-sidebar-card {
  border: 1px solid #f1f5f9;
}

.x-small {
  font-size: 0.65rem;
  letter-spacing: 1px;
}

/* Booking Box Styling */
.booking-box {
  background-color: #033d4a;
  position: relative;
  z-index: 1;
}

.booking-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: 1.8rem;
  font-weight: 400;
}

.booking-subtitle {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: 1.05rem;
  line-height: 1.6;
  opacity: 0.85;
}

.decor-circle {
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
  right: -50px;
  bottom: -150px;
  z-index: 0;
}

.vh-100 {
  height: 100vh !important;
}
</style>
