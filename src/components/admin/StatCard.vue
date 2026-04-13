<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: String,
  value: [String, Number],
  trend: String,
  icon: String,
  periodLabel: {
    type: String,
    default: 'from last month'
  }
})

const trendValue = computed(() => {
  if (!props.trend) return null
  const match = props.trend.match(/([+-]?\d+)%?/)
  if (!match) return null
  return parseInt(match[1])
})

const trendType = computed(() => {
  if (trendValue.value === null || trendValue.value === 0) return null
  return trendValue.value > 0 ? 'positive' : 'negative'
})

const showTrend = computed(() => {
  return trendValue.value !== null && trendValue.value !== 0
})
</script>

<template>
  <div class="stat-card bg-white p-4 rounded-4 shadow-sm border h-100">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div
        class="icon-box d-flex align-items-center justify-content-center rounded-3 bg-light-soft"
      >
        <i :class="['bi fs-4', icon]"></i>
      </div>
      <div class="dropdown">
        <i class="bi bi-three-dots text-secondary cursor-pointer"></i>
      </div>
    </div>

    <p class="text-secondary small fw-bold text-uppercase mb-2">{{ title }}</p>
    <div class="d-flex align-items-end gap-3">
      <h3 class="mb-0 fw-bold">{{ value }}</h3>
      <div
        v-if="showTrend"
        :class="[
          'trend-badge badge rounded-pill px-2 py-1',
          trendType === 'positive'
            ? 'bg-success-subtle text-success'
            : 'bg-danger-subtle text-danger',
        ]"
      >
        <span class="small fw-bold">{{ trend }}</span>
      </div>
    </div>
    <p class="text-secondary small mt-2 mb-0">{{ periodLabel }}</p>
  </div>
</template>

<style scoped>
.stat-card {
  border-color: #f0f0f0 !important;
}

.icon-box {
  width: 48px;
  height: 48px;
  background-color: #f0f7f9;
  color: #033d4a;
}

.bg-light-soft {
  background-color: #f8fafb;
}

.cursor-pointer {
  cursor: pointer;
}

.trend-badge {
  font-size: 0.75rem;
}
</style>
