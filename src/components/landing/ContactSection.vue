<script setup>
import { ref, onMounted } from 'vue'
import ContactInfoItem from '@/components/ui/ContactInfoItem.vue'
import ActionButton from '@/components/ui/ActionButton.vue'
import { contactService } from '@/api/contact'

defineProps({
  id: String,
})

const contactData = ref({
  title: 'Kunjungi Kami',
  description:
    'Berlokasi strategis di pusat kota Samarinda, Teras Samarinda sangat mudah diakses dengan kendaraan pribadi maupun transportasi umum.',
  address: 'Jl. Gajah Mada, Bugis, Kec. Samarinda Kota, kota Samarinda, Kalimantan Timur 75121',
  operating_hours: 'Senin - Minggu: Terbuka 24 Jam',
  map_embed: '',
  cta_text: 'BOOKING EVENT',
  cta_link: 'https://maps.app.goo.gl/96e5ea78007505d1',
  title_italic: [],
})

const isLoading = ref(true)

const fetchContactData = async () => {
  try {
    const response = await contactService.get()
    if (response.data.success && response.data.data) {
      const data = response.data.data
      contactData.value = {
        ...contactData.value,
        title: data.title || contactData.value.title,
        description: data.description || contactData.value.description,
        address: data.address || contactData.value.address,
        operating_hours: data.operatingHours || contactData.value.operating_hours,
        map_embed: data.mapEmbed || contactData.value.map_embed,
        cta_text: data.cta_text || contactData.value.cta_text,
        cta_link: data.cta_link || contactData.value.cta_link,
        title_italic: Array.isArray(data.title_italic) ? data.title_italic : []
      }
    }
  } catch (error) {
    console.error('Failed to fetch contact data:', error)
  } finally {
    isLoading.value = false
  }
}

const locationIcon = `
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
    <circle cx="12" cy="10" r="3"></circle>
  </svg>
`

const clockIcon = `
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="12" cy="12" r="10"></circle>
    <polyline points="12 6 12 12 16 14"></polyline>
  </svg>
`

const getWords = (text) => {
  if (!text) return []
  return text.split(/\s+/).filter((w) => w.length > 0)
}

const isItalic = (word) => {
  const italicWords = contactData.value.title_italic
  if (!Array.isArray(italicWords)) return false
  return italicWords.includes(word)
}

onMounted(async () => {
  await fetchContactData()
})
</script>

<template>
  <section :id="id" class="contact-section page-section-pad section-stack-over entrance-section">
    <div class="container-fluid px-3 px-md-4 px-lg-5">
      <div class="row align-items-center gy-5">
        <!-- Content Column -->
        <div class="col-lg-6">
          <div class="pe-lg-5">
            <h2 class="contact-title mb-3 mb-md-4" v-entrance="{ x: -60, blur: 10 }">
              <template v-for="(word, index) in getWords(contactData.title)" :key="'title-' + index">
                <span :class="{ 'text-italic': isItalic(word) }">{{ word }}</span
                >{{ index < getWords(contactData.title).length - 1 ? ' ' : '' }}
              </template>
            </h2>
            <p class="contact-description mb-4" v-entrance="{ x: -40, blur: 10, delay: 200 }">
              {{ contactData.description }}
            </p>

            <div class="contact-info-list mb-4" v-entrance="{ x: -40, blur: 10, delay: 400 }">
              <ContactInfoItem :icon="locationIcon" label="Alamat" :value="contactData.address" />
              <ContactInfoItem
                :icon="clockIcon"
                label="Jam Operasional"
                :value="contactData.operating_hours"
              />
            </div>

            <div v-entrance="{ x: -30, blur: 10, delay: 600 }">
              <ActionButton
                :text="contactData.cta_text"
                variant="dark"
                gap="0px"
                :href="contactData.cta_link"
              />
            </div>
          </div>
        </div>

        <!-- Map Column -->
        <div class="col-lg-6">
          <div
            class="map-container shadow-lg rounded-4 overflow-hidden border"
            v-entrance="{ x: 60, blur: 10, delay: 300 }"
          >
            <div
              v-if="contactData.map_embed"
              v-html="contactData.map_embed"
              class="map-embed-wrapper h-100"
            ></div>
            <div v-else class="h-100 d-flex align-items-center justify-content-center bg-light">
              <div class="text-center text-secondary opacity-50">
                <i class="bi bi-map fs-1"></i>
                <p class="small">Peta belum tersedia</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.contact-section {
  background-color: var(--color-bg-light);
}

.contact-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: var(--type-heading-lg);
  font-weight: 400;
  line-height: 1.1;
  letter-spacing: -0.02em;
  color: var(--color-primary);
}

.text-serif {
  font-family: var(--font-family-serif);
}

.text-italic {
  font-style: italic;
}

.contact-description {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: var(--type-body-relaxed);
  font-weight: 500;
  line-height: 1.55;
  color: var(--color-secondary);
  max-width: var(--prose-max-width);
}

.map-container {
  min-height: 450px;
  background-color: #fafafa;
  position: relative;
}

.map-embed-wrapper :deep(iframe) {
  width: 100% !important;
  height: 450px !important;
  border: 0;
  display: block;
}

@media (max-width: 991.98px) {
}

@media (max-width: 767.98px) {
  .map-container {
    min-height: auto;
    aspect-ratio: 4 / 3;
  }

  .map-embed-wrapper :deep(iframe) {
    height: 100% !important;
  }
}
</style>
