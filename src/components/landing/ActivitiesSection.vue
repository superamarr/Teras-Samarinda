<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import ActivityListItem from '@/components/ui/ActivityListItem.vue'

// Import images
import img1 from '@/assets/images/image (1).jpg'
import img2 from '@/assets/images/image (2).jpg'
import img3 from '@/assets/images/image (3).jpg'
import img4 from '@/assets/images/image (4).jpg'
import img5 from '@/assets/images/image (5).jpg'

defineProps({
  id: String,
})

// Data list aktivitas dengan gambar
const activities = [
  { text: 'Menikmati Sunset & View Sungai', image: img1 },
  { text: 'Hunting Foto Estetik', image: img2 },
  { text: 'Kuliner Malam', image: img3 },
  { text: 'Jalan-jalan Santai', image: img4 },
  { text: 'Menikmati Event', image: img5 },
]

// State untuk menyimpan index list yang sedang aktif
const activeIndex = ref(0)

// State untuk reveal setiap item
const revealedItems = ref([])

// Computed untuk gambar aktif
const currentImage = computed(() => activities[activeIndex.value]?.image || img1)

// Ref untuk setiap item di list
const itemRefs = ref([])
const sectionRef = ref(null)

// Intersection Observer
let observer = null

onMounted(() => {
  // Inisialisasi revealedItems dengan false
  revealedItems.value = activities.map(() => false)

  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        const index = Number(entry.target.dataset.index)
        if (entry.isIntersecting && index >= 0) {
          // Reveal item dengan delay
          setTimeout(() => {
            revealedItems.value[index] = true
            // Set active saat item reveal
            activeIndex.value = index
          }, index * 150)
        }
      })
    },
    {
      threshold: 0.2,
      rootMargin: '0px 0px -50px 0px',
    },
  )

  // Observe setiap item
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

// Set ref untuk item
const setItemRef = (el, index) => {
  if (el) {
    el.dataset.index = index
    itemRefs.value[index] = el
  }
}
</script>

<template>
  <section :id="id" ref="sectionRef" class="activities-section py-5">
    <div class="container py-lg-5">
      <!-- Header Teks Bagian Atas -->
      <div class="row mb-5">
        <div class="col-lg-6">
          <h2 class="section-title mb-4">
            <span class="text-italic">Aktivitas </span>
            <span class="text-normal">Yang Bisa Anda Lakukan</span>
          </h2>
          <p class="section-subtitle">
            Beragam kegiatan menarik sering diadakan di Teras Samarinda, mulai dari event komunitas
            hingga hiburan publik.
          </p>
        </div>
      </div>

      <!-- Konten Gambar Kiri & List Kanan -->
      <div class="row align-items-center gx-lg-5">
        <!-- Kolom Kiri: Gambar -->
        <div class="col-lg-6 mb-4 mb-lg-0">
          <div class="img-wrapper h-100">
            <Transition name="fade" mode="out-in">
              <img
                :key="activeIndex"
                :src="currentImage"
                :alt="activities[activeIndex]?.text"
                class="img-fluid w-100 object-fit-cover shadow-sm"
                style="min-height: 400px; max-height: 400px"
              />
            </Transition>
          </div>
        </div>

        <!-- Kolom Kanan: List Berulang (Modular) -->
        <div class="col-lg-6">
          <div class="activity-list-container d-flex flex-column">
            <div
              v-for="(activity, index) in activities"
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
  position: relative;
  z-index: 10;
}

.section-title {
  font-family: 'Instrument Serif', serif;
  font-size: clamp(2.5rem, 4vw, 3.5rem);
  font-weight: 400;
  line-height: 1.1;
  letter-spacing: -0.01em;
  color: #000000;
  white-space: nowrap;
}

.section-title .text-italic {
  font-style: italic;
}

.section-title .text-normal {
  font-style: normal;
}

.section-subtitle {
  font-family: 'Inter', sans-serif;
  font-size: 1rem;
  font-weight: 500;
  line-height: 1.6;
  color: #333333;
  max-width: 90%;
}

.img-wrapper {
  position: relative;
  overflow: hidden;
  border-radius: 4px;
}

.img-wrapper img {
  border-radius: 4px;
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
</style>
