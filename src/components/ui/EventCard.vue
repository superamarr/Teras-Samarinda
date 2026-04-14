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

      <div class="event-content glass-card">
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
  position: relative;
}

.event-image-wrapper {
  position: relative;
  width: 100%;
  aspect-ratio: 4 / 4;
  border-radius: 16px;
  overflow: hidden;
}

.event-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.event-card:hover .event-image {
  transform: scale(1.05);
}

/* Content Panel - Forced Full Width */
.event-content {
  position: absolute;
  bottom: -1px;
  left: -1px;
  right: -1px;
  width: calc(100% + 2px);
  padding: 1rem 2rem;
  color: #ffffff;
  z-index: 2;

  /* Reinforced Glassmorphism */
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(12px) saturate(1.15);
  -webkit-backdrop-filter: blur(12px) saturate(1.15);
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
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
@media (max-width: 992px) {
  .event-image-wrapper {
    border-radius: 20px;
  }

  .event-content {
    padding: 1.25rem 1.75rem;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
  }
}

@media (max-width: 768px) {
  .event-image-wrapper {
    border-radius: 16px;
  }

  .event-content {
    padding: 1rem 1.5rem;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
  }

  .event-title {
    font-size: 1.25rem;
  }

  .event-description {
    font-size: 0.875rem;
  }

  .event-date {
    font-size: 0.8rem;
  }

  .event-link {
    font-size: 0.8rem;
  }
}

@media (max-width: 576px) {
  .event-image-wrapper {
    border-radius: 12px;
  }

  .event-content {
    padding: 0.875rem 1.25rem;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
  }

  .event-title {
    font-size: 1.1rem;
    margin-bottom: 0.5rem !important;
  }

  .event-description {
    font-size: 0.8rem;
    margin-bottom: 0.75rem !important;
  }

  .event-date {
    font-size: 0.75rem;
    margin-bottom: 0.25rem !important;
  }

  .event-link {
    font-size: 0.75rem;
  }
}
</style>
