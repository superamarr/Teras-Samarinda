<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import ActivityListItem from '@/components/ui/ActivityListItem.vue'
import { activityService } from '@/api/activities'
import { resolveMediaUrl } from '@/utils/media'

defineProps({
  id: String,
})

const activities = ref([])
const DEFAULT_CAPTION =
  'Dari sudut-sudut estetik yang tenang hingga energi pameran UMKM dan festival musik, Teras Samarinda menyajikan sisi terbaik Samarinda dalam satu ruang terpadu.'

const sectionSettings = ref({
  section_title: 'Aktivitas Yang Bisa Anda Lakukan',
  section_subtitle: 'Beragam kegiatan menarik sering diadakan di Teras Samarinda.',
  section_subtitle_extra: DEFAULT_CAPTION,
  layout_type: 'default',
  section_title_italic: [],
})
const isLoading = ref(true)

const activeIndex = ref(0)
const revealedItems = ref([])
const itemRefs = ref([])
const sectionRef = ref(null)
const listContainerRef = ref(null)

const fetchActivities = async () => {
  try {
    const [activitiesRes, settingsRes] = await Promise.all([
      activityService.getAll(),
      activityService.getSettings(),
    ])

    if (activitiesRes.data.success) {
      activities.value = activitiesRes.data.data || []
    }

    if (settingsRes.data.success && settingsRes.data.data) {
      sectionSettings.value = { ...sectionSettings.value, ...settingsRes.data.data }
    }
  } catch (error) {
    console.error('Failed to fetch activities:', error)
    activities.value = []
  } finally {
    isLoading.value = false
  }
}

const activityList = computed(() => {
  return activities.value.map((a) => ({
    text: a.name,
    image: a.image ? resolveMediaUrl(a.image) : '/images/placeholder.jpg',
  }))
})

const getWords = (text) => {
  if (!text) return []
  return text.split(/\s+/).filter((w) => w.length > 0)
}

const isItalic = (word) => {
  const italicWords = sectionSettings.value.section_title_italic
  if (!Array.isArray(italicWords)) return false
  return italicWords.includes(word)
}

const currentImage = computed(
  () => activityList.value[activeIndex.value]?.image || '/images/placeholder.jpg',
)

const footerCaption = computed(() => {
  const raw = sectionSettings.value.section_subtitle_extra
  const t = typeof raw === 'string' ? raw.trim() : ''
  return t || DEFAULT_CAPTION
})

let observer = null

onMounted(async () => {
  await fetchActivities()
  revealedItems.value = activityList.value.map(() => false)

  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        const index = Number(entry.target.dataset.index)
        if (entry.isIntersecting && index >= 0) {
          revealedItems.value[index] = true
          activeIndex.value = index
        }
      })
    },
    {
      root: listContainerRef.value || null,
      threshold: 0.6,
      rootMargin: '-10% 0px -20% 0px',
    },
  )

  itemRefs.value.forEach((el) => {
    if (el) observer.observe(el)
  })
})

onUnmounted(() => {
  if (observer) {
    itemRefs.value.forEach((el) => {
      if (el) observer.unobserve(el)
    })
  }
})

const setItemRef = (el, index) => {
  if (el) {
    el.dataset.index = index
    itemRefs.value[index] = el
  }
}

const handleSectionWheel = (e) => {
  if (!listContainerRef.value) return

  // Hanya hijack di tablet/desktop dimana list ada di samping gambar
  if (window.innerWidth < 992) return

  const list = listContainerRef.value
  const isAtTop = list.scrollTop <= 0
  const isAtBottom = Math.ceil(list.scrollTop + list.clientHeight) >= list.scrollHeight

  // Arah scroll (positif = ke bawah, negatif = ke atas)
  if (e.deltaY > 0 && !isAtBottom) {
    e.preventDefault()
    list.scrollTop += e.deltaY
  } else if (e.deltaY < 0 && !isAtTop) {
    e.preventDefault()
    list.scrollTop += e.deltaY
  }
}
</script>

