<script setup>
defineProps({
  icon: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  },
  value: {
    type: String,
    required: true
  }
})
</script>

<template>
  <div class="contact-info-item d-flex align-items-start gap-3 mb-4">
    <div class="icon-wrapper d-flex justify-content-center align-items-center flex-shrink-0">
      <!-- We expect icon to be an SVG path or similar, or just a slot -->
      <span v-html="icon"></span>
    </div>
    <div class="info-content">
      <h6 class="info-label mb-1">{{ label }}</h6>
      <p class="info-value mb-0">{{ value }}</p>
    </div>
  </div>
</template>

<style scoped>
.icon-wrapper {
  width: 40px;
  height: 40px;
  background-color: #e9f2f5;
  border-radius: 50%;
  color: #083b4c;
}

.info-label {
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  font-size: 1rem;
  color: #1a1a1a;
}

.info-value {
  font-family: 'Inter', sans-serif;
  font-size: 0.95rem;
  color: #555;
  line-height: 1.5;
}

:deep(svg) {
  width: 20px;
  height: 20px;
}
</style>
