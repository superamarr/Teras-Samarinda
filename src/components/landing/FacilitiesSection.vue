<script setup>
import { ref, onMounted } from 'vue'
import FacilityCard from '@/components/ui/FacilityCard.vue'
import { facilityService } from '@/api/facilities'
import { resolveMediaUrl } from '@/utils/media'

defineProps({
  id: String,
})

const facilities = ref([])
const sectionSettings = ref({
  section_title: 'Fasilitas Teras Samarinda',
  section_subtitle: 'Nikmati berbagai fasilitas modern yang dirancang untuk memberikan pengalaman terbaik.',
  layout_type: 'default',
  section_title_italic: []
})
const isLoading = ref(true)
const hoverPaused = ref(false)

const fetchFacilities = async () => {
  try {
    const [facilitiesRes, settingsRes] = await Promise.all([
      facilityService.getAll(),
      facilityService.getSettings()
    ])

    if (facilitiesRes.data.success) {
      facilities.value = facilitiesRes.data.data || []
    }

    if (settingsRes.data.success && settingsRes.data.data) {
      sectionSettings.value = { ...sectionSettings.value, ...settingsRes.data.data }
    }
  } catch (error) {
    console.error('Failed to fetch facilities:', error)
    facilities.value = []
  } finally {
    isLoading.value = false
  }
}

const getImageUrl = (path) => {
  if (!path) return ''
  return resolveMediaUrl(path)
}

const getWords = (text) => {
  if (!text) return []
  return text.split(/\s+/).filter((w) => w.length > 0)
}

const isItalic = (word) => {
  const italicWords = sectionSettings.value.section_title_italic
  if (!Array.isArray(italicWords)) return false
  return italicWords.includes(word)
}

onMounted(async () => {
  await fetchFacilities()
})
</script>

<template>
  <section :id="id" class="facilities-section page-section-pad section-stack-over entrance-section">
    <div class="container-fluid px-3 px-md-4 px-lg-5">
      <div class="facilities-header">
        <h2 class="facilities-title" v-entrance="{ x: 60, blur: 10 }">
          <template v-for="(word, index) in getWords(sectionSettings.section_title)" :key="'title-' + index">
            <span :class="{ 'text-italic': isItalic(word) }">{{ word }}</span
            >{{ index < getWords(sectionSettings.section_title).length - 1 ? ' ' : '' }}
          </template>
        </h2>
        <p class="facilities-description" v-entrance="{ x: 40, blur: 10, delay: 200 }">
          {{ sectionSettings.section_subtitle }}
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="facilities.length === 0" class="text-center py-5">
        <p class="text-secondary">Belum ada fasilitas.</p>
      </div>

      <!-- Carousel Container -->
      <div
        v-else
        class="carousel-wrapper"
        :class="{ paused: hoverPaused }"
        @mouseenter="hoverPaused = true"
        @mouseleave="hoverPaused = false"
      >
        <div class="carousel-track">
          <!-- Original Items -->
          <FacilityCard
            v-for="facility in facilities"
            :key="'original-' + facility.id"
            :title="facility.name"
            :image="getImageUrl(facility.image)"
          />
          <!-- Duplicate Items untuk Seamless Loop -->
          <FacilityCard
            v-for="facility in facilities"
            :key="'duplicate-' + facility.id"
            :title="facility.name"
            :image="getImageUrl(facility.image)"
          />
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.facilities-section {
  background-color: #f7f7f7;
}

.container {
  width: 100%;
  max-width: 1320px;
  margin: 0 auto;
  padding: 0 1rem;
}

.facilities-header {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  text-align: right;
  margin-bottom: var(--section-header-gap);
}

.facilities-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: var(--type-heading-lg);
  font-weight: 400;
  color: #111;
  line-height: 1.1;
  letter-spacing: -0.02em;
  margin-bottom: 0.75rem;
}

.facilities-title .text-italic {
  font-style: italic;
}

.facilities-description {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: var(--type-body-relaxed);
  line-height: 1.55;
  color: #333;
  max-width: var(--prose-max-width);
}

/* Carousel Styles */
.carousel-wrapper {
  width: 100%;
  overflow: hidden;
}

.carousel-track {
  display: flex;
  gap: 1.5rem;
  animation: scroll 40s linear infinite;
  width: max-content;
}

.carousel-wrapper.paused .carousel-track {
  animation-play-state: paused;
}

.carousel-track > * {
  flex-shrink: 0;
  width: 300px;
}

@keyframes scroll {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
}

@media (max-width: 768px) {
  .facilities-header {
    align-items: flex-start;
    text-align: left;
  }

  .carousel-track > * {
    width: 280px;
  }
}

@media (max-width: 480px) {
  .carousel-track > * {
    width: 260px;
  }
}
</style>
