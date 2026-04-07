<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import ActionButton from '@/components/ui/ActionButton.vue'

// Import images from assets
import img1 from '@/assets/images/image (1).jpg'
import img2 from '@/assets/images/image (2).jpg'
import img3 from '@/assets/images/image (3).jpg'
import img4 from '@/assets/images/image (4).jpg'
import img5 from '@/assets/images/image (5).jpg'

defineProps({
  id: String,
})

const galleryImages = [
  { id: 1, src: img1, alt: 'Gallery Image 1' },
  { id: 2, src: img2, alt: 'Gallery Image 2' },
  { id: 3, src: img3, alt: 'Gallery Image 3' },
  { id: 4, src: img4, alt: 'Gallery Image 4' },
  { id: 5, src: img5, alt: 'Gallery Image 5' },
]

// Triplicate for infinite loop effect
const extendedGallery = computed(() => [...galleryImages, ...galleryImages, ...galleryImages])

// Carousel Logic
const slider = ref(null)
const isDragging = ref(false)
const startX = ref(0)
const scrollLeft = ref(0)
const activeIndex = ref(0)
const itemWidth = 550 + 16 // width + gap (1rem = 16px)

// Momentum / Velocity tracking
const velocity = ref(0)
const lastX = ref(0)
const lastTime = ref(0)
let momentumAnimation = null

const startDragging = (e) => {
  isDragging.value = true
  const pageX = e.pageX || e.touches[0].pageX
  startX.value = pageX - slider.value.offsetLeft
  scrollLeft.value = slider.value.scrollLeft
  lastX.value = pageX
  lastTime.value = Date.now()
  velocity.value = 0

  slider.value.style.cursor = 'grabbing'
  slider.value.style.scrollBehavior = 'auto'

  // Cancel any ongoing momentum animation
  if (momentumAnimation) {
    cancelAnimationFrame(momentumAnimation)
  }
}

const stopDragging = (e) => {
  if (!isDragging.value) return

  isDragging.value = false

  if (slider.value) {
    slider.value.style.cursor = 'grab'

    // Apply momentum
    if (Math.abs(velocity.value) > 0.5) {
      applyMomentum()
    } else {
      slider.value.style.scrollBehavior = 'smooth'
      snapToNearest()
    }
  }
}

const move = (e) => {
  if (!isDragging.value || !slider.value) return
  e.preventDefault()

  const pageX = e.pageX || e.touches[0].pageX
  const x = pageX - slider.value.offsetLeft
  const walk = (x - startX.value) * 1.5

  // Calculate velocity
  const now = Date.now()
  const dt = now - lastTime.value
  if (dt > 0) {
    velocity.value = (pageX - lastX.value) / dt
  }
  lastX.value = pageX
  lastTime.value = now

  slider.value.scrollLeft = scrollLeft.value - walk
}

const applyMomentum = () => {
  let currentVelocity = velocity.value
  let lastScrollLeft = slider.value.scrollLeft
  const friction = 0.95
  const minVelocity = 0.01

  const animate = () => {
    if (Math.abs(currentVelocity) < minVelocity || isDragging.value) {
      slider.value.style.scrollBehavior = 'smooth'
      snapToNearest()
      return
    }

    currentVelocity *= friction
    slider.value.scrollLeft = lastScrollLeft - currentVelocity * 20
    lastScrollLeft = slider.value.scrollLeft

    handleInfiniteLoop()
    momentumAnimation = requestAnimationFrame(animate)
  }

  animate()
}

const snapToNearest = () => {
  if (!slider.value) return

  const scrollPos = slider.value.scrollLeft
  const nearestIndex = Math.round(scrollPos / itemWidth)
  const snapPosition = nearestIndex * itemWidth

  slider.value.scrollTo({
    left: snapPosition,
    behavior: 'smooth',
  })
}

const handleInfiniteLoop = () => {
  if (!slider.value) return

  const scrollPos = slider.value.scrollLeft
  const totalWidth = galleryImages.length * itemWidth

  // Infinite loop: seamless boundary crossing
  if (scrollPos >= totalWidth * 2) {
    slider.value.scrollLeft = scrollPos - totalWidth
  } else if (scrollPos < itemWidth) {
    slider.value.scrollLeft = scrollPos + totalWidth
  }
}

const handleScroll = () => {
  if (!slider.value || isDragging.value) return

  handleInfiniteLoop()

  // Calculate active index (normalized to original array)
  const rawIndex = Math.round(slider.value.scrollLeft / itemWidth)
  activeIndex.value = Math.abs(rawIndex) % galleryImages.length
}

