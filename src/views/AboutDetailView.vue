<script setup>
import { onMounted } from 'vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'

// Import images from assets
import heroImg from '@/assets/images/generation_8510.jfif'
import welcomeImg from '@/assets/images/about-sec.jpg' // Using this as the main welcome image
import storyBg from '@/assets/images/image 8.jpg'

gsap.registerPlugin(ScrollTrigger)

onMounted(() => {
  gsap.to('.story-bg', {
    yPercent: -30,
    ease: 'none',
    scrollTrigger: {
      trigger: '.story-section',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    },
  })
})
</script>

<template>
  <div class="about-detail-page">
    <Navbar initial-light />

    <!-- About Hero Section -->
    <section class="about-hero" :style="{ backgroundImage: `url(${heroImg})` }">
      <div class="hero-overlay"></div>
      <div
        class="container h-100 d-flex flex-column justify-content-center align-items-center text-center text-white"
      >
        <h1 class="hero-title mb-4">Tentang Teras <span class="text-italic"> Samarinda</span></h1>
        <p class="hero-subtitle">
          Teras Samarinda adalah wajah baru ruang publik Kota Samarinda yang dirancang sebagai
          kawasan waterfront modern di tepian Sungai Mahakam.
        </p>
      </div>
    </section>

    <!-- Welcome Section -->
    <section class="welcome-section py-5">
      <div class="container py-lg-5">
        <div class="row align-items-center gx-lg-5">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="welcome-img-wrapper shadow-sm rounded-4 overflow-hidden">
              <img :src="welcomeImg" alt="Welcome Teras Samarinda" class="img-fluid" />
            </div>
          </div>
          <div class="col-lg-6">
            <h2 class="section-title mb-4">Selamat Datang Di Teras Samarinda</h2>
            <div class="section-text">
              <p>
                Teras Samarinda bukan sekadar ruang terbuka hijau; ia adalah sebuah pernyataan
                tentang transformasi urban yang berkelanjutan. Terletak strategis di sepanjang
                tepian Sungai Mahakam yang legendaris, proyek ini mengintegrasikan desain arsitektur
                modern dengan kearifan lokal Kalimantan Timur.
              </p>
              <p>
                Inisiatif ini bertujuan untuk menghadirkan kembali akses publik ke sungai,
                menciptakan "paru-paru kota" yang fungsional sekaligus estetik. Dengan material
                ramah lingkungan dan sistem manajemen air yang canggih, Teras Samarinda menjadi
                simbol kemajuan kota yang tetap berpijak pada pelestarian alam.
              </p>
              <p>
                Dampak budayanya terasa kuat—menjadi titik temu bagi masyarakat dari berbagai
                lapisan, memicu ekonomi kreatif lokal melalui zona kuliner terpilih, dan menjadi
                panggung bagi ekspresi seni komunitas Samarinda.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Story Section -->
    <section class="story-section py-5">
      <div class="story-bg-wrapper">
        <img :src="storyBg" alt="" class="story-bg" />
        <div class="story-overlay"></div>
      </div>
      <div class="story-content position-relative">
        <div class="container py-5">
          <div class="row justify-content-center">
            <div class="col-lg-10">
              <div class="story-card p-4 p-lg-5 text-center shadow-lg">
                <h2 class="story-title mb-4">
                  Story Teras <span class="text-italic">Samarinda</span>
                </h2>
                <div class="story-text">
                  <p>
                    Di tepian Sungai Mahakam, wajah Kota Samarinda perlahan berubah. Kawasan yang
                    dahulu dikenal padat, kurang tertata, dan lebih berfungsi sebagai jalur
                    aktivitas biasa, kini bertransformasi menjadi ruang publik yang hidup dan penuh
                    makna. Perubahan ini melahirkan sebuah ikon baru kota, yaitu Teras Samarinda.
                  </p>
                  <p>
                    Sejarah Teras Samarinda bermula dari kesadaran akan besarnya potensi tepian
                    sungai sebagai pusat kehidupan kota. Sungai Mahakam yang selama ini seolah
                    menjadi "belakang" kota, mulai dipandang sebagai aset utama yang dapat
                    dihidupkan kembali. Pemerintah kota kemudian merancang sebuah visi besar:
                    menjadikan kawasan ini sebagai wajah depan Samarinda melalui konsep waterfront
                    city.
                  </p>
                </div>
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
.about-detail-page {
  background-color: #ffffff;
}

/* Hero Section Styles */
.about-hero {
  position: relative;
  height: 600px;
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(3, 61, 74, 0.7);
}

.hero-title {
  font-family: 'Instrument Serif', serif;
  font-size: clamp(3rem, 7vw, 5rem);
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
  font-size: 1.25rem;
  max-width: 700px;
  opacity: 0.9;
  position: relative;
  z-index: 2;
  line-height: 1.6;
}

/* Welcome Section */
.section-title {
  font-family: 'Instrument Serif', serif;
  font-size: 3.5rem;
  font-weight: 400;
  color: #1a1a1a;
  line-height: 1.1;
}

.section-text p {
  font-family: 'Inter', sans-serif;
  font-size: 1.1rem;
  line-height: 1.7;
  color: #444;
  margin-bottom: 1.5rem;
}

/* Story Section */
.story-section {
  position: relative;
  min-height: 600px;
  display: flex;
  align-items: center;
  overflow: hidden;
}

.story-bg-wrapper {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
}

.story-bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 150%;
  object-fit: cover;
  object-position: top;
}

.story-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
}

.story-content {
  position: relative;
  z-index: 1;
  width: 100%;
}

.story-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 4px;
  position: relative;
  z-index: 2;
}

.story-title {
  font-family: 'Instrument Serif', serif;
  font-size: 4rem;
  color: #1a1a1a;
}

.story-title .text-italic {
  font-style: italic;
}

.story-text p {
  font-family: 'Inter', sans-serif;
  font-size: 1.15rem;
  line-height: 1.8;
  color: #333;
  margin-bottom: 2rem;
}

@media (max-width: 991px) {
  .section-title {
    font-size: 2.8rem;
  }
  .story-title {
    font-size: 3rem;
  }
}

@media (max-width: 768px) {
  .about-hero {
    height: 500px;
  }
  .section-title {
    font-size: 2.4rem;
  }
}
</style>
