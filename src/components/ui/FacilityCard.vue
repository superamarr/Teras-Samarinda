<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  image: {
    type: String,
    required: true,
  },
})
</script>

<template>
  <div class="facility-card">
    <img :src="image" :alt="title" class="facility-image" loading="lazy" />
    <div class="facility-overlay"></div>
    <div class="facility-title">{{ title }}</div>
  </div>
</template>

<style scoped>
.facility-card {
  position: relative;
  width: 100%;
  aspect-ratio: 4 / 4;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.facility-card:hover {
  transform: translateY(-5px);
}

.facility-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.facility-card:hover .facility-image {
  transform: scale(1.05);
}

.facility-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 50%;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, transparent 100%);
  pointer-events: none;
}

.facility-title {
  position: absolute;
  bottom: 24px;
  left: 24px;
  color: white;
  font-family: 'Inter', sans-serif;
  font-size: 1.125rem;
  font-weight: 500;
  z-index: 2;
}
</style>
