<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'

const props = defineProps({
  initialLight: {
    type: Boolean,
    default: false,
  },
})

const isScrolled = ref(false)
const activeSection = ref('hero')
const navRef = ref(null)
const indicatorStyle = ref({})

const navLinks = [
  { id: 'hero', text: 'Beranda' },
  { id: 'about', text: 'Tentang' },
  { id: 'activities', text: 'Aktivitas' },
  { id: 'gallery', text: 'Galeri' },
  { id: 'events', text: 'Event' },
  { id: 'contact', text: 'Kontak' },
]

let observer = null

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50
}

const updateIndicator = () => {
  nextTick(() => {
    if (!navRef.value) return

    const activeLink = navRef.value.querySelector('.nav-link.active')
    if (activeLink) {
      indicatorStyle.value = {
        left: `${activeLink.offsetLeft}px`,
        width: `${activeLink.offsetWidth}px`,
      }
    }
  })
}

const observeSections = () => {
  const options = {
    root: null,
    rootMargin: '-20% 0px -80% 0px',
    threshold: 0,
  }

  observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        activeSection.value = entry.target.id
        updateIndicator()
      }
    })
  }, options)

  navLinks.forEach((link) => {
    const section = document.getElementById(link.id)
    if (section) observer.observe(section)
  })
}

const scrollToSection = (sectionId) => {
  const section = document.getElementById(sectionId)
  if (section) {
    section.scrollIntoView({ behavior: 'smooth' })
    activeSection.value = sectionId
    updateIndicator()
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  observeSections()
  // Initial indicator position
  nextTick(() => {
    updateIndicator()
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  if (observer) {
    navLinks.forEach((link) => {
      const section = document.getElementById(link.id)
      if (section) observer.unobserve(section)
    })
  }
})
</script>

<template>
  <nav
    v-motion
    :initial="{ opacity: 0, y: -50 }"
    :enter="{ opacity: 1, y: 0 }"
    :duration="600"
    :class="[
      'navbar navbar-expand-lg fixed-top transition-navbar',
      isScrolled ? 'glass-nav' : initialLight ? 'light-nav' : 'transparent-nav',
    ]"
  >
    <div class="container">
      <!-- Logo/Brand -->
      <a
        :class="[
          'navbar-brand fw-bold fs-5',
          initialLight && !isScrolled ? 'text-white' : 'text-dark',
        ]"
        href="#hero"
        @click.prevent="scrollToSection('hero')"
        >TeraSamarinda</a
      >

      <!-- Hamburger Menu for Mobile -->
      <button
        class="navbar-toggler border-0 shadow-none"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarContent"
        aria-controls="navbarContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navigation Links -->
      <div ref="navRef" class="collapse navbar-collapse justify-content-end" id="navbarContent">
        <ul class="navbar-nav gap-lg-4 align-items-center mt-3 mt-lg-0 position-relative">
          <!-- Sliding Indicator -->
          <span
            :class="[
              'nav-indicator',
              initialLight && !isScrolled ? 'nav-indicator-light' : 'nav-indicator-dark',
            ]"
            :style="indicatorStyle"
          ></span>

          <li v-for="link in navLinks" :key="link.id" class="nav-item">
            <a
              :class="[
                'nav-link fw-medium',
                initialLight && !isScrolled ? 'text-white' : 'text-dark',
                { active: activeSection === link.id },
              ]"
              :href="`#${link.id}`"
              @click.prevent="scrollToSection(link.id)"
            >
              {{ link.text }}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<style scoped>
/* ==============================================================
   PENGATURAN KETINGGIAN & POSISI NAVBAR (UBAH DI SINI) 
   ============================================================== */

/* 1. Jarak Navbar Saat di Atas (Transparan - Teks Gelap) */
.transparent-nav {
  background-color: transparent !important;
  border-bottom: none;
  padding-top: 0.8rem;
  padding-bottom: 0.8rem;
}

/* 2. Navbar Saat di Atas (Transparan - Teks Putih) */
.light-nav {
  background-color: transparent !important;
  border-bottom: none;
  padding-top: 0.8rem;
  padding-bottom: 0.8rem;
}

/* 3. Jarak & Efek Navbar Saat Di-Scroll (Glassmorphism) */
.glass-nav {
  background-color: rgba(255, 255, 255, 0.25) !important;
  backdrop-filter: blur(50px);
  -webkit-backdrop-filter: blur(50px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

/* ============================================================== */

.navbar {
  font-family: 'Inter', sans-serif;
  z-index: 1030;
  transition:
    background-color 0.3s ease,
    backdrop-filter 0.3s ease,
    border-bottom 0.3s ease,
    box-shadow 0.3s ease,
    padding 0.3s ease;
}

/* Typography & Interactive Styles */
.navbar-brand {
  letter-spacing: -0.5px;
  cursor: pointer;
}

.nav-link {
  position: relative;
  font-size: 0.95rem;
  transition: color 0.2s ease;
}

.nav-link:hover {
  opacity: 0.8;
}

.nav-link.text-dark:hover {
  color: #000000 !important;
}

.nav-link.text-white:hover {
  color: #ffffff !important;
}

/* Sliding Indicator */
.nav-indicator {
  position: absolute;
  bottom: 0;
  height: 2px;
  border-radius: 2px;
  transition:
    left 0.3s ease,
    width 0.3s ease;
  pointer-events: none;
}

.nav-indicator-dark {
  background-color: #000000;
}

.nav-indicator-light {
  background-color: #ffffff;
}
</style>
