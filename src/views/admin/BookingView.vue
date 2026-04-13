<script setup>
import { ref, computed, onMounted } from 'vue'
import { bookingService } from '@/api/bookings'
import { eventService } from '@/api/events'
import { useSwal } from '@/composables/useSwal'

const { success, error: showError, confirm, confirmDelete, toast } = useSwal()

// Filters & Data
const selectedFilter = ref('all')
const bookingData = ref([])
const events = ref([])
const isLoading = ref(true)

// Manual Entry Form State
const showModal = ref(false)
const isSaving = ref(false)
const newBooking = ref({
  name: '',
  email: '',
  phone: '',
  event_id: '',
  guest_count: 1,
  booking_date: '',
  notes: '',
  status: 'Confirmed' // Default for manual entry is usually confirmed
})

const loadData = async () => {
  isLoading.value = true
  try {
    const [bookingRes, eventRes] = await Promise.all([
      bookingService.getAll(),
      eventService.getAll()
    ])

    if (bookingRes.data.success) {
      bookingData.value = bookingRes.data.data || []
    }
    if (eventRes.data.success) {
      events.value = eventRes.data.data || []
    }
  } catch (error) {
    console.error('Failed to load data:', error)
    showError('Gagal!', 'Tidak dapat memuat data booking')
  } finally {
    isLoading.value = false
  }
}

const filteredBookings = computed(() => {
  if (selectedFilter.value === 'all') return bookingData.value
  return bookingData.value.filter((b) => b.status.toLowerCase() === selectedFilter.value.toLowerCase())
})

const stats = computed(() => {
  const total = bookingData.value.length
  const pending = bookingData.value.filter((b) => b.status.toLowerCase() === 'pending').length
  const confirmed = bookingData.value.filter((b) => b.status.toLowerCase() === 'confirmed').length
  const completed = bookingData.value.filter((b) => b.status.toLowerCase() === 'completed').length

  return [
    { title: 'Total Booking', value: total.toString(), icon: 'bi-ticket-perforated-fill', color: '#033d4a' },
    { title: 'Dibatalkan', value: bookingData.value.filter((b) => b.status.toLowerCase() === 'cancelled').length.toString(), icon: 'bi-x-circle-fill', color: '#dc3545' },
    { title: 'Confirmed', value: confirmed.toString(), icon: 'bi-check-circle-fill', color: '#2563eb' },
    { title: 'Completed', value: completed.toString(), icon: 'bi-check2-all', color: '#10b981' },
  ]
})

const getStatusBadge = (status) => {
  const s = status.toLowerCase()
  const badges = {
    pending: { class: 'bg-warning-subtle text-warning', icon: 'bi-clock', label: 'Pending' },
    confirmed: { class: 'bg-primary-subtle text-primary', icon: 'bi-check-circle', label: 'Confirmed' },
    completed: { class: 'bg-success-subtle text-success', icon: 'bi-check2-all', label: 'Completed' },
    cancelled: { class: 'bg-danger-subtle text-danger', icon: 'bi-x-circle', label: 'Cancelled' },
  }
  return badges[s] || badges.pending
}

const saveManualBooking = async () => {
  if (!newBooking.value.name || !newBooking.value.phone || !newBooking.value.booking_date) {
    toast('error', 'Mohon lengkapi data wajib (Nama, Telp, Tanggal)')
    return
  }

  isSaving.value = true
  try {
    const response = await bookingService.create(newBooking.value)
    if (response.data.success) {
      await success('Berhasil!', 'Booking manual berhasil dicatat')
      showModal.value = false
      resetForm()
      await loadData()
    } else {
      showError('Gagal!', response.data.message || 'Terjadi kesalahan')
    }
  } catch (error) {
    showError('Gagal!', 'Terjadi kesalahan saat menyambung ke server')
  } finally {
    isSaving.value = false
  }
}

const resetForm = () => {
  newBooking.value = {
    name: '',
    email: '',
    phone: '',
    event_name: '',
    location: '',
    guest_count: 1,
    booking_date: '',
    notes: '',
    status: 'Confirmed'
  }
}

const deleteBooking = async (id) => {
  const result = await confirmDelete('catatan booking ini')
  if (!result.isConfirmed) return
  
  try {
    await bookingService.delete(id)
    bookingData.value = bookingData.value.filter((b) => b.id !== id)
    toast('success', 'Booking berhasil dihapus')
  } catch (error) {
    showError('Gagal!', 'Tidak dapat menghapus booking')
  }
}

