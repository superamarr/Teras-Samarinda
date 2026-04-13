<script setup>
import { ref, onMounted } from 'vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import ActionButton from '@/components/ui/ActionButton.vue'
import { aboutService } from '@/api/about'
import { resolveMediaUrl } from '@/utils/media'

gsap.registerPlugin(ScrollTrigger)

defineProps({
  id: String,
})

const aboutData = ref({
  title: 'Tentang Teras',
  subtitle: 'Samarinda',
  content:
    'Teras Samarinda adalah wajah baru ruang publik Kota Samarinda yang dirancang sebagai kawasan waterfront modern di tepian Sungai Mahakam.',
  image_left: null,
  image_right: null,
  button_text: 'BACA SELENGKAPNYA',
  button_link: '/tentang',
  layout_type: 'default',
  title_italic: [],
})

const isLoading = ref(true)

const fetchAboutData = async () => {
  try {
    const response = await aboutService.get()
    if (response.data.success && response.data.data) {
      aboutData.value = { ...aboutData.value, ...response.data.data }
    }
  } catch (error) {
    console.error('Failed to fetch about data:', error)
  } finally {
    isLoading.value = false
  }
}

const getImageUrl = (path, side) => {
  if (path) return resolveMediaUrl(path)
  // Fallbacks
  return side === 'left' ? '/images/header-ex.png' : '/images/about-sec.jpg'
}

const getWords = (text) => {
  if (!text) return []
  return text.split(/\s+/).filter((w) => w.length > 0)
}

const isItalic = (word) => {
  const italicWords = aboutData.value.title_italic
  if (!Array.isArray(italicWords)) return false
  return italicWords.includes(word)
}

onMounted(async () => {
  await fetchAboutData()

  gsap.fromTo(
    '.about-title',
    { y: 80 },
    {
      y: -80,
      ease: 'none',
      scrollTrigger: {
        trigger: '.about-section',
        start: 'top bottom',
        end: 'bottom top',
        scrub: true,
      },
    },
  )

  gsap.fromTo(
    '.about-description',
    { y: 50 },
    {
      y: -50,
      ease: 'none',
      scrollTrigger: {
        trigger: '.about-section',
        start: 'top bottom',
        end: 'bottom top',
        scrub: true,
      },
    },
  )

  gsap.fromTo(
    '.about-btn-wrapper',
    { y: 30 },
    {
      y: -30,
      ease: 'none',
      scrollTrigger: {
        trigger: '.about-section',
        start: 'top bottom',
        end: 'bottom top',
        scrub: true,
      },
    },
  )

  gsap.fromTo(
    '.parallax-img-sm',
    { yPercent: 0 },
    {
      yPercent: -33.3,
      ease: 'none',
      scrollTrigger: {
        trigger: '.img-wrapper-sm',
        start: 'top bottom',
        end: 'bottom top',
        scrub: true,
      },
    },
  )

  gsap.fromTo(
    '.parallax-img-lg',
    { yPercent: 0 },
    {
      yPercent: -33.3,
      ease: 'none',
      scrollTrigger: {
        trigger: '.img-wrapper-lg',
        start: 'top bottom',
        end: 'bottom top',
        scrub: true,
      },
    },
  )
})
</script>

<template>
  <section :id="id" class="about-section page-section-pad section-stack-over entrance-section">
    <div class="container-fluid px-3 px-md-4 px-lg-5">
      <div
        class="row gx-lg-5 align-items-stretch"
        :class="{ 'flex-row-reverse': aboutData.layout_type === 'reversed' }"
      >
        <!-- Kolom Kiri (Text & Bottom Image) -->
        <div class="col-lg-6 d-flex flex-column mb-5 mb-lg-0">
          <div class="about-text-content mb-4 mb-lg-5 pe-lg-4">
            <h2 class="about-title mb-3 mb-md-4" v-entrance="{ x: -60, blur: 10 }">
              <template v-for="(word, index) in getWords(aboutData.title)" :key="'title-' + index">
                <span :class="{ 'text-italic': isItalic(word) }">{{ word }}</span
                >{{ index < getWords(aboutData.title).length - 1 ? ' ' : '' }}
              </template>
              &nbsp;
              <span class="text-italic">{{ aboutData.subtitle }}</span>
            </h2>
            <p class="about-description mb-4" v-entrance="{ x: -40, blur: 10, delay: 200 }">
              {{ aboutData.content }}
            </p>

            <div class="about-btn-wrapper" v-entrance="{ x: -30, blur: 10, delay: 400 }">
              <ActionButton
                :text="aboutData.button_text"
                variant="dark"
                gap="0px"
                :href="aboutData.button_link"
              />
            </div>
          </div>

          <!-- Gambar Bawah Kiri/Kanan (Small) -->
          <div class="img-wrapper-sm mt-auto">
            <img
              :src="getImageUrl(aboutData.image_left, 'left')"
              alt="Teras Samarinda Left"
              class="parallax-img-sm"
            />
          </div>
        </div>

        <!-- Kolom Kanan (Large Image) -->
        <div class="col-lg-6">
          <div class="img-wrapper-lg h-100">
            <img
              :src="getImageUrl(aboutData.image_right, 'right')"
              alt="Teras Samarinda Right"
              class="parallax-img-lg"
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.about-section {
  background-color: #fafafa;
  color: #1a1a1a;
}

.about-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: var(--type-heading-lg);
  font-weight: 400;
  line-height: 1.1;
  letter-spacing: -0.02em;
  color: #000000;
  will-change: transform;
}

.about-title .text-italic {
  font-style: italic;
}

.about-title .text-normal {
  font-style: normal;
}

.about-description {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: var(--type-body-relaxed);
  font-weight: 500;
  line-height: 1.55;
  color: #333333;
  max-width: var(--prose-max-width);
  will-change: transform;
}

.about-btn-wrapper {
  will-change: transform;
}

.img-wrapper-sm,
.img-wrapper-lg {
  position: relative;
  overflow: hidden;
  border-radius: 0.25rem;
  width: 100%;
}

.img-wrapper-sm {
  aspect-ratio: 16 / 9;
  height: auto;
}

.img-wrapper-lg {
  min-height: 400px;
  height: 100%;
}

@media (min-width: 992px) {
  .img-wrapper-sm {
    height: 300px;
    aspect-ratio: auto;
  }
}

.parallax-img-sm,
.parallax-img-lg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 150%; /* Gives room for scroll translation */
  object-fit: cover;
  will-change: transform;
}
</style>