<template>
  <section
    :id="id"
    ref="sectionRef"
    class="activities-section page-section-pad section-stack-over entrance-section"
    @wheel="handleSectionWheel"
  >
    <div class="container-fluid px-3 px-md-4 px-lg-5">
      <!-- Header Teks Bagian Atas -->
      <div class="row mb-4 mb-lg-5">
        <div class="col-12 col-lg-7 col-xl-6">
          <h2 class="section-title mb-3 mb-md-4" v-entrance="{ x: -60, blur: 10 }">
            <template
              v-for="(word, index) in getWords(sectionSettings.section_title)"
              :key="'title-' + index"
            >
              <span :class="{ 'text-italic': isItalic(word) }">{{ word }}</span
              >{{ index < getWords(sectionSettings.section_title).length - 1 ? ' ' : '' }}
            </template>
          </h2>
          <p class="section-subtitle mb-0" v-entrance="{ x: -40, blur: 10, delay: 200 }">
            {{ sectionSettings.section_subtitle }}
          </p>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="activityList.length === 0" class="text-center py-5">
        <p class="text-secondary">Belum ada aktivitas.</p>
      </div>

      <!-- Konten Gambar Kiri & List Kanan -->
      <div v-else class="row gx-lg-5 gy-4 align-items-stretch">
        <!-- Kolom Kiri: Gambar -->
        <div class="col-lg-6">
          <div class="img-wrapper h-100 position-relative">
            <Transition name="fade" mode="out-in">
              <img
                :key="activeIndex"
                :src="currentImage"
                :alt="activityList[activeIndex]?.text"
                class="activities-preview-img img-fluid w-100 object-fit-cover shadow-sm h-100"
              />
            </Transition>
          </div>
        </div>

        <!-- Kolom Kanan: List Berulang (Modular) -->
        <div class="col-lg-6">
          <div class="activity-scroll-wrapper" ref="listContainerRef">
            <div class="activity-list-container d-flex flex-column py-2">
              <div
                v-for="(activity, index) in activityList"
                :key="index"
                :ref="(el) => setItemRef(el, index)"
              >
                <ActivityListItem
                  :text="activity.text"
                  :is-active="activeIndex === index"
                  :is-revealed="revealedItems[index]"
                  @click="activeIndex = index"
                />
              </div>

              <!-- Teks penutup (dikelola dari dashboard → Section Beranda) -->
              <p class="mt-4 pt-4 mb-4 mb-md-5 caption-text text-secondary text-break">
                {{ footerCaption }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.activities-section {
  background-color: #f7f7f7;
  color: #1a1a1a;
}

.section-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: var(--type-heading-lg);
  font-weight: 400;
  line-height: 1.1;
  letter-spacing: -0.02em;
  color: #000000;
}

.section-title .text-italic {
  font-style: italic;
}

.section-title .text-normal {
  font-style: normal;
}

.section-subtitle {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: var(--type-body-relaxed);
  font-weight: 500;
  line-height: 1.55;
  color: #333333;
  max-width: var(--prose-max-width);
}

.img-wrapper {
  position: relative;
  overflow: hidden;
  border-radius: 4px;
}

.img-wrapper img {
  border-radius: 4px;
}

/* Tinggi gambar mengikuti layar, tidak memaksa 400px di ponsel */
.activities-preview-img {
  display: block;
  aspect-ratio: 4 / 3;
  min-height: 200px;
  max-height: min(420px, 48vh);
  object-fit: cover;
}

@media (min-width: 992px) {
  .activities-preview-img {
    aspect-ratio: 16 / 11;
    min-height: 320px;
    max-height: min(440px, 55vh);
  }
}

/* Transition untuk gambar */
.fade-enter-active,
.fade-leave-active {
  transition:
    opacity 0.4s ease,
    transform 0.4s ease;
}

.fade-enter-from {
  opacity: 0;
  transform: scale(1.05);
}

.fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.activity-scroll-wrapper {
  overflow-y: auto;
  scrollbar-width: none;
  mask-image: linear-gradient(to bottom, black 75%, transparent 100%);
  -webkit-mask-image: linear-gradient(to bottom, black 75%, transparent 100%);
  padding-right: 1.5rem;
  /* Matching image heights */
  min-height: 200px;
  max-height: min(420px, 48vh);
}

.activity-scroll-wrapper::-webkit-scrollbar {
  display: none;
}

@media (min-width: 992px) {
  .activity-scroll-wrapper {
    min-height: 320px;
    max-height: min(440px, 55vh);
  }
}

.caption-text {
  font-family: var(--font-family-sans), 'Inter', sans-serif;
  font-size: clamp(0.9375rem, 2.8vw, 1rem);
  line-height: 1.6;
  max-width: 100%;
}

@media (min-width: 992px) {
  .caption-text {
    max-width: min(42rem, 100%);
  }
}
</style>
