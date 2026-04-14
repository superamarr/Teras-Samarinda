<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import { aboutService } from '@/api/about'
import { resolveMediaUrl } from '@/utils/media'

// Fallback images in case no dynamic image is set
import defaultHeroImg from '@/assets/images/generation_8510.jfif'
import defaultWelcomeImg from '@/assets/images/about-sec.jpg'
import defaultStoryBg from '@/assets/images/image 8.jpg'

gsap.registerPlugin(ScrollTrigger)

const aboutData = ref({
  page_hero_title: 'Tentang Teras Samarinda',
  page_hero_subtitle:
    'Teras Samarinda adalah wajah baru ruang publik Kota Samarinda yang dirancang sebagai kawasan waterfront modern di tepian Sungai Mahakam.',
  page_hero_background: '',
  welcome_title: 'Selamat Datang Di Teras Samarinda',
  welcome_text:
    'Teras Samarinda bukan sekadar ruang terbuka hijau; ia adalah sebuah pernyataan tentang transformasi urban yang berkelanjutan.',
  welcome_image: '',
  story_title: 'Story Teras Samarinda',
  story_text:
    'Di tepian Sungai Mahakam, wajah Kota Samarinda perlahan berubah. Kawasan yang dahulu dikenal padat, kurang tertata, dan lebih berfungsi sebagai jalur aktivitas biasa, kini bertransformasi menjadi ruang publik yang hidup dan penuh makna. Perubahan ini melahirkan sebuah ikon baru kota, yaitu Teras Samarinda.',
  story_background: '',
})

const isLoading = ref(true)

const getImageUrl = (path, defaultImg) => {
  return path ? resolveMediaUrl(path) : defaultImg
}

const formatText = (text) => {
  if (!text) return ''
  // Split by newlines and wrap in p tags
  return text
    .split(/\n+/)
    .filter((p) => p.trim() !== '')
    .map((p) => `<p>${p}</p>`)
    .join('')
}

const formatHeroTitle = (title) => {
  if (!title) return ''
  // Simple heuristic: if there are multiple words, italicize the last word like the design
  const words = title.split(' ')
  if (words.length > 1) {
    const lastWord = words.pop()
    return `${words.join(' ')} <span class="text-italic">${lastWord}</span>`
  }
  return title
}

const initAnimations = () => {
  // Hero background parallax
  gsap.to('.hero-bg', {
    yPercent: -30,
    ease: 'none',
    scrollTrigger: {
      trigger: '.about-hero',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    },
  })

  // Hero title parallax
  gsap.to('.hero-title', {
    yPercent: -50,
    ease: 'none',
    scrollTrigger: {
      trigger: '.about-hero',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    },
  })

  // Hero subtitle parallax
  gsap.to('.hero-subtitle', {
    yPercent: -30,
    ease: 'none',
    scrollTrigger: {
      trigger: '.about-hero',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    },
  })

  // Background parallax (Story section)
  gsap.to('.story-bg', {
    yPercent: -30,
    ease: 'none',
    scrollTrigger: {
      trigger: '.story-section',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    },
  })

  // Card floating parallax - hanging effect dari kanan atas
  gsap.fromTo(
    '.story-card',
    {
      y: 40,
      opacity: 0.6,
      scale: 0.98,
      rotate: -8,
      transformOrigin: 'right top',
    },
    {
      y: 0,
      opacity: 1,
      scale: 1,
      rotate: 0,
      transformOrigin: 'right top',
      ease: 'none',
      scrollTrigger: {
        trigger: '.story-section',
        start: 'top bottom',
        end: 'center center',
        scrub: true,
      },
    },
  )
}

onMounted(async () => {
  try {
    const response = await aboutService.get()
    if (response.data.success && response.data.data) {
      // Merge fetched data onto aboutData to preserve defaults
      aboutData.value = { ...aboutData.value, ...response.data.data }
    }
  } catch (error) {
    console.error('Failed to load about page data:', error)
  } finally {
    isLoading.value = false
    // Wait for DOM to render the new sections before applying GSAP
    nextTick(() => {
      initAnimations()
      ScrollTrigger.refresh()
    })
  }
})
</script>

