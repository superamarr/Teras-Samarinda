<script setup>
import { ref, onMounted, computed } from 'vue'
import ActionButton from '@/components/ui/ActionButton.vue'
import GalleryEmblaCarousel from '@/components/landing/GalleryEmblaCarousel.vue'
import { galleryService } from '@/api/gallery'
import { resolveMediaUrl } from '@/utils/media'

defineProps({
  id: String,
})

const galleryImages = ref([])
const sectionSettings = ref({
  section_title: 'Gallery Teras Samarinda',
  section_subtitle: 'Intip keceriaan pengunjung dan keindahan arsitektur Teras Samarinda',
  cta_text: 'LIHAT SELENGKAPNYA',
  cta_link: '/galeri',
  layout_type: 'default',
  section_title_italic: []
})
const isLoading = ref(true)

const getWords = (text) => {
  if (!text) return []
  return text.split(/\s+/).filter((w) => w.length > 0)
}

const isItalic = (word) => {
  const italicWords = sectionSettings.value.section_title_italic
  if (!Array.isArray(italicWords)) return false
  return italicWords.includes(word)
}

const fetchGallery = async () => {
  try {
    const [imagesRes, settingsRes] = await Promise.all([
      galleryService.getAll(),
      galleryService.getSettings(),
    ])

    if (imagesRes.data.success) {
      galleryImages.value = (imagesRes.data.data || []).map((img) => ({
        id: img.id,
        src: resolveMediaUrl(img.url || img.image),
        alt: img.title || 'Gallery Image',
      }))
    }

    if (settingsRes?.data?.success && settingsRes.data.data) {
      const d = settingsRes.data.data
      const prev = sectionSettings.value
      sectionSettings.value = {
        ...prev,
        ...d,
        section_title: (d.section_title && String(d.section_title).trim())
          ? d.section_title
          : prev.section_title,
        section_subtitle: (d.section_subtitle && String(d.section_subtitle).trim())
          ? d.section_subtitle
          : prev.section_subtitle,
        cta_text: (d.cta_text != null && String(d.cta_text).trim())
          ? d.cta_text
          : prev.cta_text,
        cta_link: (d.cta_link != null && String(d.cta_link).trim())
          ? d.cta_link
          : prev.cta_link,
        layout_type: d.layout_type || prev.layout_type,
        section_title_italic: Array.isArray(d.section_title_italic) ? d.section_title_italic : []
      }
    }
  } catch (error) {
    console.error('Failed to fetch gallery:', error)
    galleryImages.value = []
  } finally {
    isLoading.value = false
  }
}

onMounted(async () => {
  await fetchGallery()
})
</script>

<template>
  <section :id="id" class="gallery-section page-section-pad section-stack-over entrance-section">
    <div class="container-fluid px-3 px-md-4 px-lg-5">
      <header class="gallery-header">
        <div class="gallery-header__text">
          <h2 class="gallery-title mb-3 mb-md-4" v-entrance="{ x: -60, blur: 10 }">
            <template v-for="(word, index) in getWords(sectionSettings.section_title)" :key="'title-' + index">
              <span :class="{ 'text-italic': isItalic(word) }">{{ word }}</span
              >{{ index < getWords(sectionSettings.section_title).length - 1 ? ' ' : '' }}
            </template>
          </h2>
          <p class="gallery-subtitle mb-0" v-entrance="{ x: -40, blur: 10, delay: 200 }">
            {{ sectionSettings.section_subtitle }}
          </p>
        </div>
        <div
          v-if="sectionSettings.cta_text"
          class="gallery-header__cta"
          v-entrance="{ x: 28, blur: 10, delay: 280 }"
        >
          <ActionButton
            :text="sectionSettings.cta_text"
            variant="dark"
            gap="0px"
            :href="sectionSettings.cta_link"
          />
        </div>
      </header>

      <div v-if="isLoading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <div v-else-if="galleryImages.length === 0" class="text-center py-5">
        <p class="text-secondary">Belum ada foto di galeri.</p>
      </div>

      <GalleryEmblaCarousel
        v-else
        :slides="galleryImages"
      />
    </div>
  </section>
</template>

<style scoped>
.gallery-section {
  background-color: #f4f7f8;
  position: relative;
}

.gallery-header {
  display: flex;
  flex-wrap: wrap;
  align-items: center; /* Set to center to vertically align button in middle of p and h */
  justify-content: space-between;
  column-gap: clamp(1.25rem, 4vw, 2.5rem);
  row-gap: clamp(1rem, 2.5vw, 1.5rem);
  margin-bottom: 2rem;
}

@media (min-width: 992px) {
  .gallery-header {
    margin-bottom: 3rem;
  }
}

.gallery-header__text {
  flex: 1 1 0;
  min-width: min(100%, 20rem);
  max-width: min(100%, var(--prose-max-width));
}

@media (min-width: 992px) {
  .gallery-header__text {
    max-width: min(100%, 36rem);
  }
}

.gallery-header__cta {
  flex: 0 0 auto;
  display: flex;
  justify-content: flex-start;
  align-items: center;
  min-height: 3rem;
}

@media (min-width: 992px) {
  .gallery-header__cta {
    justify-content: flex-end;
  }
}

.gallery-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: var(--type-heading-lg);
  font-weight: 400;
  line-height: 1.1;
  letter-spacing: -0.02em;
  color: #000000;
}

.text-normal {
  font-style: normal;
}

.text-italic {
  font-style: italic;
}

.gallery-subtitle {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: var(--type-body-relaxed);
  font-weight: 500;
  line-height: 1.55;
  color: #4a5560;
  max-width: var(--prose-max-width);
}

@media (max-width: 575.98px) {
  .gallery-title__script {
    display: block;
    margin-left: 0;
    margin-top: 0.15rem;
    line-height: 1;
  }
}
</style>
