<script setup>
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'

// Import images from assets
import heroImg from '@/assets/images/image 5.jpg'
import img1 from '@/assets/images/image (2).jpg'
import img2 from '@/assets/images/image (3).jpg'
import img3 from '@/assets/images/image (4).jpg'
import img4 from '@/assets/images/image (5).jpg'
import img5 from '@/assets/images/image 5.jpg'
import img6 from '@/assets/images/image 6.jpg'
import img7 from '@/assets/images/image 8.jpg'

const galleryItems = [
  { id: 1, src: img1, alt: 'Gallery Item 1', class: 'grid-item-wide' },
  { id: 2, src: img2, alt: 'Gallery Item 2', class: 'grid-item-tall' },
  { id: 3, src: img3, alt: 'Gallery Item 3', class: 'grid-item-normal' },
  { id: 4, src: img4, alt: 'Gallery Item 4', class: 'grid-item-normal' },
  { id: 5, src: img5, alt: 'Gallery Item 5', class: 'grid-item-tall' },
  { id: 6, src: img6, alt: 'Gallery Item 6', class: 'grid-item-wide' },
  { id: 7, src: img7, alt: 'Gallery Item 7', class: 'grid-item-normal' },
]
</script>

<template>
  <div class="gallery-page">
    <Navbar initial-light />

    <!-- Gallery Hero Section -->
    <section class="gallery-hero" :style="{ backgroundImage: `url(${heroImg})` }">
      <div class="hero-overlay"></div>
      <div
        class="container h-100 d-flex flex-column justify-content-center align-items-center text-center"
      >
        <h1 class="hero-title mb-3">
          Galeri Foto Teras <span class="text-italic">Samarinda</span>
        </h1>
        <p class="hero-subtitle">
          Intip keceriaan pengunjung dan keindahan arsitektur Teras Samarinda
        </p>
      </div>
    </section>

    <!-- Gallery Content Selection -->
    <section class="gallery-content py-5">
      <div class="container py-lg-5">
        <div class="gallery-masonry">
          <div
            v-for="item in galleryItems"
            :key="item.id"
            :class="['gallery-grid-item', item.class]"
          >
            <div class="image-wrapper">
              <img :src="item.src" :alt="item.alt" class="img-fluid" />
              <div class="image-hover-overlay">
                <span class="view-text">Teras Samarinda</span>
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
.gallery-page {
  background-color: #ffffff;
}

/* Hero Section Styles */
.gallery-hero {
  position: relative;
  height: 500px;
  background-size: cover;
  background-position: 30% 200%;
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
  font-size: clamp(3rem, 6vw, 4.5rem);
  font-weight: 400;
  line-height: 1.1;
  position: relative;
  z-index: 2;
}

.hero-title .text-italic {
  font-style: italic;
}

.hero-subtitle {
  font-family: 'Inter', sans-serif;
  font-size: 1.25rem;
  max-width: 600px;
  opacity: 1.9;
  position: relative;
  z-index: 2;
  white-space: nowrap;
}

/* Masonry Grid Styles */
.gallery-masonry {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  grid-auto-rows: 250px;
  gap: 24px;
}

.gallery-grid-item {
  border-radius: 4px;
  overflow: hidden;
  transition: transform 0.4s ease;
}

.grid-item-wide {
  grid-column: span 1;
  grid-row: span 1;
}

.grid-item-tall {
  grid-column: span 1;
  grid-row: span 2;
}

.grid-item-normal {
  grid-column: span 1;
  grid-row: span 1;
}

/* Khusus untuk layout yang menarik seperti di desain */
@media (min-width: 992px) {
  .gallery-masonry {
    grid-auto-rows: 300px;
  }

  .gallery-grid-item:nth-child(2) {
    grid-row: span 2;
  }

  .gallery-grid-item:nth-child(3) {
    grid-row: span 2;
  }

  .gallery-grid-item:nth-child(5) {
    grid-row: span 2;
  }
}

.image-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
}

.image-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.image-hover-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0;
  transition: opacity 0.4s ease;
}

.view-text {
  color: #fff;
  font-family: 'Inter', sans-serif;
  font-weight: 500;
  letter-spacing: 1px;
  transform: translateY(20px);
  transition: transform 0.4s ease;
}

.gallery-grid-item:hover .image-wrapper img {
  transform: scale(1.05);
}

.gallery-grid-item:hover .image-hover-overlay {
  opacity: 1;
}

.gallery-grid-item:hover .view-text {
  transform: translateY(0);
}

@media (max-width: 768px) {
  .gallery-masonry {
    grid-template-columns: 1fr;
    grid-auto-rows: 300px;
  }

  .gallery-hero {
    height: 400px;
  }

  .grid-item-tall,
  .grid-item-wide,
  .grid-item-normal {
    grid-column: span 1;
    grid-row: span 1;
  }
}
</style>
