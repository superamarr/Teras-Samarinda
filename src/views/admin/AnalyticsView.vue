<script setup>
import StatCard from '@/components/admin/StatCard.vue'
import { ref, computed, watch, onMounted } from 'vue'
import { analyticsService } from '@/api/analytics'

const selectedPeriod = ref('year')

const currentKpi = ref({
  pageViews: '0',
  pageViewsTrend: '0%',
  duration: '0m 0s',
  durationTrend: '0%',
  bounceRate: '0%',
  bounceRateTrend: '0%',
  booking: '0',
  bookingTrend: '0%',
})

const periodLabelText = computed(() => {
  switch (selectedPeriod.value) {
    case 'week': return 'dari minggu lalu'
    case 'month': return 'dari bulan lalu'
    case 'year': return 'dari tahun lalu'
    case 'all': return 'selama ini'
    default: return 'dari bulan lalu'
  }
})

const lineChartOptions = ref({
  chart: {
    type: 'area',
    toolbar: { show: false },
    fontFamily: 'Inter, sans-serif',
    animations: { enabled: true },
  },
  colors: ['#033D4A', '#10b981'],
  stroke: { curve: 'smooth', width: 3 },
  fill: {
    type: 'gradient',
    gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 90, 100] },
  },
  dataLabels: { enabled: false },
  xaxis: {
    categories: [],
    labels: { style: { colors: '#6b7280' } },
    title: { text: 'Periode', style: { color: '#6b7280' } },
  },
  yaxis: {
    labels: { style: { colors: '#6b7280' } },
    title: { text: 'Jumlah', style: { color: '#6b7280' } },
  },
  tooltip: { theme: 'dark', shared: true, intersect: false },
  grid: { borderColor: '#f0f0f0' },
  legend: { position: 'top', horizontalAlign: 'left' },
  markers: { size: 0 },
})
const lineSeries = ref([
  { name: 'Page Views', data: [] },
  { name: 'Booking', data: [] },
])

const donutContentOptions = ref({
  chart: { type: 'donut', fontFamily: 'Inter, sans-serif' },
  colors: ['#033D4A', '#0791B0', '#06b6d4', '#67e8f9', '#10b981', '#f59e0b'],
  labels: [],
  legend: { position: 'bottom', labels: { colors: '#6b7280' } },
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        labels: {
          show: true,
          total: {
            show: true,
            label: 'Total',
            color: '#1a202c',
            fontSize: '14px',
            fontWeight: 600,
          },
        },
      },
    },
  },
  dataLabels: { enabled: false },
  tooltip: { theme: 'dark' },
})
const donutContentSeries = ref([])

const donutBookingOptions = ref({
  chart: { type: 'donut', fontFamily: 'Inter, sans-serif' },
  colors: ['#22c55e', '#3b82f6', '#ef4444'],
  labels: [],
  legend: { position: 'bottom', labels: { colors: '#6b7280' } },
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        labels: {
          show: true,
          total: {
            show: true,
            label: 'Total',
            color: '#1a202c',
            fontSize: '14px',
            fontWeight: 600,
          },
        },
      },
    },
  },
  dataLabels: { enabled: false },
  tooltip: { theme: 'dark' },
})
const donutBookingSeries = ref([])

const horizontalBarOptions = ref({
  chart: {
    type: 'bar',
    toolbar: { show: false },
    fontFamily: 'Inter, sans-serif',
    animations: { enabled: true },
  },
  colors: ['#033D4A'],
  plotOptions: { bar: { borderRadius: 4, horizontal: true, barHeight: '60%' } },
  dataLabels: { enabled: false },
  xaxis: {
    categories: [],
    labels: { style: { colors: '#6b7280' } },
    title: { text: 'Jumlah Views', style: { color: '#6b7280' } },
  },
  yaxis: { labels: { style: { colors: '#6b7280', fontSize: '12px' } } },
  tooltip: { theme: 'dark', y: { formatter: (val) => `${val.toLocaleString('id-ID')} Views` } },
  grid: { borderColor: '#f0f0f0' },
})
const horizontalSeries = ref([{ name: 'Views', data: [] }])

const bookingAreaOptions = ref({
  chart: { type: 'area', toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
  colors: ['#0791B0'],
  stroke: { curve: 'smooth', width: 3 },
  fill: {
    type: 'gradient',
    gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 90, 100] },
  },
  dataLabels: { enabled: false },
  xaxis: { categories: [], labels: { style: { colors: '#6b7280' } } },
  yaxis: { labels: { style: { colors: '#6b7280' } } },
  tooltip: { theme: 'dark', y: { formatter: (val) => `${val} Booking` } },
  grid: { borderColor: '#f0f0f0' },
})
const bookingAreaSeries = ref([{ name: 'Booking', data: [] }])
const topContentData = ref([])

