<script setup>
import { ref, computed, onMounted } from 'vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import ActionButton from '@/components/ui/ActionButton.vue'
import api from '@/api/index'
import { resolveMediaUrl } from '@/utils/media'

gsap.registerPlugin(ScrollTrigger)

defineProps({
  id: String,
})

const heroData = ref({
  title_line1: 'Jelajahi Ikon Baru',
  title_line2: 'Kota Samarinda',
  title_line1_italic: ['Ikon', 'Baru'],
  title_line2_italic: ['Kota'],
  subtitle:
    'Teras Samarinda menghadirkan ruang publik modern di tepian Sungai Mahakam, tempat terbaik untuk bersantai, beraktivitas, dan menikmati keindahan kota.',
  cta_text: 'LIHAT EVENT',
  cta_link: '/events',
  cta_text_secondary: 'BOOKING EVENT',
  cta_link_secondary: 'https://wa.me/6281522650048',
  use_video: false,
  video_file: '',
  background_image: '',
})

const isLoading = ref(true)
const videoLoaded = ref(false)
const hasBackgroundImage = computed(() => !!String(heroData.value.background_image || '').trim())
const hasVideoFile = computed(() => !!String(heroData.value.video_file || '').trim())

const heroBackgroundStyle = computed(() => {
  if (hasBackgroundImage.value) {
    return {
      backgroundImage: `url(${resolveMediaUrl(heroData.value.background_image)})`,
      backgroundSize: 'cover',
      backgroundPosition: 'center',
    }
  }
  if (heroData.value.use_video && hasVideoFile.value) {
    return {
      background: 'linear-gradient(135deg, #033d4a 0%, #0791b0 100%)',
    }
  }
  return {
    background: 'linear-gradient(135deg, #033d4a 0%, #0791b0 100%)',
  }
})

const shouldShowVideo = computed(() => {
  // Prioritaskan background image jika tersedia dari admin.
  if (hasBackgroundImage.value) return false
  return heroData.value.use_video && hasVideoFile.value && videoLoaded.value
})

const hasMediaContent = computed(() => {
  return hasBackgroundImage.value || (heroData.value.use_video && hasVideoFile.value)
})

const getWords = (text) => {
  if (!text) return []
  return text.split(/\s+/).filter((w) => w.length > 0)
}

const isItalic = (lineKey, word) => {
  const italicWords = heroData.value[lineKey]
  if (!Array.isArray(italicWords)) return false
  return italicWords.includes(word)
}

const fetchHeroData = async () => {
  try {
    const response = await api.get('/hero')
    console.log('Hero API Response:', response.data)
    if (response.data.success && response.data.data) {
      const data = response.data.data
      heroData.value = {
        title_line1: data.title_line1 || 'Jelajahi Ikon Baru',
        title_line2: data.title_line2 || 'Kota Samarinda',
        title_line1_italic: Array.isArray(data.title_line1_italic) ? data.title_line1_italic : [],
        title_line2_italic: Array.isArray(data.title_line2_italic) ? data.title_line2_italic : [],
        subtitle:
          data.subtitle ||
          'Teras Samarinda menghadirkan ruang publik modern di tepian Sungai Mahakam.',
        cta_text: data.cta_text || 'LIHAT EVENT',
        cta_link: data.cta_link || '/events',
        cta_text_secondary: data.cta_text_secondary || 'BOOKING EVENT',
        cta_link_secondary: data.cta_link_secondary || 'https://wa.me/6281522650048',
        use_video: !!data.use_video,
        video_file: data.video_file || '',
        background_image: data.background_image || '',
      }
      console.log('Hero Data Updated:', heroData.value)
    }
  } catch (error) {
    console.error('Failed to fetch hero data:', error)
    if (error.response?.status === 401) {
      console.warn('Hero endpoint requires auth - using default data')
    }
  } finally {
    isLoading.value = false
  }
}

const onVideoLoad = () => {
  videoLoaded.value = true
}

const onVideoError = () => {
  videoLoaded.value = false
}

onMounted(async () => {
  await fetchHeroData()

  gsap.to('.hero-video', {
    yPercent: -30,
    ease: 'none',
    scrollTrigger: {
      trigger: '.hero-section',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    },
  })

  gsap.to('.hero-title', {
    yPercent: -40,
    ease: 'none',
    scrollTrigger: {
      trigger: '.hero-section',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    },
  })

  gsap.to('.hero-subtitle', {
    yPercent: -40,
    ease: 'none',
    scrollTrigger: {
      trigger: '.hero-section',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    },
  })

  gsap.to('.hero-button', {
    yPercent: -20,
    ease: 'none',
    scrollTrigger: {
      trigger: '.hero-section',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    },
  })
})
</script>

