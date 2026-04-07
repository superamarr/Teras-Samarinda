<script setup>
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import ActionButton from '@/components/ui/ActionButton.vue'

// Import images from assets
import heroImg from '@/assets/images/generation_8510.jfif'
</script>

<template>
  <div class="event-detail-page">
    <Navbar initial-light />

    <!-- Event Detail Hero -->
    <section class="event-hero" :style="{ backgroundImage: `url(${heroImg})` }">
      <div class="hero-overlay"></div>
      <div class="container h-100 d-flex flex-column justify-content-center">
        <div class="hero-content text-white text-center position-relative z-index-2">
          <div class="event-status d-flex align-items-center gap-2 mb-4 mx-auto">
            <span class="status-dot"></span>
            <span class="status-text fw-bold">EVENT AKTIF</span>
          </div>

          <h1 class="event-title mb-4">
            Festival Musik Tepi <span class="text-italic">Mahakam</span>
          </h1>

          <div class="event-meta d-flex flex-wrap gap-4 justify-content-center">
            <div class="meta-item d-flex align-items-center gap-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                fill="currentColor"
                viewBox="0 0 16 16"
              >
                <path
                  d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"
                />
              </svg>
              <span>12 Agustus 2025</span>
            </div>
            <div class="meta-item d-flex align-items-center gap-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                fill="currentColor"
                viewBox="0 0 16 16"
              >
                <path
                  d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"
                />
              </svg>
              <span>Teras Samarinda</span>
            </div>
          </div>

          <p class="hero-description mt-4 mx-auto">
            Nikmati keseruan di tepian Mahakam dengan suasana<br />
            yang hangat dan penuh energi
          </p>
        </div>
      </div>
    </section>

    <!-- Event Content Section -->
    <section class="event-content py-5">
      <div class="container py-lg-5">
        <div class="row justify-content-center">
          <div class="col-lg-10">
            <h2 class="content-title mb-4">Tentang <span class="text-italic">Event</span></h2>
            <div class="content-text mb-5">
              <p>
                UMKM Creative Fair merupakan ajang pameran yang menghadirkan berbagai produk kreatif
                dari pelaku usaha lokal di Kota Samarinda. Event ini menjadi wadah bagi para pelaku
                UMKM untuk memperkenalkan produk unggulan mereka kepada masyarakat luas.
              </p>
              <p>
                Tidak hanya itu, pengunjung juga dapat menikmati suasana yang hangat dan interaktif
                dengan berbagai aktivitas menarik seperti demo produk, pertunjukan hiburan, dan
                kuliner khas daerah.
              </p>
            </div>

            <!-- Booking Box -->
            <div
              class="booking-box p-4 p-lg-5 rounded-4 d-flex flex-column flex-md-row align-items-center justify-content-between gap-4"
            >
              <div class="booking-content text-white text-md-start text-center">
                <h3 class="booking-title mb-3">Wujudkan Event Anda di Teras Samarinda</h3>
                <p class="booking-subtitle mb-0">
                  Jadikan Teras Samarinda sebagai tempat terbaik untuk menggelar acara Anda. Mulai
                  dari pertunjukan seni, event komunitas, hingga hiburan publik — hadirkan
                  pengalaman tak terlupakan di ruang terbuka yang ikonik.
                </p>
              </div>
              <div class="booking-action flex-shrink-0">
                <ActionButton text="BOOKING SEKARANG" variant="light" gap="0px" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <Footer />
  </div>
</template>

<style scoped>
.event-detail-page {
  background-color: #ffffff;
}

/* Hero Section Styles */
.event-hero {
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
  background-color: rgba(3, 61, 74, 0.7);
}

.event-status {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(4px);
  padding: 4px 12px;
  border-radius: 100px;
  width: fit-content;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.status-dot {
  width: 8px;
  height: 8px;
  background-color: #3fe082;
  border-radius: 50%;
  box-shadow: 0 0 8px #3fe082;
}

.status-text {
  font-size: 0.8rem;
  letter-spacing: 1px;
}

.event-title {
  font-family: 'Instrument Serif', serif;
  font-size: clamp(3rem, 6vw, 4.5rem);
  font-weight: 400;
  line-height: 1;
}

.event-title .text-italic {
  font-style: italic;
}

.meta-item {
  font-family: 'Inter', sans-serif;
  font-size: 1.1rem;
  font-weight: 500;
}

.hero-description {
  font-family: 'Inter', sans-serif;
  font-size: 1.2rem;
  line-height: 1.5;
  opacity: 0.9;
  max-width: 700px;
}

.content-title {
  font-family: 'Instrument Serif', serif;
  font-size: 3.5rem;
  font-weight: 400;
  color: #1a1a1a;
}

.content-title .text-italic {
  font-style: italic;
}

.content-text p {
  font-family: 'Inter', sans-serif;
  font-size: 1.15rem;
  line-height: 1.8;
  color: #333;
  margin-bottom: 2rem;
}

/* Booking Box Styling */
.booking-box {
  background-color: #033d4a;
  position: relative;
  overflow: hidden;
}

.booking-title {
  font-family: 'Instrument Serif', serif;
  font-size: 1.8rem;
  font-weight: 400;
}

.booking-subtitle {
  font-family: 'Inter', sans-serif;
  font-size: 1.05rem;
  line-height: 1.6;
  opacity: 0.8;
  max-width: 600px;
}

@media (max-width: 991px) {
  .event-hero {
    height: 500px;
  }
}

@media (max-width: 768px) {
  .content-title {
    font-size: 2.8rem;
  }
}
</style>