const updateCharts = async (period) => {
  try {
    const res = await analyticsService.getOverview(period)
    if (res.data.success) {
      const d = res.data.data
      currentKpi.value = d.kpiData

      lineChartOptions.value = {
        ...lineChartOptions.value,
        xaxis: { ...lineChartOptions.value.xaxis, categories: d.chartData.categories },
      }
      lineSeries.value = [
        { name: 'Page Views', data: d.chartData.pageViews },
        { name: 'Booking', data: d.chartData.booking },
      ]

      bookingAreaOptions.value = {
        ...bookingAreaOptions.value,
        xaxis: { ...bookingAreaOptions.value.xaxis, categories: d.chartData.categories },
      }
      bookingAreaSeries.value = [{ name: 'Booking', data: d.chartData.booking }]

      horizontalBarOptions.value = {
        ...horizontalBarOptions.value,
        xaxis: { ...horizontalBarOptions.value.xaxis, categories: d.contentData.sections },
      }
      horizontalSeries.value = [{ name: 'Views', data: d.contentData.views }]

      donutContentOptions.value = {
        ...donutContentOptions.value,
        labels: d.donutContentData.labels,
      }
      donutContentSeries.value = d.donutContentData.series

      donutBookingOptions.value = {
        ...donutBookingOptions.value,
        labels: d.donutBookingData.labels,
      }
      donutBookingSeries.value = d.donutBookingData.series

      topContentData.value = d.topContentData
    }
  } catch (err) {
    console.error('Failed to update charts', err)
  }
}

watch(selectedPeriod, (newVal) => {
  updateCharts(newVal)
})

onMounted(() => {
  updateCharts(selectedPeriod.value)
})
</script>

