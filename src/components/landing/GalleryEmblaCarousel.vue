<script setup>
/**
 * Komponen terpisah agar useEmblaCarousel onMounted jalan saat viewport sudah ada di DOM.
 * (Di parent dengan v-if data, ref belum ada saat onMounted parent → Embla tidak pernah init.)
 */
import { ref, watch, onUnmounted, onMounted, nextTick, computed } from 'vue'
import useEmblaCarousel from 'embla-carousel-vue'

const props = defineProps({
  slides: {
    type: Array,
    required: true,
    /** { id, src, alt }[] */
  },
})

const [emblaRef, emblaApi] = useEmblaCarousel({
  loop: true,
  align: 'center',
  skipSnaps: false,
  dragFree: false,
  duration: 24,
})

const activeIndex = ref(0)
const slidesInView = ref([])
const lightboxOpen = ref(false)
const lightboxIndex = ref(0)

const currentImage = computed(() => {
  return props.slides[lightboxIndex.value] || props.slides[0]
})

const galleryGapPx = () =>
  typeof window !== 'undefined' && window.matchMedia('(min-width: 992px)').matches ? 16 : 12

let resizeObserver = null
let slideMetricsRaf = 0
let lastSlideMetricsKey = ''

const teardownGalleryResize = () => {
  if (resizeObserver) {
    resizeObserver.disconnect()
    resizeObserver = null
  }
  cancelAnimationFrame(slideMetricsRaf)
}

const applyGallerySlideMetrics = () => {
  const root = emblaRef.value
  const api = emblaApi.value
  if (!root?.getBoundingClientRect || !api) return

  cancelAnimationFrame(slideMetricsRaf)
  slideMetricsRaf = requestAnimationFrame(() => {
    const w = Math.round(root.getBoundingClientRect().width)
    if (w < 80) return

    const g = galleryGapPx()
    const key = `${w}:${g}`
    if (key === lastSlideMetricsKey) return
    lastSlideMetricsKey = key

    const slidePx = Math.max(140, w * 0.5 - g)
    root.style.setProperty('--gallery-gutter-px', `${g}px`)
    root.style.setProperty('--gallery-slide-size', `${slidePx}px`)
    api.reInit()
  })
}

const setupGalleryResize = async () => {
  await nextTick()
  const root = emblaRef.value
  const api = emblaApi.value
  if (!root || !api || props.slides.length === 0) return

  lastSlideMetricsKey = ''
  teardownGalleryResize()
  applyGallerySlideMetrics()
  resizeObserver = new ResizeObserver(() => applyGallerySlideMetrics())
  resizeObserver.observe(root)
}

const getSlideClass = (index) => {
  if (activeIndex.value === index) return 'is-active'
  if (slidesInView.value.includes(index)) return 'is-visible'
  return ''
}

const openLightbox = (index) => {
  lightboxIndex.value = index
  lightboxOpen.value = true
  document.body.style.overflow = 'hidden'
  nextTick(() => {
    lightboxApi.value?.reInit()
    setTimeout(() => {
      lightboxApi.value?.scrollTo(index, true)
    }, 50)
  })
}

const closeLightbox = () => {
  lightboxOpen.value = false
  document.body.style.overflow = ''
}

const onKeydown = (event) => {
  if (!lightboxOpen.value) return
  if (event.key === 'Escape') closeLightbox()
  if (event.key === 'ArrowLeft') lightboxApi.value?.scrollPrev()
  if (event.key === 'ArrowRight') lightboxApi.value?.scrollNext()
}

watch(emblaApi, (api) => {
  if (!api) return

  slidesInView.value = api.slidesInView()
  activeIndex.value = api.selectedScrollSnap()

  api.on('select', () => {
    activeIndex.value = api.selectedScrollSnap()
  })

  if (typeof api.on === 'function') {
    api.on('slidesInView', (_emblaApi, event) => {
      if (event?.detail?.slidesInView) {
        slidesInView.value = event.detail.slidesInView
      }
    })
  }

  nextTick(() => {
    if (props.slides.length > 0) setupGalleryResize()
  })
})

