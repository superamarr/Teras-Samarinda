<script setup>
defineProps({
  image: {
    type: String,
    required: true,
  },
  date: {
    type: String,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    required: true,
  },
  link: {
    type: String,
    default: '#',
  },
})
</script>

<template>
  <div class="event-card">
    <div class="event-image-wrapper">
      <img :src="image" :alt="title" class="event-image" />

      <div class="event-content">
        <p class="event-date mb-2">{{ date }}</p>
        <h3 class="event-title mb-3">{{ title }}</h3>
        <p class="event-description mb-4">{{ description }}</p>

        <router-link :to="link" class="event-link d-flex align-items-center text-decoration-none">
          <span class="me-2">Lihat Detail</span>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            fill="currentColor"
            viewBox="0 0 16 16"
          >
            <path
              fill-rule="evenodd"
              d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"
            />
          </svg>
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.event-card {
  width: 100%;
  border-radius: 16px;
  overflow: hidden;
  position: relative;
  transition: transform 0.3s ease;
}

.event-card:hover {
  transform: translateY(-5px);
}

.event-image-wrapper {
  position: relative;
  width: 100%;
  aspect-ratio: 1/1;
  border-radius: 16px;
  overflow: hidden;
}

.event-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Glassmorphism Content Panel */
.event-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 2rem;

  /* Glassmorphism Effect - Light Glass */
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(18px);
  -webkit-backdrop-filter: blur(18px);
  border-top: 1px solid rgba(248, 248, 248, 0.04);

  color: #ffffff;
}

.event-date {
  font-family: 'Inter', sans-serif;
  font-size: 0.9rem;
  opacity: 0.85;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.event-title {
  font-family: 'Inter', sans-serif;
  font-size: 1.5rem;
  font-weight: 600;
  line-height: 1.2;
  color: #ffffff;
  text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.event-description {
  font-family: 'Inter', sans-serif;
  font-size: 0.95rem;
  line-height: 1.5;
  opacity: 0.9;
  color: #ffffff;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.event-link {
  color: #ffffff;
  font-family: 'Inter', sans-serif;
  font-size: 0.9rem;
  font-weight: 500;
  border-bottom: 1px solid transparent;
  width: fit-content;
  transition: all 0.3s ease;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.event-link:hover {
  border-bottom: 1px solid #ffffff;
  opacity: 1;
}

.event-link svg {
  transition: transform 0.3s ease;
}

.event-link:hover svg {
  transform: translateX(5px);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .event-content {
    padding: 1.5rem;
  }

  .event-title {
    font-size: 1.25rem;
  }
}
</style>
