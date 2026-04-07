<script setup>
import { ref } from 'vue'
import FacilityCard from '@/components/ui/FacilityCard.vue'

// Import images from assets
import imgEventBudaya from '@/assets/images/generation_8491.jfif'
import imgToilet from '@/assets/images/generation_8501.jfif'
import imgPejalanKaki from '@/assets/images/generation_8510.jfif'
import imgPedestrian from '@/assets/images/generation_8515.jfif'
import imgPenerangan from '@/assets/images/image 5.jpg'

// Facilities data
const facilities = [
  {
    id: 1,
    title: 'Event Budaya',
    image: imgEventBudaya,
  },
  {
    id: 2,
    title: 'Toilet Umum',
    image: imgToilet,
  },
  {
    id: 3,
    title: 'Area Pejalan Kaki',
    image: imgPejalanKaki,
  },
  {
    id: 4,
    title: 'Jalur Pedestrian',
    image: imgPedestrian,
  },
  {
    id: 5,
    title: 'Penerangan',
    image: imgPenerangan,
  },
]

// Hover state untuk pause animation
const isPaused = ref(false)
</script>

<template>
  <section class="facilities-section">
    <div class="container">
      <div class="facilities-header">
        <h2 class="facilities-title">Fasilitas Teras <span class="italic">Samarinda</span></h2>
        <p class="facilities-description">
          Nikmati berbagai fasilitas modern yang dirancang untuk memberikan<br />
          pengalaman terbaik selama berada di Teras Samarinda.
        </p>
      </div>

      <!-- Carousel Container -->
      <div
        class="carousel-wrapper"
        :class="{ paused: isPaused }"
        @mouseenter="isPaused = true"
        @mouseleave="isPaused = false"
      >
        <div class="carousel-track">
          <!-- Original Items -->
          <FacilityCard
            v-for="facility in facilities"
            :key="'original-' + facility.id"
            :title="facility.title"
            :image="facility.image"
          />
          <!-- Duplicate Items untuk Seamless Loop -->
          <FacilityCard
            v-for="facility in facilities"
            :key="'duplicate-' + facility.id"
            :title="facility.title"
            :image="facility.image"
          />
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.facilities-section {
  padding: 6rem 0;
  background-color: #f7f7f7;
  position: relative;
  z-index: 10;
  overflow: hidden;
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
  margin-bottom: 4rem;
}

.facilities-title {
  font-family: 'Instrument Serif', serif;
  font-size: 4rem;
  font-weight: 400;
  color: #111;
  line-height: 1.1;
  margin-bottom: 1rem;
}

.facilities-title .italic {
  font-style: italic;
}

.facilities-description {
  font-family: 'Inter', sans-serif;
  font-size: 1.125rem;
  line-height: 1.6;
  color: #333;
  max-width: 600px;
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
  .facilities-title {
    font-size: 3rem;
  }

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