watch(
  () => props.slides.length,
  (len) => {
    if (len === 0) {
      teardownGalleryResize()
      lastSlideMetricsKey = ''
      return
    }
    if (emblaApi.value) nextTick(() => setupGalleryResize())
  },
)

onUnmounted(() => {
  teardownGalleryResize()
  document.body.style.overflow = ''
  window.removeEventListener('keydown', onKeydown)
})

onMounted(() => {
  window.addEventListener('keydown', onKeydown)
})
</script>

<template>
  <div class="gallery-carousel">
    <div class="embla">
      <div class="embla__viewport" ref="emblaRef">
        <div class="embla__container">
          <div
            v-for="(image, index) in slides"
            :key="image.id"
            class="embla__slide"
            :class="getSlideClass(index)"
          >
            <div class="image-inner">
              <img
                :src="image.src"
                :alt="image.alt"
                class="img-fluid w-100 h-100 object-fit-cover"
                draggable="false"
                @click="openLightbox(index)"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <Teleport to="body">
    <div
      class="gallery-lightbox"
      :class="{ 'is-visible': lightboxOpen }"
      @click.self="closeLightbox"
    >
      <button class="lightbox-close" @click="closeLightbox" aria-label="Close popup">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="currentColor"
          viewBox="0 0 16 16"
        >
          <path
            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"
          />
        </svg>
      </button>
      <div class="lightbox-image-container">
        <img
          v-if="currentImage"
          :src="currentImage.src"
          :alt="currentImage.alt"
          class="lightbox-single-image"
        />
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.gallery-carousel {
  width: 100%;
  min-width: 0;
}

.embla {
  width: 100%;
  margin: 0 auto;
}

.embla__viewport {
  width: 100%;
  cursor: grab;
  padding: 0;
  overflow: hidden;
}

.embla__viewport:active {
  cursor: grabbing;
}

.embla__container {
  display: flex;
  touch-action: pan-x pinch-zoom;
  gap: var(--gallery-gutter-px, 12px);
}

.embla__slide {
  flex: 0 0 var(--gallery-slide-size, 42%);
  min-width: 0;
  position: relative;
}

.image-inner {
  position: relative;
  aspect-ratio: 16 / 10;
  overflow: hidden;
  border-radius: 0;
  transform: scale(0.94);
  opacity: 0.72;
  transition:
    transform 0.55s cubic-bezier(0.2, 1, 0.3, 1),
    opacity 0.45s ease;
  box-shadow: none;
}

.embla__slide.is-active .image-inner {
  transform: scale(1);
  opacity: 1;
  z-index: 2;
}

.image-inner img {
  user-select: none;
  -webkit-user-drag: none;
  cursor: zoom-in;
}

.gallery-lightbox {
  position: fixed;
  inset: 0;
  z-index: 10000;
  background: rgba(0, 0, 0, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  visibility: hidden;
  opacity: 0;
  transition:
    opacity 0.25s ease,
    visibility 0.25s ease;
}

.gallery-lightbox.is-visible {
  visibility: visible;
  opacity: 1;
}

.lightbox-image-container {
  display: flex;
  align-items: center;
  justify-content: center;
  width: min(82vw, 980px);
  height: min(78vh, 720px);
}

.lightbox-single-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.lightbox-close {
  position: absolute;
  top: 1.25rem;
  right: 1.25rem;
  border: 0;
  width: 3.25rem;
  height: 3.25rem;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.5);
  color: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  z-index: 11;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
}

.lightbox-close svg {
  width: 1.75rem;
  height: 1.75rem;
  color: #fff;
}

.lightbox-close:hover {
  background: rgba(220, 53, 69, 0.9);
  border-color: rgba(255, 255, 255, 0.5);
  transform: scale(1.1) rotate(90deg);
}

@media (max-width: 768px) {
  .lightbox-image-container {
    width: min(92vw, 560px);
    height: min(68vh, 560px);
  }

  .lightbox-close {
    top: 0.75rem;
    right: 0.75rem;
  }
}
</style>