<template>
  <div class="analytics-view">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold mb-0 dashboard-title">Laporan Analytics</h2>
      <select v-model="selectedPeriod" class="chart-filter-select">
        <option value="all">Semua Waktu</option>
        <option value="year">Tahun Ini</option>
        <option value="month">Bulan Ini</option>
        <option value="week">Minggu Ini</option>
      </select>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-5">
      <div class="col-md-3">
        <StatCard
          title="Total Page Views"
          :value="currentKpi.pageViews"
          :trend="currentKpi.pageViewsTrend"
          icon="bi-eye-fill"
          :periodLabel="periodLabelText"
        />
      </div>
      <div class="col-md-3">
        <StatCard
          title="Rata-rata Durasi"
          :value="currentKpi.duration"
          :trend="currentKpi.durationTrend"
          icon="bi-clock-fill"
          :periodLabel="periodLabelText"
        />
      </div>
      <div class="col-md-3">
        <StatCard
          title="Bounce Rate"
          :value="currentKpi.bounceRate"
          :trend="currentKpi.bounceRateTrend"
          icon="bi-graph-up-arrow"
          :periodLabel="periodLabelText"
        />
      </div>
      <div class="col-md-3">
        <StatCard
          title="Total Booking"
          :value="currentKpi.booking"
          :trend="currentKpi.bookingTrend"
          icon="bi-ticket-perforated-fill"
          :periodLabel="periodLabelText"
        />
      </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row g-4 mb-5">
      <!-- Mixed Chart: Page Views + Booking -->
      <div class="col-lg-8">
        <div class="chart-card bg-white p-4 rounded-4 shadow-sm border h-100">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="icon-box-small rounded-2 d-flex align-items-center justify-content-center">
              <i class="bi bi-graph-up"></i>
            </div>
            <h5 class="mb-0 fw-bold">Tren Analytics</h5>
          </div>
          <apexchart
            :key="'line-' + selectedPeriod"
            type="area"
            height="400"
            :options="lineChartOptions"
            :series="lineSeries"
          />
        </div>
      </div>

      <!-- Donut Chart: Proporsi Konten -->
      <div class="col-lg-4">
        <div class="chart-card bg-white p-4 rounded-4 shadow-sm border h-100">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="icon-box-small rounded-2 d-flex align-items-center justify-content-center">
              <i class="bi bi-pie-chart-fill"></i>
            </div>
            <h5 class="mb-0 fw-bold">Proporsi Konten</h5>
          </div>
          <apexchart
            :key="'content-' + selectedPeriod"
            type="donut"
            height="350"
            :options="donutContentOptions"
            :series="donutContentSeries"
          />
        </div>
      </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-4 mb-5">
      <!-- Booking Trend Area Chart -->
      <div class="col-lg-6">
        <div class="chart-card bg-white p-4 rounded-4 shadow-sm border h-100">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="icon-box-small rounded-2 d-flex align-items-center justify-content-center">
              <i class="bi bi-calendar-check"></i>
            </div>
            <h5 class="mb-0 fw-bold">Booking Trend</h5>
          </div>
          <apexchart
            :key="'booking-' + selectedPeriod"
            type="area"
            height="280"
            :options="bookingAreaOptions"
            :series="bookingAreaSeries"
          />
        </div>
      </div>

      <!-- Donut Chart: Booking Status -->
      <div class="col-lg-6">
        <div class="chart-card bg-white p-4 rounded-4 shadow-sm border h-100">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="icon-box-small rounded-2 d-flex align-items-center justify-content-center">
              <i class="bi bi-pie-chart"></i>
            </div>
            <h5 class="mb-0 fw-bold">Status Booking</h5>
          </div>
          <apexchart
            :key="'booking-status-' + selectedPeriod"
            type="donut"
            height="280"
            :options="donutBookingOptions"
            :series="donutBookingSeries"
          />
        </div>
      </div>
    </div>

    <!-- Charts Row 3 -->
    <div class="row g-4 mb-5">
      <!-- Horizontal Bar Chart: Performa Section -->
      <div class="col-12">
        <div class="chart-card bg-white p-4 rounded-4 shadow-sm border h-100">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="icon-box-small rounded-2 d-flex align-items-center justify-content-center">
              <i class="bi bi-bar-chart-fill"></i>
            </div>
            <h5 class="mb-0 fw-bold">Performa Kunjungan Halaman</h5>
          </div>
          <apexchart
            :key="'horizontal-' + selectedPeriod"
            type="bar"
            height="300"
            :options="horizontalBarOptions"
            :series="horizontalSeries"
          />
        </div>
      </div>
    </div>

    <!-- Top Content Table -->
        <div class="bg-white rounded-4 shadow-sm border overflow-hidden mt-2">
          <div class="p-4 border-bottom bg-light d-flex align-items-center gap-3">
            <div class="icon-box-small rounded-2 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary">
              <i class="bi bi-bar-chart-line-fill"></i>
            </div>
            <h5 class="mb-0 fw-bold">Konten Paling Banyak Dilihat (Top Konten)</h5>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light border-bottom">
                <tr>
                  <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">Nama Konten</th>
                  <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">Jenis</th>
                  <th class="px-4 py-3 text-secondary small fw-bold text-uppercase text-center">Views</th>
                  <th class="px-4 py-3 text-secondary small fw-bold text-uppercase text-center">Avg Time</th>
                  <th class="px-4 py-3 text-secondary small fw-bold text-uppercase text-end">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in topContentData" :key="index" class="border-bottom">
                  <td class="px-4 py-3 fw-bold text-dark">{{ item.name }}</td>
                  <td class="px-4 py-3">
                    <span class="badge px-3 py-2 rounded-bill bg-light text-dark border fw-normal">
                      <i class="bi bi-tag me-1 text-primary"></i>
                      {{ item.type }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center fw-semibold">{{ item.views.toLocaleString('id-ID') }}</td>
                  <td class="px-4 py-3 text-center text-secondary font-monospace">{{ item.avgTime }}</td>
                  <td class="px-4 py-3 text-end">
                    <span class="badge px-3 py-2 rounded-bill bg-success-subtle text-success">
                      <i class="bi bi-check-circle me-1"></i>
                      {{ item.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
  </div>
</template>

<style scoped>
.analytics-view {
  padding: 0;
}
.dashboard-title {
  color: #1a202c;
}
.chart-card {
  border-color: #f0f0f0 !important;
}
.icon-box-small {
  width: 36px;
  height: 36px;
  background-color: #f0f7f9;
  color: #033d4a;
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
.table thead th {
  border-bottom: 2px solid #f0f0f0;
}
.table tbody tr:hover {
  background-color: #f8fafb;
}
.border-bottom {
  border-color: #f0f0f0 !important;
}
.bg-success-subtle {
  background-color: #d1fae5;
}
.text-success {
  color: #059669 !important;
}
</style>
