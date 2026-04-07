<script setup>
import { ref } from 'vue'
import ActionButton from '@/components/ui/ActionButton.vue'
import EventCard from '@/components/ui/EventCard.vue'

defineProps({
  id: String,
})

// Import images from assets
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
])
</script>

<template>
  <section :id="id" class="events-section py-5">
    <div class="container py-lg-5">
      <!-- Header Row -->
      <div class="row align-items-center mb-5 gy-4">
        <!-- Left: Action Button (vertically centered between H2 and P) -->
        <div class="col-lg-6 d-flex align-items-center">
          <ActionButton text="LIHAT EVENT" variant="dark" gap="0px" href="/events" />
        </div>

        <!-- Right: H2 Title + Paragraph -->
        <div class="col-lg-6">
          <h2 class="events-title">Kegiatan & Event <span class="text-italic">Terbaru</span></h2>
          <p class="events-subtitle">
            Beragam kegiatan menarik sering diadakan di Teras Samarinda, mulai<br />
            dari event komunitas hingga hiburan publik.
          </p>
        </div>
      </div>

      <!-- Events Grid -->
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
</template>

<style scoped>
.events-section {
  background-color: #f7f7f7;
  position: relative;
  z-index: 10;
}

.events-title {
  font-family: 'Instrument Serif', serif;
  font-size: clamp(3.5rem, 6vw, 4.5rem);
  font-weight: 400;
  line-height: 1;
  color: #1a1a1a;
  white-space: nowrap;
  margin-bottom: 1rem;
}

.events-title .text-italic {
  font-style: italic;
}

.events-subtitle {
  font-family: 'Inter', sans-serif;
  font-size: 1.15rem;
  line-height: 1.6;
  color: #333333;
  max-width: 650px;
  margin-left: auto;
  text-align: right;
}

@media (max-width: 991px) {
  .events-subtitle {
    max-width: 100%;
    text-align: left;
    margin-left: 0;
  }

  .events-title {
    white-space: normal;
  }
}
</style>
