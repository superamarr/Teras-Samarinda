<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const props = defineProps({
  initialLight: {
    type: Boolean,
    default: false,
  },
  currentPage: {
    type: String,
    default: 'landing',
  },
})

const isScrolled = ref(false)
const activeSection = ref('hero')
const navRef = ref(null)
const indicatorStyle = ref({})
const isMenuOpen = ref(false)

const isLandingPage = computed(() => !props.currentPage || props.currentPage === 'landing')

const effectiveActiveSection = computed(() => {
  if (isLandingPage.value) return activeSection.value
  if (props.currentPage === 'about-page') return 'about'
  if (props.currentPage === 'gallery-page') return 'gallery'
  if (props.currentPage === 'event-page' || props.currentPage === 'events-page') return 'events'
  return 'hero'
})

const navLinks = [
  { id: 'hero', text: 'Beranda' },
  { id: 'about', text: 'Tentang' },
  { id: 'activities', text: 'Aktivitas' },
  { id: 'facilities', text: 'Fasilitas' },
  { id: 'gallery', text: 'Galeri' },
  { id: 'events', text: 'Event' },
  { id: 'contact', text: 'Kontak' },
]

let observer = null
let isProgrammaticScrolling = false
let programmaticScrollTimer = null

// Handle scroll untuk navbar style dan hero detection
const handleScroll = () => {
  isScrolled.value = window.scrollY > 50

  // Khusus untuk Hero section dengan position: sticky
  // IntersectionObserver tidak bisa mendeteksi dengan baik
  if (!isProgrammaticScrolling && window.scrollY < 100) {
    activeSection.value = 'hero'
    updateIndicator()
  }
}

// Update indicator position
const updateIndicator = () => {
  if (!navRef.value) return

  const activeLink = navRef.value.querySelector('.nav-link.active')
  if (activeLink) {
    indicatorStyle.value = {
      left: `${activeLink.offsetLeft}px`,
      width: `${activeLink.offsetWidth}px`,
    }
  }
}

// Debounced update indicator
let debounceTimer = null
const debouncedUpdateIndicator = () => {
  if (debounceTimer) clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    updateIndicator()
  }, 100)
}

// Setup IntersectionObserver
const observeSections = () => {
  const options = {
    root: null,
    rootMargin: '-80px 0px -70% 0px',
    threshold: 0,
  }

  observer = new IntersectionObserver((entries) => {
    // Skip jika sedang programmatic scroll
    if (isProgrammaticScrolling) return

    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        activeSection.value = entry.target.id
        debouncedUpdateIndicator()
      }
    })
  }, options)

  navLinks.forEach((link) => {
    const section = document.getElementById(link.id)
    if (section) observer.observe(section)
  })
}

// Scroll to section dengan cleanup yang proper
const scrollToSection = (sectionId) => {
  // Jika bukan di landing page, navigasi ke landing page dengan hash
  if (!isLandingPage.value) {
    router.push({ path: '/', hash: `#${sectionId}` })
    return
  }

  // Set flag
  isProgrammaticScrolling = true

  // Clear existing timer
  if (programmaticScrollTimer) {
    clearTimeout(programmaticScrollTimer)
  }

  // Update active section immediately
  activeSection.value = sectionId
  nextTick(() => {
    updateIndicator()
  })

  // Perform scroll
  if (sectionId === 'hero') {
    window.scrollTo({
      top: 0,
      behavior: 'smooth',
    })
  } else {
    const section = document.getElementById(sectionId)
    if (section) {
      const navHeight = 80
      const elementPosition = section.getBoundingClientRect().top
      const offsetPosition = elementPosition + window.pageYOffset - navHeight

      window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth',
      })
    }
  }

  // Reset flag after scroll animation completes
  // Smooth scroll biasanya 500-1000ms, kita gunakan 1200ms untuk safety
  programmaticScrollTimer = setTimeout(() => {
    isProgrammaticScrolling = false
  }, 1200)
}

// Watch effectiveActiveSection changes untuk update indicator
watch(effectiveActiveSection, () => {
  nextTick(() => {
    updateIndicator()
  })
})

// Lifecycle hooks dengan cleanup yang proper
onMounted(() => {
  // Setup scroll listener
  window.addEventListener('scroll', handleScroll)

  // Setup intersection observer
  observeSections()

  // Setup collapse event listeners for mobile menu background
  const navbarCollapse = document.getElementById('navbarContent')
  if (navbarCollapse) {
    navbarCollapse.addEventListener('show.bs.collapse', () => {
      isMenuOpen.value = true
    })
    navbarCollapse.addEventListener('hide.bs.collapse', () => {
      isMenuOpen.value = false
    })
  }

  // Initial indicator position
  nextTick(() => {
    updateIndicator()
  })
})

onUnmounted(() => {
  // Cleanup scroll listener
  window.removeEventListener('scroll', handleScroll)

  // Cleanup debounce timer
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }

  // Cleanup programmatic scroll timer
  if (programmaticScrollTimer) {
    clearTimeout(programmaticScrollTimer)
  }

  // Cleanup intersection observer
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
      isMenuOpen && !isScrolled
        ? 'mobile-menu-open-top'
        : isMenuOpen && isScrolled
          ? 'mobile-menu-open-scrolled'
          : isScrolled
            ? 'glass-nav'
            : initialLight
              ? 'light-nav'
              : 'transparent-nav',
    ]"
  >
    <div class="container">
      <!-- Logo/Brand -->
      <a
        :class="[
          'navbar-brand fw-bold fs-5',
          (initialLight && !isScrolled) || (isMenuOpen && !isScrolled) ? 'text-white' : 'text-dark',
        ]"
        href="#hero"
        @click.prevent="scrollToSection('hero')"
        >TeraSamarinda</a
      >

      <!-- Hamburger Menu for Mobile -->
      <button
        :class="[
          'navbar-toggler border-0 shadow-none',
          (initialLight && !isScrolled) || (isMenuOpen && !isScrolled)
            ? 'navbar-toggler-light'
            : '',
        ]"
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
                (initialLight && !isScrolled) || (isMenuOpen && !isScrolled)
                  ? 'text-white'
                  : 'text-dark',
                { active: effectiveActiveSection === link.id },
              ]"
              :href="isLandingPage ? `#${link.id}` : `/#${link.id}`"
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
  background-color: var(--color-glass-bg) !important;
  backdrop-filter: blur(var(--glass-blur));
  -webkit-backdrop-filter: blur(var(--glass-blur));
  border-bottom: 1px solid var(--color-glass-border);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
  padding-top: var(--spacing-sm);
  padding-bottom: var(--spacing-sm);
}

/* 4. Mobile Menu Open (Top) */
@media (max-width: 991px) {
  .mobile-menu-open-top {
    background-color: #033d4a !important;
    border-bottom: none;
  }

  .mobile-menu-open-scrolled {
    background-color: #ffffff !important;
    border-bottom: 1px solid #f0f0f0;
  }
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

.navbar-toggler-light .navbar-toggler-icon {
  filter: invert(1) brightness(2);
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
  left: 0;
  height: 2px;
  border-radius: 2px;
  will-change: left, width;
  transform: translateZ(0);
  transition:
    left 0.4s cubic-bezier(0.4, 0, 0.2, 1),
    width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  pointer-events: none;
}

.nav-indicator-dark {
  background-color: #000000;
}

.nav-indicator-light {
  background-color: #ffffff;
}
</style>
