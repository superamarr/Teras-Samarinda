<script setup>
import { computed } from 'vue'

const props = defineProps({
  text: {
    type: String,
    required: true
  },
  href: {
    type: String,
    default: '#'
  },
  variant: {
    type: String,
    default: 'light', // 'light' atau 'dark'
    validator: (value) => ['light', 'dark'].includes(value)
  },
  gap: {
    type: String,
    default: '12px'
  }
})

const buttonClass = computed(() => {
  return props.variant === 'dark' ? 'btn-dark-custom' : 'btn-light-custom'
})
</script>

<template>
  <div class="action-buttons-container d-inline-flex align-items-center" :style="{ gap: gap }">
    <a :href="href" :class="['btn rounded-pill text-decoration-none px-4 py-2 d-flex align-items-center', buttonClass]" style="height: 48px;">
      <span class="btn-text fw-bold">{{ text }}</span>
    </a>
    <a :href="href" :class="['btn rounded-circle d-flex justify-content-center align-items-center p-0', buttonClass]" style="width: 48px; height: 48px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <line x1="7" y1="17" x2="17" y2="7"></line>
        <polyline points="7 7 17 7 17 17"></polyline>
      </svg>
    </a>
  </div>
</template>

<style scoped>
/* Transisi Dasar */
.btn {
  transition: all 0.3s ease;
  font-family: 'Inter', sans-serif;
  border: none;
}

.btn:active {
  transform: translateY(1px);
}

.btn-text {
  font-size: 0.85rem;
  letter-spacing: 0.8px;
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
</style>
