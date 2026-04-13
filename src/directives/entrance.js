import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

/**
 * v-entrance directive
 *
 * Creates an entrance animation with blur and slide effects from left to right.
 * Only runs once when element enters viewport.
 *
 * Usage:
 * <h2 v-entrance>Title</h2>
 * <p v-entrance="{ x: -40, blur: 10, delay: 200 }">Description</p>
 *
 * Default values:
 * - x: -40 (slide from left)
 * - blur: 10 (blur effect)
 * - opacity: 0 (fade in)
 * - duration: 0.8 (animation duration in seconds)
 * - delay: 0 (delay in milliseconds)
 * - trigger: null (uses parent element with entrance-section class, or element itself)
 * - start: 'top 80%' (when animation starts)
 * - ease: 'power2.out' (easing function)
 */

const defaultOptions = {
  x: -40,
  blur: 10,
  opacity: 0,
  duration: 0.8,
  delay: 0,
  trigger: null,
  start: 'top 80%',
  ease: 'power2.out',
}

const entranceDirective = {
  mounted(el, binding) {
    // Merge default options with binding value
    const options = { ...defaultOptions, ...binding.value }

    // Initial state
    gsap.set(el, {
      opacity: options.opacity,
      x: options.x,
      filter: `blur(${options.blur}px)`,
    })

    // Find trigger element (look for parent with entrance-section class)
    let triggerElement = el
    if (options.trigger) {
      triggerElement = options.trigger
    } else {
      // Try to find parent section with entrance-section class
      let parent = el.parentElement
      while (parent) {
        if (parent.classList && parent.classList.contains('entrance-section')) {
          triggerElement = parent
          break
        }
        parent = parent.parentElement
      }
    }

    // Create ScrollTrigger
    ScrollTrigger.create({
      trigger: triggerElement,
      start: options.start,
      onEnter: () => {
        gsap.to(el, {
          opacity: 1,
          x: 0,
          filter: 'blur(0px)',
          duration: options.duration,
          delay: options.delay / 1000, // Convert ms to seconds
          ease: options.ease,
        })
      },
      once: true,
    })
  },
}

export default entranceDirective
