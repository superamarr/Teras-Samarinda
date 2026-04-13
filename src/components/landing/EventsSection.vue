<script setup>
import { ref, computed, onMounted } from 'vue'
import ActionButton from '@/components/ui/ActionButton.vue'
import EventCard from '@/components/ui/EventCard.vue'
import { eventService } from '@/api/events'
import { resolveMediaUrl } from '@/utils/media'

defineProps({
  id: String,
})

const events = ref([])
const sectionSettings = ref({
  section_title: 'Kegiatan & Event Terbaru',
  section_subtitle: 'Beragam kegiatan menarik sering diadakan di Teras Samarinda.',
  cta_text: 'LIHAT EVENT',
  cta_link: '/events',
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

const fetchEvents = async () => {
  try {
    const [eventsRes, settingsRes] = await Promise.all([
      eventService.getAll({ featured: 'true' }),
      eventService.getSettings(),
    ])

    if (eventsRes.data.success) {
      events.value = eventsRes.data.data || []
    }

    if (settingsRes.data.success && settingsRes.data.data) {
      sectionSettings.value = { ...sectionSettings.value, ...settingsRes.data.data }
    }
  } catch (error) {
    console.error('Failed to fetch events:', error)
    events.value = []
  } finally {
    isLoading.value = false
  }
}

const formattedEvents = computed(() => {
  return events.value.slice(0, 3).map((event) => ({
    id: event.id,
    date: event.date
      ? new Date(event.date).toLocaleDateString('id-ID', {
          day: '2-digit',
          month: 'short',
          year: 'numeric',
        })
      : '',
    title: event.name,
    description: event.description || '',
    image: resolveMediaUrl(event.image),
    link: `/events/${event.id}`,
  }))
})

onMounted(async () => {
  await fetchEvents()
})
</script>

<template>
  <section :id="id" class="events-section page-section-pad section-stack-over entrance-section">
    <div class="container-fluid px-3 px-md-4 px-lg-5">
      <!-- Header: Asymmetrical layout (Button Left, Title Right) -->
      <div class="row mb-5 align-items-center g-4">
        <!-- CTA Button on Left -->
        <div class="col-12 col-md-4 order-2 order-md-1">
          <div
            v-if="sectionSettings.cta_text"
            class="events-cta"
            v-entrance="{ x: -30, blur: 10, delay: 350 }"
          >
            <ActionButton
              :text="sectionSettings.cta_text"
              variant="dark"
              gap="0px"
              :href="sectionSettings.cta_link"
            />
          </div>
        </div>

        <!-- Title & Subtitle on Right -->
        <div class="col-12 col-md-8 order-1 order-md-2 text-start text-md-end">
          <h2 class="events-title mb-3" v-entrance="{ x: 60, blur: 10 }">
            <template v-for="(word, index) in getWords(sectionSettings.section_title)" :key="'title-' + index">
              <span :class="{ 'text-italic': isItalic(word) }">{{ word }}</span
              >{{ index < getWords(sectionSettings.section_title).length - 1 ? ' ' : '' }}
            </template>
          </h2>
          <div class="d-flex justify-content-md-end">
            <p
              class="events-subtitle mb-0 text-md-end"
              v-entrance="{ x: 40, blur: 10, delay: 200 }"
            >
              {{ sectionSettings.section_subtitle }}
            </p>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="formattedEvents.length === 0" class="text-center py-5">
        <p class="text-secondary">Belum ada event.</p>
      </div>

      <!-- Events Grid -->
      <div v-else class="row g-3 g-lg-4">
        <div v-for="event in formattedEvents" :key="event.id" class="col-lg-4 col-md-6">
          <EventCard
            :image="event.image"
            :date="event.date"
            :title="event.title"
            :description="event.description"
            :link="event.link"
          />
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.events-section {
  background-color: var(--color-bg-light);
}

.events-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: var(--type-heading-lg);
  font-weight: 400;
  line-height: 1.1;
  letter-spacing: -0.02em;
  color: var(--color-primary);
}

.text-normal {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-style: normal;
}

.text-italic {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-weight: 400;
  font-style: italic;
}

.events-subtitle {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: var(--type-body-relaxed);
  font-weight: 500;
  line-height: 1.55;
  color: var(--color-secondary);
  max-width: var(--prose-max-width);
}

@media (max-width: 768px) {
  .events-subtitle {
    max-width: 100%;
  }
}
</style>
