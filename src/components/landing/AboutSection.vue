<script setup>
import ActionButton from '@/components/ui/ActionButton.vue'

defineProps({
  id: String,
})
</script>

<template>
  <section :id="id" class="about-section py-5">
    <div class="container py-lg-5">
      <div class="row gx-lg-5">
        <!-- Kolom Kiri -->
        <div class="col-lg-6 d-flex flex-column mb-4 mb-lg-0">
          <div class="about-text-content mb-4">
            <h2 class="about-title mb-4">
              <span class="text-normal">Tentang Teras</span>
              <span class="text-italic">Samarinda</span>
            </h2>
            <p class="about-description mb-4">
              Teras Samarinda adalah wajah baru ruang publik Kota Samarinda yang dirancang sebagai
              kawasan waterfront modern di tepian Sungai Mahakam.
            </p>

            <ActionButton text="BACA SELENGKAPNYA" variant="dark" gap="0px" href="/tentang" />
          </div>

          <!-- Gambar Bawah Kiri (Jembatan / Header) -->
          <div class="img-wrapper-sm mt-auto">
            <img
              src="@/assets/images/header-ex.png"
              alt="Jembatan Teras Samarinda"
              class="img-fluid w-100 object-fit-cover shadow-sm"
              style="max-height: 250px"
            />
          </div>
        </div>

        <!-- Kolom Kanan (Gambar Sign) -->
        <div class="col-lg-6">
          <div class="img-wrapper-lg h-100">
            <img
              src="@/assets/images/about-sec.jpg"
              alt="Sign Teras Samarinda"
              class="img-fluid w-100 h-100 object-fit-cover shadow-sm"
              style="min-height: 400px"
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.about-section {
  position: relative;
  z-index: 10;
  background-color: #f7f7f7;
  color: #1a1a1a;
}

.about-title {
  font-family: 'Instrument Serif', serif;
  font-size: clamp(2.5rem, 4vw, 3.5rem);
  font-weight: 400;
  line-height: 1.1;
  letter-spacing: -0.01em;
  color: #000000;
}

.about-title .text-italic {
  font-style: italic;
}

.about-title .text-normal {
  font-style: normal;
}

.about-description {
  font-family: 'Inter', sans-serif;
  font-size: 1rem;
  font-weight: 500;
  line-height: 1.6;
  color: #333333;
  max-width: 90%;
}

.img-wrapper-sm img,
.img-wrapper-lg img {
  border-radius: 4px;
}
</style>
