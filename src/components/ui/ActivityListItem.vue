<script setup>
defineProps({
  text: {
    type: String,
    required: true,
  },
  isActive: {
    type: Boolean,
    default: false,
  },
  isRevealed: {
    type: Boolean,
    default: false,
  },
})
</script>

<template>
  <div
    :class="[
      'activity-item border-bottom py-3',
      isActive ? 'active-item' : 'inactive-item',
      isRevealed ? 'revealed' : 'not-revealed',
    ]"
  >
    <h3 class="activity-text mb-0">{{ text }}</h3>
  </div>
</template>

<style scoped>
.activity-item {
  border-bottom-color: #cccccc !important;
  cursor: pointer;
  transition: all 0.3s ease;
}

.activity-text {
  font-family: 'Instrument Serif', serif;
  font-size: clamp(1.75rem, 2.5vw, 2.5rem);
  font-weight: 400;
  letter-spacing: -0.02em;
}

/* State Aktif (Hitam Tajam) */
.active-item .activity-text {
  color: #1a1a1a;
}

/* State Tidak Aktif (Abu-abu Redup/Glassy) */
.inactive-item .activity-text {
  color: #9e9e9e;
}

.inactive-item:hover .activity-text {
  color: #666666;
  transform: translateX(5px);
}

/* Scroll Reveal Animation */
.not-revealed {
  opacity: 0;
  transform: translateY(20px);
}

.revealed {
  opacity: 1;
  transform: translateY(0);
}
</style>
