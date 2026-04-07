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
  <main class="landing-page">
    <Navbar initialLight />
    <HeroSection id="hero" />
    <AboutSection id="about" />
    <ActivitiesSection id="activities" />
    <FacilitiesSection />
    <GallerySection id="gallery" />
    <EventsSection id="events" />
    <ContactSection id="contact" />
    <Footer />
  </main>
</template>

<style scoped>
.landing-page {
  min-height: 100vh;
}
</style>