<template>
  <div
    :id="id"
    class="hero-section d-flex align-items-center entrance-section"
    :style="heroBackgroundStyle"
  >
    <!-- Video Background (only shows if use_video is true and video loads successfully) -->
    <video
      v-if="heroData.use_video && hasVideoFile && !hasBackgroundImage"
      class="hero-video"
      :class="{ 'video-loaded': shouldShowVideo }"
      autoplay
      loop
      muted
      playsinline
      @canplay="onVideoLoad"
      @error="onVideoError"
    >
      <source :src="resolveMediaUrl(heroData.video_file)" type="video/webm" />
      <source :src="resolveMediaUrl(heroData.video_file)" type="video/mp4" />
    </video>
    <div v-if="shouldShowVideo" class="hero-video-overlay"></div>

    <!-- Overlay for darkening the background (always show when video/image is present) -->
    <div v-if="hasMediaContent || shouldShowVideo" class="hero-overlay"></div>

    <div class="container-fluid px-3 px-md-4 px-lg-5 hero-content">
      <div class="row">
        <!-- Tambahkan class hero-text-container di sini untuk mengatur posisi atas-bawah -->
        <div class="col-lg-8 col-md-10 hero-text-container">
          <h1 v-entrance="{ x: -60, blur: 10 }" class="hero-title mb-3 mb-md-4">
            <template
              v-for="(word, index) in getWords(heroData.title_line1)"
              :key="'line1-' + index"
            >
              <span :class="{ 'text-italic': isItalic('title_line1_italic', word) }">{{
                word
              }}</span
              >{{ index < getWords(heroData.title_line1).length - 1 ? ' ' : '' }}
            </template>
            <br />
            <template
              v-for="(word, index) in getWords(heroData.title_line2)"
              :key="'line2-' + index"
            >
              <span :class="{ 'text-italic': isItalic('title_line2_italic', word) }">{{
                word
              }}</span
              >{{ index < getWords(heroData.title_line2).length - 1 ? ' ' : '' }}
            </template>
          </h1>
          <p v-entrance="{ x: -40, blur: 10, delay: 200 }" class="hero-subtitle mb-4">
            {{ heroData.subtitle }}
          </p>

          <!-- Gunakan ActionButton Component Reusable -->
          <div
            v-entrance="{ x: -30, blur: 10, delay: 400 }"
            class="hero-button d-flex gap-3 flex-wrap"
          >
            <ActionButton
              :text="heroData.cta_text"
              variant="light"
              gap="0px"
              :href="heroData.cta_link"
            />
            <ActionButton
              :text="heroData.cta_text_secondary"
              variant="outline"
              gap="0px"
              :href="heroData.cta_link_secondary"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* ==============================================================
   PENGATURAN POSISI & JARAK (UBAH NILAINYA DI SINI)
   ============================================================== */

/* 1. Atur offset vertikal blok teks dari pusat flex (desktop vs mobile) */
.hero-text-container {
  margin-top: clamp(3.5rem, 10vh, 6.25rem);
}

/* ============================================================== */

.hero-section {
  /* Sticky: About + section lain (.section-stack-over) meluncur di atas hero */
  position: sticky;
  top: 0;
  z-index: var(--z-hero-sticky, 100);

  /*
   * Jangan pakai align-self: flex-start di bawah parent flex-direction: column —
   * di mobile sering membuat lebar = lebar konten, bukan 100% layar (strip putih di kanan).
   */
  align-self: stretch;
  width: 100%;
  max-width: none;
  min-width: 0;
  box-sizing: border-box;

  /* Satu layar penuh; lvh = large viewport (Chrome) bantu saat UI browser minim */
  min-height: 100vh;
  min-height: 100svh;
  min-height: 100dvh;
  height: auto;

  /* Default background - teal gradient */
  background: linear-gradient(135deg, #033d4a 0%, #0791b0 100%);
  background-size: cover;
  background-position: center;
  color: #ffffff;
  padding: 0;
}

/* GSAP mengatur transform pada .hero-video — jangan tambahkan transform/scale di CSS */
.hero-video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  min-width: 100%;
  height: 150%;
  object-fit: cover;
  object-position: center;
  z-index: 0;
  opacity: 0;
  transition: opacity 0.5s ease;
}

.hero-video.video-loaded {
  opacity: 1;
}

.hero-video-overlay {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
  pointer-events: none;
}

.hero-overlay {
  content: '';
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  /* Keep media colors visible; only soft contrast layer */
  background: linear-gradient(
    to right,
    rgba(0, 0, 0, 0.18) 0%,
    rgba(0, 0, 0, 0.08) 50%,
    rgba(0, 0, 0, 0.02) 100%
  );
  z-index: 2;
}

.hero-content {
  position: relative;
  z-index: 3;
  padding-bottom: calc(1rem + env(safe-area-inset-bottom, 0px));
}

.hero-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: var(--type-hero-display);
  font-weight: 400;
  line-height: 1.08;
  letter-spacing: -0.01em;
  text-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
  white-space: pre-wrap;
  word-spacing: 0.1em;
}

.hero-title .text-italic {
  font-style: italic;
}

.hero-title .text-normal {
  font-style: normal;
}

.hero-subtitle {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: var(--type-hero-sub);
  font-weight: 400;
  line-height: 1.65;
  max-width: var(--prose-max-width);
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
  opacity: 0.95;
}

@media (max-width: 768px) {
  .hero-text-container {
    margin-top: clamp(2.5rem, 8vw, 3.75rem);
  }

  .hero-button {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem !important;
  }

  .hero-button :deep(.action-buttons-container) {
    width: 100%;
    max-width: 22rem;
  }
}

@media (max-width: 576px) {
  .hero-text-container {
    margin-top: clamp(2rem, 7vw, 3rem);
  }
}
</style>
