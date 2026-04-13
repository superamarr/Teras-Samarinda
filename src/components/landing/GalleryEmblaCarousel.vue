<script setup>
/**
 * Komponen terpisah agar useEmblaCarousel onMounted jalan saat viewport sudah ada di DOM.
 * (Di parent dengan v-if data, ref belum ada saat onMounted parent → Embla tidak pernah init.)
 */
import { ref, watch, onUnmounted, nextTick } from 'vue'
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
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
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
}
</style>
