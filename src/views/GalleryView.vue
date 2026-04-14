<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import { galleryService } from '@/api/gallery'
import { resolveMediaUrl } from '@/utils/media'

gsap.registerPlugin(ScrollTrigger)

const galleryItems = ref([])
const isLoading = ref(true)

const heroSettings = ref({
  page_hero_title: 'Galeri Foto Teras Samarinda',
  page_hero_subtitle: 'Intip keceriaan pengunjung dan keindahan arsitektur Teras Samarinda',
  page_hero_background_url: '',
})

/**
 * Smart Logic to group items into rows that sum to 12 columns.
 * This ensures perfectly straight left, right, and bottom boundaries.
 */
const mosaicItems = computed(() => {
  if (!galleryItems.value.length) return []

  const items = [...galleryItems.value]
  const processed = []
  let i = 0

  // Patterns: [spans]
  const rowPatterns = [
    [4, 8], // Pattern A (User screenshot style)
    [8, 4], // Pattern B
    [4, 4, 4], // Pattern C
    [6, 6], // Pattern D
  ]

  let patternIndex = 0

  while (i < items.length) {
    const remaining = items.length - i
    const currentPattern = rowPatterns[patternIndex % rowPatterns.length]

    // If the remaining items can fit the next pattern
    if (remaining >= currentPattern.length) {
      currentPattern.forEach((span) => {
        if (items[i]) {
          processed.push({ ...items[i], colSpan: span })
          i++
        }
      })
      patternIndex++
    }
    // Handle remainders to ensure the last row is ALWAYS flush (sums to 12)
    else {
      if (remaining === 1) {
        processed.push({ ...items[i], colSpan: 12 })
        i++
      } else if (remaining === 2) {
        processed.push({ ...items[i], colSpan: 6 })
        processed.push({ ...items[i + 1], colSpan: 6 })
        i += 2
      }
    }
  }

  return processed
})

const fetchGallery = async () => {
  isLoading.value = true
  try {
    const [imagesRes, settingsRes] = await Promise.all([
      galleryService.getAll(),
      galleryService.getSettings(),
    ])

    if (imagesRes.data.success) {
      galleryItems.value = imagesRes.data.data || []

      // Initial animation after data load
      await nextTick()
      animateItems()
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
    console.error('Failed to fetch gallery:', error)
  } finally {
    isLoading.value = false
  }
}

const animateItems = () => {
  gsap.from('.gallery-grid-item', {
    y: 50,
    opacity: 0,
    duration: 0.8,
    stagger: 0.1,
    ease: 'power3.out',
    scrollTrigger: {
      trigger: '.gallery-flush-grid',
      start: 'top 80%',
    },
  })
}

const isVideoHero = (url) => {
  if (!url) return false
  return url.match(/\.(mp4|webm|ogg)$/i)
}

onMounted(() => {
  fetchGallery()

  // Hero background parallax
  gsap.to('.hero-bg', {
    yPercent: -30,
    ease: 'none',
    scrollTrigger: {
      trigger: '.gallery-hero',
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
      trigger: '.gallery-hero',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    },
  })
})
</script>

<template>
  <div class="gallery-page page-shell">
    <Navbar initial-light current-page="gallery-page" />

    <!-- Gallery Hero Section -->
    <section class="gallery-hero entrance-section">
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
            alt="Gallery Hero"
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
            class="hero-title mb-3"
            v-html="heroSettings.page_hero_title"
          ></h1>
          <p v-entrance="{ y: 40, blur: 10, delay: 200 }" class="hero-subtitle">
            {{ heroSettings.page_hero_subtitle }}
          </p>
        </div>
      </div>
    </section>

    <!-- Gallery Content Selection -->
    <section class="gallery-content page-section-pad section-stack-over">
      <div class="container-fluid px-3 px-md-4 px-lg-5 py-lg-0">
        <!-- Loading State -->
        <div v-if="isLoading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status"></div>
        </div>

        <!-- Perfect Flush Mosaic (Row-based 12-column grid) -->
        <div v-else class="gallery-flush-grid">
          <div
            v-for="item in mosaicItems"
            :key="item.id"
            class="gallery-grid-item"
            :style="{ gridColumn: `span ${item.colSpan}` }"
          >
            <div class="image-wrapper">
              <img :src="resolveMediaUrl(item.url)" :alt="item.title" class="img-fluid" />
              <div class="image-hover-overlay">
                <div class="text-center p-3">
                  <span class="d-block view-text">{{ item.title }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!isLoading && galleryItems.length === 0" class="text-center py-5">
          <p class="text-secondary fs-5">Belum ada foto yang tersedia.</p>
        </div>
      </div>
    </section>

    <div class="position-relative" style="z-index: 10">
      <Footer />
    </div>
  </div>
</template>

<style scoped>
.gallery-page {
  background-color: #ffffff;
}

/* Hero Section Styles */
.gallery-hero {
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
  margin: 0 auto;
  opacity: 0.9;
  line-height: 1.55;
  color: #ffffff;
}

/* Perfect Flush Mosaic Styles */
.gallery-content {
  background-color: #ffffff;
}

.gallery-flush-grid {
  display: grid;
  /* 12 columns base for perfect row math */
  grid-template-columns: repeat(12, 1fr);
  grid-auto-rows: 420px; /* Fixed row height makes bottoms align perfectly */
  gap: 20px;
  width: 100%;
}

.gallery-grid-item {
  position: relative;
  overflow: hidden;
  border-radius: 4px;
  transition: transform 0.4s ease;
}

@media (max-width: 992px) {
  .gallery-flush-grid {
    grid-auto-rows: 350px;
    gap: 15px;
  }
}

@media (max-width: 768px) {
  .gallery-flush-grid {
    grid-auto-rows: 300px;
  }
}

/* Mobile responsive simplification */
@media (max-width: 576px) {
  .gallery-flush-grid {
    grid-template-columns: repeat(1, 1fr); /* 1 column for mobile safety */
    grid-auto-rows: 250px;
    gap: 12px;
  }

  .gallery-grid-item {
    grid-column: span 1 !important;
  }
}

.image-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
}

.image-wrapper img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
  transition: transform 0.8s cubic-bezier(0.165, 0.84, 0.44, 1);
}

/* Hover Aesthetic Refinement */
.image-hover-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0;
  transition: opacity 0.5s ease;
}

.view-text {
  color: #fff;
  font-family: var(--font-family-serif);
  font-size: 1.15rem;
  font-weight: 300;
  letter-spacing: 0.5px;
  text-align: center;
  padding: 0 1rem;
  transform: translateY(15px);
  transition: transform 0.5s ease;
}

.gallery-grid-item:hover .image-wrapper img {
  transform: scale(1.1);
}

.gallery-grid-item:hover .image-hover-overlay {
  opacity: 1;
}

.gallery-grid-item:hover .view-text {
  transform: translateY(0);
}

.hero-bg-placeholder {
  background: linear-gradient(135deg, #033d4a 0%, #0791b0 100%);
}
</style>
