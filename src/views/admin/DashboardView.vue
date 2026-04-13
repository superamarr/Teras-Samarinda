<script setup>
import StatCard from '@/components/admin/StatCard.vue'
import { ref, computed, onMounted } from 'vue'
import { activityService } from '@/api/activities'
import { eventService } from '@/api/events'
import { facilityService } from '@/api/facilities'
import { bookingService } from '@/api/bookings'
import { analyticsService } from '@/api/analytics'
import { activityLogService } from '@/api/activityLogs'

const getGreeting = () => {
  const hour = new Date().getHours()
  if (hour < 12) return 'Pagi'
  if (hour < 18) return 'Siang'
  return 'Sore'
}

const greeting = computed(() => getGreeting())

const stats = ref({
  totalBookings: 0,
  bookingsTrend: '0%',
  totalEvents: 0,
  eventsTrend: '0%',
  totalFacilities: 0,
  facilitiesTrend: '0%',
  totalActivities: 0,
})

const isLoading = ref(true)
const recentActivities = ref([])
const activityListRef = ref(null)
const currentIndex = ref(0)
const isScrolling = ref(false)

const formatTimeAgo = (dateString) => {
  const now = new Date()
  const date = new Date(dateString)
  const diffInSeconds = Math.floor((now - date) / 1000)

  if (diffInSeconds < 60) return 'Baru saja'
  if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} menit yang lalu`
  if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} jam yang lalu`
  if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} hari yang lalu`
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
}

const mapActionType = (actionType) => {
  const typeMap = {
    create: 'add',
    update: 'edit',
    delete: 'delete',
    login: 'login',
    logout: 'logout',
  }
  return typeMap[actionType] || 'edit'
}

const loadStats = async () => {
  try {
    const [activitiesRes, eventsRes, facilitiesRes, bookingsRes, analyticsRes, logsRes] =
      await Promise.all([
        activityService.getAll(),
        eventService.getAll(),
        facilityService.getAll(),
        bookingService.getAll(),
        analyticsService.getOverview('month'),
        activityLogService.getRecent(10),
      ])

    stats.value = {
      totalBookings: bookingsRes.data.data?.length || 0,
      totalEvents: eventsRes.data.data?.length || 0,
      totalFacilities: facilitiesRes.data.data?.length || 0,
      totalActivities: activitiesRes.data.data?.length || 0,
    }

    if (analyticsRes.data.success) {
      const cData = analyticsRes.data.data.chartData
      const kpiData = analyticsRes.data.data.kpiData

      stats.value.bookingsTrend = kpiData.bookingTrend
      stats.value.eventsTrend = kpiData.eventsTrend
      stats.value.facilitiesTrend = kpiData.facilitiesTrend

      visitorsChartOptions.value = {
        ...visitorsChartOptions.value,
        xaxis: { categories: cData.categories },
      }
      pageViewsSeries.value = [{ name: 'Page Views', data: cData.pageViews }]

      eventsChartOptions.value = {
        ...eventsChartOptions.value,
        xaxis: { categories: cData.categories },
      }
      bookingSeries.value = [{ name: 'Booking', data: cData.booking }]
    }

    if (logsRes.data.success && logsRes.data.data) {
      recentActivities.value = logsRes.data.data.map((log) => ({
        type: mapActionType(log.action_type),
        message: log.description,
        time: formatTimeAgo(log.created_at),
      }))
    }
  } catch (error) {
    console.error('Failed to load stats:', error)
  } finally {
    isLoading.value = false
  }
}
const pageViewsSeries = ref([{ name: 'Page Views', data: [] }])
const bookingSeries = ref([{ name: 'Booking', data: [] }])

const visitorsChartOptions = ref({
  chart: {
    type: 'area',
    toolbar: { show: false },
    fontFamily: 'Inter, sans-serif',
    zoom: { enabled: true },
    animations: { enabled: true, easing: 'easeinout', speed: 500 },
  },
  colors: ['#033D4A'],
  stroke: { curve: 'smooth', width: 3 },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.4,
      opacityTo: 0.1,
      stops: [0, 90, 100],
    },
  },
  dataLabels: { enabled: false },
  xaxis: {
    categories: [],
    labels: { style: { colors: '#6b7280' } },
  },
  yaxis: {
    labels: { style: { colors: '#6b7280' } },
  },
  tooltip: {
    theme: 'dark',
    x: { show: true },
    y: { formatter: (val) => `${val} Views` },
  },
  grid: { borderColor: '#f0f0f0' },
  states: {
    hover: {
      filter: { type: 'lighten', value: 0.1 },
    },
    active: {
      filter: { type: 'darken', value: 0.1 },
    },
  },
})

const eventsChartOptions = ref({
  chart: {
    type: 'bar',
    toolbar: { show: false },
    fontFamily: 'Inter, sans-serif',
    animations: { enabled: true, easing: 'easeinout', speed: 500 },
    group: 'dashboard-charts',
  },
  colors: ['#4A8594'],
  states: {
    normal: { filter: { type: 'none', value: 0 } },
    hover: { filter: { type: 'none', value: 0 } },
    active: { filter: { type: 'none', value: 0 } },
  },
  plotOptions: {
    bar: { borderRadius: 6, columnWidth: '50%' },
  },
  fill: { type: 'solid', opacity: 1 },
  dataLabels: { enabled: false },
  xaxis: {
    categories: [],
    labels: { style: { colors: '#6b7280' } },
  },
  yaxis: {
    labels: { style: { colors: '#6b7280' } },
  },
  tooltip: {
    theme: 'dark',
    y: { formatter: (val) => `${val} Booking` },
  },
  grid: { borderColor: '#f0f0f0' },
})

const getMonthLabel = () => {
  const now = new Date()
  const year = now.getFullYear()
  const month = now.toLocaleString('id-ID', { month: 'long' })
  const startOfMonth = new Date(year, now.getMonth(), 1).toLocaleString('id-ID', {
    day: 'numeric',
    month: 'long',
  })
  return `${startOfMonth} - ${month} ${year}`
}

const getActivityIcon = (type) => {
  const icons = {
    edit: 'bi-pencil-fill',
    add: 'bi-plus-lg',
    gallery: 'bi-images',
    delete: 'bi-trash-fill',
  }
  return icons[type] || 'bi-circle-fill'
}

const handleWheel = (event) => {
  if (!activityListRef.value || isScrolling.value || recentActivities.value.length === 0) return

  event.preventDefault()

  const direction = event.deltaY > 0 ? 1 : -1
  const newIndex = Math.max(
    0,
    Math.min(recentActivities.value.length - 1, currentIndex.value + direction),
  )

  if (newIndex !== currentIndex.value) {
    isScrolling.value = true
    currentIndex.value = newIndex

    const items = activityListRef.value.querySelectorAll('.activity-item')
    if (items && items[newIndex]) {
      const container = activityListRef.value
      const item = items[newIndex]
      const offsetTop = item.offsetTop - container.offsetTop

      container.scrollTo({
        top: offsetTop,
        behavior: 'smooth',
      })
    }

    setTimeout(() => {
      isScrolling.value = false
    }, 400)
  }
}

onMounted(() => {
  loadStats()
})
</script>

<template>
  <div class="dashboard-view">
    <h2 class="fw-bold mb-3 mb-md-4 dashboard-title fs-3 fs-md-2">
      Selamat {{ greeting }}, Admin!
    </h2>

    <!-- Stats Grid -->
    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else class="row g-3 g-md-4 mb-4 mb-md-5">
      <div class="col-md-4">
        <StatCard
          title="Total Booking"
          :value="stats.totalBookings"
          :trend="stats.bookingsTrend"
          icon="bi-ticket-perforated-fill"
        />
      </div>
      <div class="col-md-4">
        <StatCard
          title="Total Event"
          :value="stats.totalEvents"
          :trend="stats.eventsTrend"
          icon="bi-calendar-check-fill"
        />
      </div>
      <div class="col-md-4">
        <StatCard
          title="Total Fasilitas"
          :value="stats.totalFacilities"
          :trend="stats.facilitiesTrend"
          icon="bi-building"
        />
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 g-md-4 mb-4 mb-md-5">
      <!-- Chart 1: Booking -->
      <div class="col-lg-7">
        <div class="chart-card bg-white p-3 p-md-4 rounded-4 shadow-sm border h-100">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
              <div
                class="icon-box-small rounded-2 d-flex align-items-center justify-content-center"
              >
                <i class="bi bi-people-fill"></i>
              </div>
              <h5 class="mb-0 fw-bold">Page Views</h5>
            </div>
          </div>
          <p class="text-secondary small mb-4">{{ getMonthLabel() }}</p>

          <!-- Line Chart -->
          <apexchart
            type="area"
            height="380"
            :options="visitorsChartOptions"
            :series="pageViewsSeries"
          />
        </div>
      </div>

      <!-- Chart 2: Events -->
      <div class="col-lg-5">
        <div class="chart-card bg-white p-3 p-md-4 rounded-4 shadow-sm border h-100">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
              <div
                class="icon-box-small rounded-2 d-flex align-items-center justify-content-center"
              >
                <i class="bi bi-calendar-event-fill"></i>
              </div>
              <h5 class="mb-0 fw-bold">Booking</h5>
            </div>
          </div>
          <p class="text-secondary small mb-4">{{ getMonthLabel() }}</p>

          <!-- Bar Chart -->
          <apexchart
            type="bar"
            height="350"
            :options="eventsChartOptions"
            :series="bookingSeries"
          />
        </div>
      </div>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="row g-3 g-md-4">
      <div class="col-12">
        <div class="bg-white p-3 p-md-4 rounded-4 shadow-sm border h-100">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="icon-box-small rounded-2 d-flex align-items-center justify-content-center">
              <i class="bi bi-clock-history"></i>
            </div>
            <h5 class="mb-0 fw-bold">Aktivitas Terbaru</h5>
          </div>
          <div ref="activityListRef" class="activity-list" @wheel.prevent="handleWheel">
            <div
              v-for="(activity, index) in recentActivities"
              :key="index"
              class="activity-item d-flex align-items-start gap-3"
            >
              <div
                class="activity-icon bg-light-subtle rounded-circle d-flex align-items-center justify-content-center"
              >
                <i :class="['bi', getActivityIcon(activity.type), 'text-dark small']"></i>
              </div>
              <div>
                <p class="mb-0 small fw-medium">{{ activity.message }}</p>
                <span class="text-secondary small">{{ activity.time }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard-title {
  color: #1a202c;
}

.chart-card,
.gallery-section {
  border-color: #f0f0f0 !important;
}

.icon-box-small {
  width: 36px;
  height: 36px;
  background-color: #f0f7f9;
  color: #033d4a;
}

.bg-light-soft {
  background-color: #f8fafb;
}

.pt-75 {
  padding-top: 75%;
}

.activity-icon {
  width: 36px;
  height: 36px;
  flex-shrink: 0;
  background-color: #f0f7f9;
}

.activity-icon i {
  color: #033d4a;
}

.activity-list {
  max-height: 320px;
  overflow-y: hidden;
  padding-right: 8px;
}

.activity-list::-webkit-scrollbar {
  width: 6px;
}

.activity-list::-webkit-scrollbar-track {
  background: #f0f0f0;
  border-radius: 3px;
}

.activity-list::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}

.activity-list::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

.activity-item {
  padding: 12px 8px;
  border-radius: 8px;
  transition: background-color 0.2s ease;
  min-height: 60px;
}

.activity-item:hover {
  background-color: #f8fafb;
}

.activity-item + .activity-item {
  border-top: 1px solid #f0f0f0;
}

.chart-filter-select {
  background-color: #f0f7f9;
  border: none;
  padding: 0.5rem 2rem 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 600;
  color: #033d4a;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23033d4a' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 0.75rem center;
}

.chart-filter-select:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(3, 61, 74, 0.2);
}

.chart-filter-select option {
  background-color: #fff;
  color: #1a202c;
  padding: 0.5rem;
}

@media (max-width: 991px) {
  .chart-placeholder {
    height: 200px;
  }
}
</style>
