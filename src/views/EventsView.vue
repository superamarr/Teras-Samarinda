<script setup>
import { ref } from 'vue'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import EventCard from '@/components/ui/EventCard.vue'

// Import images from assets
import heroImg from '@/assets/images/generation_8510.jfif'
import imgEvent1 from '@/assets/images/image (3).jpg'
import imgEvent2 from '@/assets/images/image (4).jpg'
import imgEvent3 from '@/assets/images/image (5).jpg'

const events = ref([
  {
    id: 1,
    date: '20 Des 2024',
    title: 'Tari Budaya Kalimantan Timur',
    description: 'Dukung penampilan tari budaya Kalimantan Timur di area Teras Samarinda.',
    image: imgEvent1,
    link: '/events/1',
  },
  {
    id: 2,
    date: '21 Des 2024',
    title: 'Festival Musik Tepi Mahakam',
    description:
      'Nikmati alunan musik dari musisi lokal sambil menikmati senja di Teras Samarinda.',
    image: imgEvent2,
    link: '/events/2',
  },
  {
    id: 3,
    date: '22 Des 2024',
    title: 'Pameran UMKM Samarinda',
    description: 'Temukan produk-produk unggulan karya anak bangsa di pameran UMKM.',
    image: imgEvent3,
    link: '/events/3',
  },
  {
    id: 4,
    date: '23 Des 2024',
    title: 'Workshop Kerajinan Tangan',
    description: 'Belajar membuat kerajinan khas lokal bersama para ahli di Teras Samarinda.',
    image: imgEvent1,
    link: '/events/4',
  },
  {
    id: 5,
    date: '24 Des 2024',
    title: 'Nonton Bareng Film Lokal',
    description: 'Saksikan karya-karya sineas lokal di area terbuka Teras Samarinda.',
    image: imgEvent2,
    link: '/events/5',
  },
  {
    id: 6,
    date: '25 Des 2024',
    title: 'Car Free Day Special',
    description: 'Olahraga pagi dengan suasana segar di sepanjang Teras Samarinda.',
    image: imgEvent3,
    link: '/events/6',
  },
])
</script>

<template>
  <div class="events-page">
    <Navbar initial-light />

    <!-- Events Hero Section -->
    <section class="events-hero" :style="{ backgroundImage: `url(${heroImg})` }">
      <div class="hero-overlay"></div>
      <div
        class="container h-100 d-flex flex-column justify-content-center align-items-center text-center"
      >
        <h1 class="hero-title mb-4">
          Kegiatan & Event <br />
          <span class="text-italic">Teras Samarinda</span>
        </h1>
        <p class="hero-subtitle">
          Teras Samarinda adalah wajah baru ruang publik Kota Samarinda yang dirancang sebagai
          kawasan waterfront modern di tepian Sungai Mahakam.
        </p>
      </div>
    </section>

    <!-- Events Grid Section -->
    <section class="events-content py-5">
      <div class="container py-lg-5">
        <div class="row g-4">
          <div v-for="event in events" :key="event.id" class="col-lg-4 col-md-6">
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

    <Footer />
  </div>
</template>

<style scoped>
.events-page {
  background-color: #ffffff;
}

/* Hero Section Styles */
.events-hero {
  position: relative;
  height: 600px;
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  color: #ffffff;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(3, 61, 74, 0.75);
}

.hero-title {
  font-family: 'Instrument Serif', serif;
  font-size: clamp(3.5rem, 7vw, 5rem);
  font-weight: 400;
  line-height: 1;
  position: relative;
  z-index: 2;
}

.hero-title .text-italic {
  font-style: italic;
}

.hero-subtitle {
  font-family: 'Inter', sans-serif;
  font-size: 1.2rem;
  max-width: 700px;
  opacity: 0.9;
  position: relative;
  z-index: 2;
  line-height: 1.6;
}

.events-content {
  background-color: #ffffff;
}

@media (max-width: 768px) {
  .events-hero {
    height: 500px;
  }
}
</style>