onMounted(() => {
  if (slider.value) {
    // Set initial scroll position to middle set
    slider.value.scrollLeft = galleryImages.length * itemWidth
    slider.value.addEventListener('scroll', handleScroll)
  }
})

onUnmounted(() => {
  if (slider.value) {
    slider.value.removeEventListener('scroll', handleScroll)
  }
  if (momentumAnimation) {
    cancelAnimationFrame(momentumAnimation)
  }
})
</script>

<template>
  <section :id="id" class="gallery-section py-5">
    <div class="container py-lg-5">
      <!-- Header Section -->
      <div class="row align-items-center mb-5">
        <div class="col-lg-8">
          <h2 class="gallery-title">Gallery Teras <span class="text-italic">Samarinda</span></h2>
          <p class="gallery-subtitle">
            Intip keceriaan pengunjung dan keindahan arsitektur Teras Samarinda
          </p>
        </div>
        <div class="col-lg-4 d-flex justify-content-lg-end mt-4 mt-lg-0">
          <ActionButton text="LIHAT SELENGKAPNYA" variant="dark" gap="0px" href="/galeri" />
        </div>
      </div>

      <!-- Carousel / Gallery Images -->
      <div
        class="gallery-carousel-wrapper"
        ref="slider"
        @mousedown="startDragging"
        @touchstart="startDragging"
        @mouseleave="stopDragging"
        @mouseup="stopDragging"
        @touchend="stopDragging"
        @mousemove="move"
        @touchmove="move"
      >
        <div class="gallery-track">
          <div
            v-for="(image, index) in extendedGallery"
            :key="'img-' + index"
            class="gallery-item"
            :class="{ active: activeIndex === index % galleryImages.length }"
          >
            <div class="image-inner">
              <img
                :src="image.src"
                :alt="image.alt"
                class="img-fluid w-100 h-100 object-fit-cover"
              />
              <div class="dim-overlay"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.gallery-section {
  background-color: #f7f7f7;
  position: relative;
  z-index: 10;
  overflow: hidden;
}

.gallery-title {
  font-family: 'Instrument Serif', serif;
  font-size: clamp(3rem, 5vw, 4.2rem);
  font-weight: 400;
  line-height: 1.1;
  color: #1a1a1a;
}

.gallery-title .text-italic {
  font-style: italic;
}

.gallery-subtitle {
  font-family: 'Inter', sans-serif;
  font-size: 1.15rem;
  color: #666;
  max-width: 500px;
}

/* Carousel Container */
.gallery-carousel-wrapper {
  cursor: grab;
  overflow-x: auto;
  overflow-y: hidden;
  scrollbar-width: none;
  -ms-overflow-style: none;
  /* Scroll Snap for center snap behavior */
  scroll-snap-type: x proximity;
  /* Smoother scrolling */
  -webkit-overflow-scrolling: touch;
  /* Padding untuk efek sepotong di kiri-kanan */
  padding: 20px calc((100% - 550px) / 2);
}

.gallery-carousel-wrapper::-webkit-scrollbar {
  display: none;
}

.gallery-track {
  display: flex;
  gap: 0.1rem;
}

.gallery-item {
  flex: 0 0 auto;
  width: 550px;
  max-width: 85vw;
  /* Scroll Snap Align Center */
  scroll-snap-align: center;
  transition: all 0.5s ease;
  transform: scale(0.85);
  opacity: 0.6;
}

.gallery-item.active {
  transform: scale(1);
  opacity: 1;
}

.image-inner {
  position: relative;
  aspect-ratio: 16/10;
  overflow: hidden;
  border-radius: 4px;
}

.image-inner img {
  user-select: none;
  -webkit-user-drag: none;
  transition: transform 0.3s ease;
}

.gallery-item.active .image-inner:hover img {
  transform: scale(1.02);
}

/* Dim Overlay untuk item non-aktif */
.dim-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(150, 150, 150, 0.3);
  pointer-events: none;
  transition: opacity 0.5s ease;
}

.gallery-item.active .dim-overlay {
  opacity: 0;
}

@media (max-width: 992px) {
  .gallery-carousel-wrapper {
    padding: 20px calc((100% - 450px) / 2);
  }

  .gallery-item {
    width: 450px;
  }
}

@media (max-width: 768px) {
  .gallery-carousel-wrapper {
    padding: 20px calc((100% - 320px) / 2);
  }

  .gallery-item {
    width: 320px;
    max-width: 80vw;
  }
}

@media (max-width: 480px) {
  .gallery-carousel-wrapper {
    padding: 20px calc((100% - 280px) / 2);
  }

  .gallery-item {
    width: 280px;
    max-width: 75vw;
  }
}
</style>
