<script setup>
import ContactInfoItem from '@/components/ui/ContactInfoItem.vue'
import ActionButton from '@/components/ui/ActionButton.vue'

defineProps({
  id: String,
})

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
</script>

<template>
  <section :id="id" class="contact-section py-5">
    <div class="container py-lg-5">
      <div class="row align-items-center gy-5">
        <!-- Content Column -->
        <div class="col-lg-6">
          <div class="pe-lg-5">
            <h2 class="contact-title mb-4"><span class="text-italic">Kunjungi Kami</span></h2>
            <p class="contact-description mb-5">
              Berlokasi strategis di pusat kota Samarinda, Teras Samarinda sangat mudah diakses
              dengan kendaraan pribadi maupun transportasi umum.
            </p>

            <div class="contact-info-list mb-5">
              <ContactInfoItem
                :icon="locationIcon"
                label="Alamat"
                value="Jl. Gajah Mada, Bugis, Kec. Samarinda Kota, Kota Samarinda, Kalimantan Timur 75121"
              />
              <ContactInfoItem
                :icon="clockIcon"
                label="Jam Operasional"
                value="Senin - Minggu: Terbuka 24 Jam"
              />
            </div>

            <!-- Action Button with 0px gap as requested -->
            <ActionButton
              text="BUKA DI GOOGLE MAPS"
              variant="dark"
              gap="0px"
              href="https://maps.app.goo.gl/96e5ea78007505d1"
            />
          </div>
        </div>

        <!-- Map Column -->
        <div class="col-lg-6">
          <div class="map-container shadow-lg rounded-4 overflow-hidden border">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.665337187675!2d117.13924390000001!3d-0.5017463999999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df67f001e7cbe89%3A0x96e5ea78007505d1!2sTeras%20Samarinda!5e0!3m2!1sid!2sid!4v1775472497616!5m2!1sid!2sid"
              width="100%"
              height="450"
              style="border: 0"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.contact-section {
  background-color: #f7f7f7;
  position: relative;
  z-index: 10;
}

.contact-title {
  font-family: 'Instrument Serif', serif;
  font-size: clamp(3.5rem, 6vw, 4.5rem);
  font-weight: 400;
  line-height: 1.1;
  color: #1a1a1a;
}

.contact-title .text-italic {
  font-style: italic;
}

.contact-description {
  font-family: 'Inter', sans-serif;
  font-size: 1.125rem;
  line-height: 1.6;
  color: #555;
  max-width: 500px;
}

.map-container {
  min-height: 450px;
  background-color: #fafafa;
}

.map-container iframe {
  display: block;
}

@media (max-width: 991.98px) {
  .contact-title {
    font-size: 3rem;
  }
}
</style>
