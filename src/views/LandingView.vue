<script setup>
import Navbar from '@/components/Navbar.vue'
import HeroSection from '@/components/landing/HeroSection.vue'
import AboutSection from '@/components/landing/AboutSection.vue'
import ActivitiesSection from '@/components/landing/ActivitiesSection.vue'
import FacilitiesSection from '@/components/landing/FacilitiesSection.vue'
import GallerySection from '@/components/landing/GallerySection.vue'
import EventsSection from '@/components/landing/EventsSection.vue'
import ContactSection from '@/components/landing/ContactSection.vue'
import Footer from '@/components/Footer.vue'
</script>

<template>
  <main class="landing-page page-shell">
    <Navbar initialLight />

    <!-- HeroSection sekarang direct child agar position: sticky bekerja sempurna -->
    <HeroSection id="hero" />

    <AboutSection id="about" />
    <ActivitiesSection id="activities" />
    <FacilitiesSection id="facilities" class="section-stack-over" />
    <GallerySection id="gallery" class="section-stack-over" />
    <EventsSection id="events" class="section-stack-over" />
    <ContactSection id="contact" class="section-stack-over" />
    <Footer class="section-stack-over" />
  </main>
</template>

<style scoped>
.landing-page {
  /* Hilangkan display: flex jika dirasa membatasi tinggi elemen sticky di Chrome/Safari */
  display: block;
  background-color: var(--color-bg-light);
  min-height: 100vh;
}

/* Seksi-seksi di bawah hero harus punya background solid agar tidak tembus pandang */
:deep(.section-stack-over) {
  background-color: var(--color-bg-light);
  /* Force stacking context */
  position: relative;
  z-index: var(--z-section-over-hero);
}

/* Footer harus tetap gelap */
:deep(.footer-section) {
  background-color: #1b1b1b !important;
}
</style>