const updateStatus = async (id, status) => {
  let actionText = '';
  switch(status.toLowerCase()) {
    case 'confirmed': actionText = 'mengonfirmasi penyewaan ini'; break;
    case 'completed': actionText = 'menandai penyewaan ini selesai'; break;
    case 'cancelled': actionText = 'membatalkan penyewaan ini'; break;
    default: actionText = 'memperbarui status'; break;
  }
  
  const result = await confirm('Konfirmasi', `Apakah Anda yakin ingin ${actionText}?`)
  if (!result.isConfirmed) return

  try {
    await bookingService.update(id, { status })
    const booking = bookingData.value.find((b) => b.id === id)
    if (booking) booking.status = status
    toast('success', `Status diperbarui ke ${status}`)
  } catch (error) {
    showError('Gagal!', 'Gagal memperbarui status')
  }
}

onMounted(() => {
  loadData()
})
</script>

<template>
  <div class="booking-view container-fluid p-0">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border">
      <div>
        <h2 class="fw-bold mb-1 text-dark">Manajemen Booking Tempat</h2>
        <p class="text-secondary small mb-0">Kelola reservasi area/tempat untuk pelaksanaan event</p>
      </div>
      <button @click="showModal = true" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
        <i class="bi bi-plus-lg me-2"></i>Catat Sewa Tempat
      </button>
    </div>

    <!-- Stats Section -->
    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
    </div>

    <template v-else>
      <div class="row g-4 mb-4">
        <div class="col-6 col-md-3" v-for="(stat, index) in stats" :key="index">
          <div class="stat-card bg-white p-4 rounded-4 shadow-sm border h-100 transition-up">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="icon-box rounded-3 d-flex align-items-center justify-content-center" :style="{ color: stat.color, backgroundColor: stat.color + '15' }">
                <i :class="['bi fs-4', stat.icon]"></i>
              </div>
            </div>
            <p class="text-secondary small fw-bold text-uppercase mb-1">{{ stat.title }}</p>
            <h3 class="mb-0 fw-bold">{{ stat.value }}</h3>
          </div>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="bg-white rounded-4 shadow-sm border overflow-hidden">
        <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-list-stars fs-5 text-primary"></i>
            <h5 class="mb-0 fw-bold">Daftar Reservasi & Kunjungan</h5>
          </div>
          <div class="d-flex gap-2">
            <select v-model="selectedFilter" class="form-select form-select-sm border-2 rounded-3 filter-select">
              <option value="all">Semua Status</option>
              <option value="pending">Menunggu</option>
              <option value="confirmed">Dikonfirmasi</option>
              <option value="completed">Selesai</option>
              <option value="cancelled">Dibatalkan</option>
            </select>
          </div>
        </div>

        <div class="p-0">
          <div v-if="filteredBookings.length === 0" class="text-center py-5 text-secondary">
            <i class="bi bi-inbox fs-1 mb-2 d-block opacity-25"></i>
            <p>Tidak ada data booking dengan filter ini.</p>
          </div>

          <div v-else class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light text-secondary small text-uppercase fw-bold">
                <tr>
                  <th class="px-4 py-3">Penyewa / Organisasi</th>
                  <th class="px-4 py-3">Event Terkait</th>
                  <th class="px-4 py-3">Tgl Pelaksanaan</th>
                  <th class="px-4 py-3">Jumlah</th>
                  <th class="px-4 py-3">Status</th>
                  <th class="px-4 py-3 text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="booking in filteredBookings" :key="booking.id" class="border-bottom">
                  <td class="px-4 py-3">
                    <div class="fw-bold text-dark">{{ booking.name }}</div>
                    <div class="text-secondary smaller"><i class="bi bi-telephone me-1"></i>{{ booking.phone }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <template v-if="booking.event_name">
                       <span class="badge bg-light text-dark border fw-normal py-2 px-3">
                         <i class="bi bi-tag me-1 text-primary"></i>
                         {{ booking.event_name }}
                       </span>
                    </template>
                    <span v-else class="text-secondary small italic">- Tidak didefinisikan -</span>
                    <div class="text-secondary smaller mt-1" v-if="booking.location">
                      <i class="bi bi-geo-alt me-1"></i>{{ booking.location }}
                    </div>
                  </td>
                  <td class="px-4 py-3 text-dark font-monospace">{{ booking.booking_date }}</td>
                  <td class="px-4 py-3"><span class="fw-bold">{{ booking.guest_count }}</span> <span class="text-secondary small">orang</span></td>
                  <td class="px-4 py-3">
                    <span :class="['badge rounded-pill px-3 py-2', getStatusBadge(booking.status).class]">
                      <i :class="['bi me-1', getStatusBadge(booking.status).icon]"></i>
                      {{ getStatusBadge(booking.status).label }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                      <button v-if="booking.status.toLowerCase() === 'pending'" @click="updateStatus(booking.id, 'Confirmed')" class="btn btn-sm btn-primary py-1 px-2" title="Konfirmasi">
                        <i class="bi bi-check-lg"></i>
                      </button>
                      <button v-if="booking.status.toLowerCase() === 'confirmed'" @click="updateStatus(booking.id, 'Completed')" class="btn btn-sm btn-success py-1 px-2 text-white" title="Selesaikan">
                        <i class="bi bi-check2-all"></i>
                      </button>
                      <button v-if="!['completed', 'cancelled'].includes(booking.status.toLowerCase())" @click="updateStatus(booking.id, 'Cancelled')" class="btn btn-sm btn-warning py-1 px-2 text-dark" title="Batalkan">
                        <i class="bi bi-x-lg"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-danger py-1 px-2" @click="deleteBooking(booking.id)" title="Hapus Data">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>

    <!-- Modal manual registration -->
    <div v-if="showModal" class="modal-backdrop fade show"></div>
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header border-0 p-4">
            <h5 class="modal-title fw-bold fs-4">Catat Peminjaman Tempat</h5>
            <button type="button" class="btn-close" @click="showModal = false"></button>
          </div>
          <div class="modal-body p-4 pt-0">
            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary">Nama Penyewa / Organisasi *</label>
                <input v-model="newBooking.name" type="text" class="form-control border-2 rounded-3 py-2" placeholder="Contoh: Budi Santoso / BEM FT" />
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary">No. WhatsApp *</label>
                <input v-model="newBooking.phone" type="text" class="form-control border-2 rounded-3 py-2" placeholder="08..." />
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary">Email (Opsional)</label>
                <input v-model="newBooking.email" type="email" class="form-control border-2 rounded-3 py-2" placeholder="budi@email.com" />
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary">Tanggal Pelaksanaan *</label>
                <input v-model="newBooking.booking_date" type="date" class="form-control border-2 rounded-3 py-2" />
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary">Nama Event (Rencana) *</label>
                <input v-model="newBooking.event_name" type="text" class="form-control border-2 rounded-3 py-2" placeholder="Contoh: Pentas Seni" />
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary">Lokasi Detail *</label>
                <input v-model="newBooking.location" type="text" class="form-control border-2 rounded-3 py-2" placeholder="Contoh: Panggung Utama / Aula" />
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary">Estimasi Peserta / Panitia</label>
                <input v-model="newBooking.guest_count" type="number" class="form-control border-2 rounded-3 py-2" min="1" />
              </div>
              <div class="col-md-12">
                <label class="form-label small fw-bold text-uppercase text-secondary">Catatan Khusus</label>
                <textarea v-model="newBooking.notes" class="form-control border-2 rounded-3" rows="2" placeholder="Misal: Request meja dekat panggung"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 p-4">
            <button type="button" class="btn btn-light px-4" @click="showModal = false">Batal</button>
            <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" @click="saveManualBooking" :disabled="isSaving">
              <span v-if="isSaving" class="spinner-border spinner-border-sm me-2"></span>
              Simpan Catatan
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.booking-view {
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.icon-box {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-card {
  border-color: #eef2f6 !important;
}

.transition-up:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
}

.smaller {
  font-size: 0.75rem;
}

.no-caret::after {
  display: none;
}

.filter-select {
  width: 160px;
  background-color: #fff;
}

:deep(.dropdown-item) {
  font-size: 0.9rem;
  font-weight: 500;
}

.modal-backdrop {
  z-index: 1040;
}

.modal {
  z-index: 1050;
}

.bg-warning-subtle { background-color: #fffbeb !important; }
.bg-primary-subtle { background-color: #eff6ff !important; }
.bg-success-subtle { background-color: #ecfdf5 !important; }
.bg-danger-subtle { background-color: #fef2f2 !important; }

.font-monospace {
  font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
}
</style>
