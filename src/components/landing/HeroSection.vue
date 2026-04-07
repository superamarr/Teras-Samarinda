<script setup>
import ActionButton from '@/components/ui/ActionButton.vue'

defineProps({
  id: String,
})
</script>

<template>
  <div :id="id" class="hero-section d-flex align-items-center">
    <!-- Video Background -->
    <video class="hero-video" autoplay loop muted playsinline>
      <source src="@/assets/videos/pbw (1).webm" type="video/webm" />
    </video>
    <!-- Overlay for darkening the background -->
    <div class="hero-overlay"></div>

    <div class="container hero-content">
      <div class="row">
        <!-- Tambahkan class hero-text-container di sini untuk mengatur posisi atas-bawah -->
        <div class="col-lg-8 col-md-10 hero-text-container">
          <h1
            v-motion
            :initial="{ opacity: 0, y: 30 }"
            :enter="{ opacity: 1, y: 0 }"
            :duration="800"
            :delay="300"
            class="hero-title mb-4"
          >
            <span class="text-italic">Jelajahi</span> <span class="text-normal">Ikon Baru</span>
            <br />
            <span class="text-normal">Kota</span> <span class="text-italic">Samarinda</span>
          </h1>
          <p
            v-motion
            :initial="{ opacity: 0, y: 30 }"
            :enter="{ opacity: 1, y: 0 }"
            :duration="800"
            :delay="500"
            class="hero-subtitle mb-5"
          >
            Teras Samarinda menghadirkan ruang publik modern di tepian Sungai Mahakam, tempat
            terbaik untuk bersantai, beraktivitas, dan menikmati keindahan kota.
          </p>

          <!-- Gunakan ActionButton Component Reusable -->
          <div
            v-motion
            :initial="{ opacity: 0, y: 30 }"
            :enter="{ opacity: 1, y: 0 }"
            :duration="800"
            :delay="700"
          >
            <ActionButton text="LIHAT EVENT" variant="light" gap="px" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* ==============================================================
   PENGATURAN POSISI & JARAK (UBAH NILAINYA DI SINI)
   ============================================================== */

/* 1. Atur Posisi / Ketinggian Seluruh Teks & Tombol */
.hero-text-container {
  margin-top: 80px;
}

/* ============================================================== */

.hero-section {
  position: sticky;
  top: 0;
  z-index: 0;
  min-height: 100vh;
  width: 100%;
  background-color: #000;
  color: #ffffff;
  padding-top: 80px;
  padding-bottom: 80px;
  overflow: hidden;
}

.hero-video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: 0;
}

.hero-overlay {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  /* Gradient matching the design: darker on the left/bottom, fading out to top right */
  background: linear-gradient(
    to right,
    rgba(0, 0, 0, 0.75) 0%,
    rgba(0, 0, 0, 0.4) 50%,
    rgba(0, 0, 0, 0.1) 100%
  );
  z-index: 1;
}

.hero-content {
  position: relative;
  z-index: 2;
}

.hero-title {
  font-family: 'Instrument Serif', serif;
  font-size: clamp(4rem, 7vw, 5rem);
  font-weight: 400;
  line-height: 1.1;
  letter-spacing: -0.01em;
  text-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
}

.hero-title .text-italic {
  font-style: italic;
}

.hero-title .text-normal {
  font-style: normal;
}

.hero-subtitle {
  font-family: 'Inter', sans-serif;
  font-size: clamp(1rem, 1.25vw, 1.15rem);
  font-weight: 400;
  line-height: 1.7;
  max-width: 600px;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
  opacity: 0.95;
}
</style>
