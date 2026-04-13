<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  text: {
    type: String,
    required: true,
  },
  href: {
    type: String,
    default: '#',
  },
  variant: {
    type: String,
    default: 'light',
    validator: (value) => ['light', 'dark', 'outline'].includes(value),
  },
  gap: {
    type: String,
    default: '12px',
  },
})

const isHovered = ref(false)

const buttonClass = computed(() => {
  if (props.variant === 'dark') return 'btn-dark-custom'
  if (props.variant === 'outline') return 'btn-outline-custom'
  return 'btn-light-custom'
})

const containerStyle = computed(() => {
  const baseGap = parseInt(props.gap) || 12
  const hoverGap = baseGap + 8 // Tambah 8px saat hover
  return {
    gap: isHovered.value ? `${hoverGap}px` : props.gap,
  }
})
</script>

<template>
  <div
    class="action-buttons-container d-inline-flex align-items-center"
    :style="containerStyle"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <component
      :is="href.startsWith('/') ? 'router-link' : 'a'"
      v-bind="href.startsWith('/') ? { to: href } : { href: href }"
      :class="[
        'btn rounded-pill text-decoration-none px-4 py-2 d-flex align-items-center',
        buttonClass,
      ]"
      style="height: 48px"
    >
      <span class="btn-text fw-bold">{{ text }}</span>
    </component>
    <component
      :is="href.startsWith('/') ? 'router-link' : 'a'"
      v-bind="href.startsWith('/') ? { to: href } : { href: href }"
      :class="[
        'btn rounded-circle d-flex justify-content-center align-items-center p-0 arrow-btn',
        buttonClass,
      ]"
      style="width: 48px; height: 48px"
    >
      <svg
        class="arrow-icon"
        :class="{ 'arrow-horizontal': isHovered }"
        xmlns="http://www.w3.org/2000/svg"
        width="20"
        height="20"
        fill="none"
        stroke="currentColor"
        stroke-width="2.5"
        stroke-linecap="round"
        stroke-linejoin="round"
        viewBox="0 0 24 24"
      >
        <line x1="5" y1="12" x2="19" y2="12"></line>
        <polyline points="14 7 19 12 14 17"></polyline>
      </svg>
    </component>
  </div>
</template>

<style scoped>
/* Container Styles */
.action-buttons-container {
  transition: gap 0.3s ease;
}

/* Transisi Dasar */
.btn {
  transition: all 0.3s ease;
  font-family: var(--font-family-sans), sans-serif;
  border: none;
}

.btn:active {
  transform: translateY(1px);
}

.btn-text {
  font-size: 0.85rem;
  letter-spacing: 0.8px;
}

/* Arrow Button Styles */
.arrow-btn {
  position: relative;
}

.arrow-icon {
  transition: transform 0.3s ease;
  transform: rotate(-45deg);
}

.arrow-icon.arrow-horizontal {
  transform: rotate(0deg);
}

/* ================================================= */
/* VARIAN LIGHT (Digunakan di Hero Background Gelap) */
/* ================================================= */
.btn-light-custom {
  background-color: #ffffff;
  color: #1a1a1a;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.btn-light-custom:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
  background-color: #f8f9fa;
  color: #000000;
}

/* ================================================= */
/* VARIAN DARK (Digunakan di Background Terang)      */
/* ================================================= */
.btn-dark-custom {
  background-color: #083b4c; /* Warna dark blue sesuai gambar */
  color: #ffffff;
  box-shadow: 0 4px 15px rgba(8, 59, 76, 0.2);
}

.btn-dark-custom:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(8, 59, 76, 0.3);
  background-color: #062b3a;
  color: #ffffff;
}

/* ================================================= */
/* VARIAN OUTLINE (Secondary Button - Transparent)  */
/* ================================================= */
.btn-outline-custom {
  background-color: transparent;
  color: #ffffff;
  border: 2px solid #ffffff;
  box-shadow: none;
}

.btn-outline-custom:hover {
  transform: translateY(-2px);
  background-color: rgba(255, 255, 255, 0.1);
  box-shadow: 0 6px 20px rgba(255, 255, 255, 0.15);
  color: #ffffff;
  border-color: #ffffff;
}

/* ================================================= */
/* RESPONSIVE STYLES                                  */
/* ================================================= */
@media (max-width: 768px) {
  .btn {
    height: 44px !important;
    padding: 0.5rem 1rem !important;
  }

  .btn-text {
    font-size: 0.8rem;
    letter-spacing: 0.6px;
  }

  .arrow-btn {
    width: 44px !important;
    height: 44px !important;
  }

  .arrow-icon {
    width: 18px;
    height: 18px;
  }
}

@media (max-width: 576px) {
  .btn {
    height: 40px !important;
    padding: 0.4rem 0.875rem !important;
  }

  .btn-text {
    font-size: 0.75rem;
    letter-spacing: 0.5px;
  }

  .arrow-btn {
    width: 40px !important;
    height: 40px !important;
  }

  .arrow-icon {
    width: 16px;
    height: 16px;
  }
}
</style>