<template>
  <div class="about-detail-page page-shell">
    <Navbar initial-light current-page="about-page" />

    <!-- Loading Overlay -->
    <div
      v-if="isLoading"
      class="min-vh-100 d-flex align-items-center justify-content-center bg-white"
      style="position: fixed; z-index: 9999; top: 0; left: 0; width: 100%"
    >
      <div class="spinner-border text-primary" role="status"></div>
    </div>

    <template v-else>
      <!-- About Hero Section -->
      <section class="about-hero entrance-section">
        <div class="hero-bg-wrapper">
          <img
            :src="getImageUrl(aboutData.page_hero_background, defaultHeroImg)"
            alt=""
            class="hero-bg"
          />
          <div class="hero-overlay"></div>
        </div>
        <div class="container-fluid px-3 px-md-4 px-lg-5 hero-content text-center text-white">
          <h1
            v-entrance="{ y: 60, blur: 10 }"
            class="hero-title mb-4"
            v-html="formatHeroTitle(aboutData.page_hero_title)"
          ></h1>
          <p v-entrance="{ y: 40, blur: 10, delay: 200 }" class="hero-subtitle">
            {{ aboutData.page_hero_subtitle }}
          </p>
        </div>
      </section>

      <!-- Welcome Section -->
      <section class="welcome-section page-section-pad section-stack-over">
        <div class="container-fluid px-3 px-md-4 px-lg-5 py-lg-0">
          <div class="row align-items-center gx-lg-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
              <div class="welcome-img-wrapper shadow-sm rounded-4 overflow-hidden">
                <img
                  :src="getImageUrl(aboutData.welcome_image, defaultWelcomeImg)"
                  alt="Welcome Teras Samarinda"
                  class="img-fluid w-100"
                  style="object-fit: cover; aspect-ratio: 1/1"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <h2 class="section-title mb-4">{{ aboutData.welcome_title }}</h2>
              <div class="section-text" v-html="formatText(aboutData.welcome_text)"></div>
            </div>
          </div>
        </div>
      </section>

      <!-- Story Section -->
      <section class="story-section section-stack-over">
        <div class="story-bg-wrapper">
          <img
            :src="getImageUrl(aboutData.story_background, defaultStoryBg)"
            alt=""
            class="story-bg"
          />
          <div class="story-overlay"></div>
        </div>
        <div class="story-content position-relative">
          <div class="container-fluid px-3 px-md-4 px-lg-5 page-section-pad">
            <div class="row justify-content-center">
              <div class="col-lg-10">
                <div class="story-card p-4 p-lg-5 text-center shadow-lg">
                  <h2 class="story-title mb-4" v-html="formatHeroTitle(aboutData.story_title)"></h2>
                  <div class="story-text" v-html="formatText(aboutData.story_text)"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </template>

    <Footer class="section-stack-over" />
  </div>
</template>

<style scoped>
.about-detail-page {
  background-color: #ffffff;
}

/* Hero Section Styles */
.about-hero {
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
}

.hero-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: var(--type-hero-display);
  font-weight: 400;
  line-height: 1.08;
}

:deep(.hero-title .text-italic) {
  font-style: italic;
}

.hero-subtitle {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: var(--type-hero-sub);
  max-width: var(--prose-max-width);
  margin: 0 auto;
  opacity: 0.9;
  line-height: 1.55;
  white-space: pre-wrap; /* allow basic wrapping */
}

/* Welcome Section */
.section-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: var(--type-heading-lg);
  font-weight: 400;
  color: #1a1a1a;
  line-height: 1.1;
}

:deep(.section-text p) {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: var(--type-body-relaxed);
  line-height: 1.65;
  color: #444;
  margin-bottom: 1.25rem;
}

.welcome-section {
  background-color: #ffffff;
}

/* Story Section */
.story-section {
  position: relative;
  min-height: 600px;
  display: flex;
  align-items: center;
  overflow: hidden;
}

.story-bg-wrapper {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
}

.story-bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 150%;
  object-fit: cover;
  object-position: top;
}

.story-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
}

.story-content {
  position: relative;
  z-index: 1;
  width: 100%;
}

.story-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 4px;
  position: relative;
  z-index: 2;
  transform-origin: right top;
  will-change: transform, opacity;
}

.story-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: var(--type-heading-lg);
  color: #1a1a1a;
}

:deep(.story-title .text-italic) {
  font-style: italic;
}

:deep(.story-text p) {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: var(--type-body-relaxed);
  line-height: 1.65;
  color: #333;
  margin-bottom: 1.5rem;
}
</style>
