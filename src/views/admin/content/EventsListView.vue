<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { eventService } from '@/api/events'
import { useSwal } from '@/composables/useSwal'
import { resolveMediaUrl } from '@/utils/media'
import AdminContentWrapper from '@/components/admin/AdminContentWrapper.vue'
const router = useRouter()
const { success, error: showError, confirmDelete, toast } = useSwal()

// Tabs State
const activeTab = ref('list')
const tabs = [
  { id: 'list', name: 'Daftar Event', icon: 'bi-calendar-event' },
  { id: 'section', name: 'Section Beranda', icon: 'bi-layout-text-window' },
  { id: 'hero', name: 'Hero Halaman', icon: 'bi-image' },
]

// List State
const events = ref([])
const searchQuery = ref('')
const filterCategory = ref('all')
const isLoading = ref(true)

// Settings State
const sectionSettings = ref({
  section_title: '',
  section_subtitle: '',
  cta_text: '',
  cta_link: '',
  layout_type: 'default',
  section_title_italic: []
})

const heroSettings = ref({
  page_hero_title: '',
  page_hero_subtitle: '',
  page_hero_background: null,
})

const isSavingSettings = ref(false)
const heroPreview = ref('')

const pageRoutes = [
  { label: 'Beranda (Home)', value: '/' },
  { label: 'Galeri Foto', value: '/galeri' },
  { label: 'Daftar Event', value: '/events' },
  { label: 'Tentang Kami', value: '/about' },
]

const loadData = async () => {
  isLoading.value = true
  try {
    const [eventsRes, settingsRes] = await Promise.all([
      eventService.getAll(),
      eventService.getSettings(),
    ])

    if (eventsRes.data.success) {
      events.value = eventsRes.data.data || []
    }

    if (settingsRes.data.success && settingsRes.data.data) {
      const data = settingsRes.data.data
      sectionSettings.value = {
        section_title: data.section_title || '',
        section_subtitle: data.section_subtitle || '',
        cta_text: data.cta_text || '',
        cta_link: data.cta_link || '',
        layout_type: data.layout_type || 'default',
        section_title_italic: Array.isArray(data.section_title_italic) ? data.section_title_italic : []
      }

      heroSettings.value = {
        page_hero_title: data.page_hero_title || '',
        page_hero_subtitle: data.page_hero_subtitle || '',
      }
      heroPreview.value = data.page_hero_background_url
        ? resolveMediaUrl(data.page_hero_background_url)
        : ''
    }
  } catch (error) {
    console.error('Failed to load data:', error)
  } finally {
    isLoading.value = false
  }
}

const saveSettings = async (type) => {
  isSavingSettings.value = true
  try {
    const formData = new FormData()

    // Append section settings
    formData.append('section_title', sectionSettings.value.section_title)
    formData.append('section_subtitle', sectionSettings.value.section_subtitle)
    formData.append('cta_text', sectionSettings.value.cta_text)
    formData.append('cta_link', sectionSettings.value.cta_link)
    formData.append('layout_type', sectionSettings.value.layout_type)
    formData.append('section_title_italic', JSON.stringify(sectionSettings.value.section_title_italic))

    // Append hero settings
    formData.append('page_hero_title', heroSettings.value.page_hero_title)
    formData.append('page_hero_subtitle', heroSettings.value.page_hero_subtitle)

    if (heroSettings.value.page_hero_background) {
      formData.append('page_hero_background', heroSettings.value.page_hero_background)
    }

    const response = await eventService.updateSettings(formData)

    if (response.data.success) {
      toast('success', `Pengaturan ${type === 'section' ? 'Landing' : 'Hero'} berhasil disimpan`)
      await loadData()
    } else {
      showError('Gagal!', response.data.message || 'Terjadi kesalahan saat menyimpan')
    }
  } catch (error) {
    showError('Gagal!', 'Tidak dapat menghubungi server')
  } finally {
    isSavingSettings.value = false
  }
}

const handleHeroMediaSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    const allowed = [
      'image/webp',
      'image/jpeg',
      'image/jpg',
      'image/png',
      'video/mp4',
      'video/webm',
    ]
    if (!allowed.includes(file.type)) {
      showError(
        'Format Tidak Valid',
        'Gunakan WebP/JPG/PNG untuk gambar atau MP4/WebM untuk video.',
      )
      return
    }
    heroSettings.value.page_hero_background = file
    heroPreview.value = URL.createObjectURL(file)
  }
}

const filteredEvents = computed(() => {
  return events.value.filter((event) => {
    const matchSearch = event.name?.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchCategory = filterCategory.value === 'all' || event.category === filterCategory.value
    return matchSearch && matchCategory
  })
})
const itemsPerPage = 5
const currentPage = ref(1)
const totalPages = computed(() => Math.max(1, Math.ceil(filteredEvents.value.length / itemsPerPage)))
const paginatedEvents = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredEvents.value.slice(start, start + itemsPerPage)
})
watch([searchQuery, filterCategory, filteredEvents], () => {
  currentPage.value = 1
})

const featuredCount = computed(() => {
  return events.value.filter((e) => e.is_featured == 1).length
})

const deleteEvent = async (id) => {
  const result = await confirmDelete('event ini')
  if (!result.isConfirmed) return
  try {
    await eventService.delete(id)
    events.value = events.value.filter((e) => e.id !== id)
    success('Dihapus!', 'Event berhasil dihapus')
  } catch (error) {
    showError('Gagal!', 'Tidak dapat menghapus event')
  }
}

const getWords = (line) => {
  if (!line) return []
  return line.split(/\s+/).filter((w) => w.length > 0)
}

const isWordItalic = (field, word) => {
  if (field === 'section_title') {
    return sectionSettings.value.section_title_italic.includes(word)
  }
  return false
}

const toggleWordItalic = (field, word) => {
  if (field === 'section_title') {
    const list = [...sectionSettings.value.section_title_italic]
    const index = list.indexOf(word)
    if (index > -1) {
      list.splice(index, 1)
    } else {
      list.push(word)
    }
    sectionSettings.value.section_title_italic = list
  }
}

onMounted(loadData)
</script>

<template>
  <AdminContentWrapper
    pageTitle="Manajemen Event"
    pageDescription="Kelola daftar event publik dan tampilan section di landing page"
    :tabs="tabs"
    v-model="activeTab"
    showAddButton
    addButtonText="Tambah Event"
    @action-add="router.push('/admin/konten/events/new')"
  >
    <template #header-actions>
      <button
        @click="router.push('/admin/booking')"
        class="btn btn-outline-primary px-3 py-2 rounded-3 shadow-sm d-flex align-items-center"
      >
        <i class="bi bi-ticket-perforated me-2"></i>Liat Booking Event
      </button>
    </template>

    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
    </div>

    <template v-else>
      <!-- Tab: List -->
      <div v-if="activeTab === 'list'" class="fade-in">
        <!-- Featured Summary Info -->
        <div class="alert bg-white border-2 rounded-4 mb-4 d-flex align-items-center justify-content-between shadow-sm p-3">
          <div class="d-flex align-items-center gap-3">
            <div class="bg-warning bg-opacity-10 p-2 rounded-3">
              <i class="bi bi-star-fill text-warning fs-4"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-0">Event di Landing Page</h6>
              <p class="text-secondary small mb-0">
                Terpilih <strong>{{ featuredCount }}</strong> dari maksimal 3 slot.
                <span v-if="featuredCount === 0" class="text-danger ms-1">(Default: Tampil 3 terbaru)</span>
              </p>
            </div>
          </div>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <div class="input-group search-group">
              <span class="input-group-text bg-white border-end-0 border-2"
                ><i class="bi bi-search"></i
              ></span>
              <input
                v-model="searchQuery"
                type="text"
                class="form-control border-start-0 border-2 py-2 px-3"
                placeholder="Cari event..."
              />
            </div>
          </div>
          <div class="col-md-3">
            <select v-model="filterCategory" class="form-select border-2 py-2 px-3">
              <option value="all">Semua Kategori</option>
              <option
                v-for="cat in ['Festival', 'Workshop', 'Seni & Budaya', 'Pameran']"
                :key="cat"
                :value="cat"
              >
                {{ cat }}
              </option>
            </select>
          </div>
        </div>

        <div
          v-if="events.length === 0"
          class="text-center py-5 bg-light rounded-4 border-2 border-dashed text-secondary"
        >
          <i class="bi bi-calendar-x fs-1 mb-2 d-block"></i>
          <p>Belum ada data event.</p>
        </div>

        <div v-else class="table-responsive rounded-4 border">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr>
                <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">
                  Informasi Event
                </th>
                <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">Kategori</th>
                <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">Status</th>
                <th class="px-4 py-3 text-secondary small fw-bold text-uppercase text-end">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="event in paginatedEvents" :key="event.id">
                <td class="px-4 py-3">
                  <div class="d-flex align-items-center gap-3">
                    <div class="thumb-wrapper rounded-3 overflow-hidden shadow-sm">
                      <img
                        v-if="event.image"
                        :src="resolveMediaUrl(event.image)"
                        class="img-fluid"
                      />
                      <div v-else class="placeholder"><i class="bi bi-image"></i></div>
                    </div>
                    <div>
                      <div class="fw-bold text-dark d-flex align-items-center gap-2">
                        {{ event.name }}
                        <span v-if="event.is_featured == 1" class="badge bg-warning text-dark border-0 py-1" style="font-size: 0.65rem;"><i class="bi bi-star-fill me-1"></i>Featured</span>
                      </div>
                      <div class="text-secondary small">{{ event.date }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <span class="badge bg-light text-dark border">{{ event.category }}</span>
                </td>
                <td class="px-4 py-3">
                  <span
                    class="badge rounded-pill px-3"
                    :class="
                      event.status === 'Aktif'
                        ? 'bg-success-subtle text-success'
                        : 'bg-secondary-subtle text-secondary'
                    "
                    >{{ event.status }}</span
                  >
                </td>
                <td class="px-4 py-3 text-end">
                  <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                    <button
                      @click="router.push(`/admin/konten/events/${event.id}`)"
                      class="btn btn-white btn-sm px-3"
                    >
                      <i class="bi bi-pencil text-primary"></i>
                    </button>
                    <button
                      @click="deleteEvent(event.id)"
                      class="btn btn-white btn-sm px-3 border-start"
                    >
                      <i class="bi bi-trash text-danger"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="filteredEvents.length > itemsPerPage" class="d-flex justify-content-between align-items-center mt-3">
          <small class="text-secondary">Halaman {{ currentPage }} dari {{ totalPages }}</small>
          <div class="btn-group">
            <button class="btn btn-sm btn-outline-secondary" :disabled="currentPage === 1" @click="currentPage--">Sebelumnya</button>
            <button class="btn btn-sm btn-outline-secondary" :disabled="currentPage === totalPages" @click="currentPage++">Selanjutnya</button>
          </div>
        </div>
      </div>

      <!-- Tab: Section Settings -->
      <div v-if="activeTab === 'section'" class="fade-in max-w-800 mx-auto">
        <div class="card border-0 bg-light rounded-4 p-4 shadow-sm">
          <h5 class="fw-bold mb-4 d-flex align-items-center">
            <i class="bi bi-gear-fill me-2 text-primary"></i>Landing Page Section
          </h5>
          <div class="row g-4">
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-secondary"
                >Judul Section Utama</label
              >
              <input
                v-model="sectionSettings.section_title"
                type="text"
                class="form-control border-2 py-2 px-3 rounded-3"
              />
              <div class="italic-control mt-2">
                <small class="text-secondary d-block mb-2">Pilih kata untuk italic:</small>
                <div class="word-toggles">
                  <button
                    v-for="word in getWords(sectionSettings.section_title)"
                    :key="'section-title-' + word"
                    type="button"
                    class="word-toggle"
                    :class="{ active: isWordItalic('section_title', word) }"
                    @click="toggleWordItalic('section_title', word)"
                  >
                    {{ word }}
                  </button>
                </div>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-secondary"
                >Deskripsi Section</label
              >
              <textarea
                v-model="sectionSettings.section_subtitle"
                class="form-control border-2 py-2 px-3 rounded-3"
                rows="3"
              ></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-secondary">Teks CTA</label>
              <input
                v-model="sectionSettings.cta_text"
                type="text"
                class="form-control border-2 py-2 px-3 rounded-3"
              />
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-secondary">Link CTA</label>
              <select v-model="sectionSettings.cta_link" class="form-select border-2 py-2 px-3 rounded-3">
                <option value="">Pilih Tujuan...</option>
                <option v-for="route in pageRoutes" :key="route.value" :value="route.value">
                  {{ route.label }}
                </option>
              </select>
            </div>
          </div>
          <div class="mt-5 text-end">
            <button
              @click="saveSettings('section')"
              class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm"
              :disabled="isSavingSettings"
            >
              <span v-if="isSavingSettings" class="spinner-border spinner-border-sm me-2"></span>
              Simpan Pengaturan
            </button>
          </div>
        </div>
      </div>

      <!-- Tab: Hero Settings -->
      <div v-if="activeTab === 'hero'" class="fade-in max-w-800 mx-auto">
        <div class="card border-0 bg-light rounded-4 overflow-hidden mb-4 shadow-sm">
          <div
            class="hero-preview-box position-relative"
            :style="{
              backgroundImage: heroPreview ? `url(${heroPreview})` : 'none',
              backgroundColor: '#033d4a',
            }"
          >
            <div class="hero-preview-overlay"></div>
            <div class="hero-preview-content position-relative z-1 p-5 text-center text-white">
              <h2 class="display-6 fw-bold mb-2">
                {{ heroSettings.page_hero_title || 'Hero Title' }}
              </h2>
              <p class="lead opacity-75">
                {{ heroSettings.page_hero_subtitle || 'Hero Subtitle' }}
              </p>
            </div>
            <div class="position-absolute bottom-0 end-0 p-3 z-2">
              <label class="btn btn-sm btn-light rounded-pill px-3 shadow-sm cursor-pointer">
                <i class="bi bi-camera me-1"></i>Ganti Media
                <input
                  type="file"
                  @change="handleHeroMediaSelect"
                  class="d-none"
                  accept="image/*,video/*"
                />
              </label>
            </div>
          </div>
          <div class="card-body p-4 bg-white">
            <div class="row g-4">
              <div class="col-12">
                <label class="form-label small fw-bold text-uppercase text-secondary"
                  >Judul Hero</label
                >
                <input
                  v-model="heroSettings.page_hero_title"
                  type="text"
                  class="form-control border-2 py-2 px-3 rounded-3"
                />
              </div>
              <div class="col-12">
                <label class="form-label small fw-bold text-uppercase text-secondary"
                  >Subjudul Hero</label
                >
                <textarea
                  v-model="heroSettings.page_hero_subtitle"
                  class="form-control border-2 py-2 px-3 rounded-3"
                  rows="3"
                ></textarea>
              </div>
            </div>
            <div class="mt-4 text-end">
              <button
                @click="saveSettings('hero')"
                class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm"
                :disabled="isSavingSettings"
              >
                <span v-if="isSavingSettings" class="spinner-border spinner-border-sm me-2"></span>
                Simpan Hero
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>
  </AdminContentWrapper>
</template>

<style scoped>
.nav-tabs .nav-link {
  color: #64748b;
  border: none;
  padding: 1rem 1.5rem;
}
.nav-tabs .nav-link.active {
  color: #033d4a;
  background: #fff;
  border-bottom: 3px solid #033d4a;
  border-radius: 0;
  box-shadow: none;
}
.thumb-wrapper {
  width: 80px;
  height: 50px;
  background-color: #f1f5f9;
  flex-shrink: 0;
}
.thumb-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.thumb-wrapper .placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: #cbd5e1;
}
.btn-white {
  background: #fff;
  border: 1px solid #e2e8f0;
  color: #64748b;
}
.btn-white:hover {
  background: #f8fafc;
  color: #033d4a;
}
.hero-preview-box {
  height: 250px;
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.hero-preview-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(rgba(0, 0, 0, 0.05), rgba(0, 0, 0, 0.15));
}
.max-w-800 {
  max-width: 800px;
}
.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.cursor-pointer {
  cursor: pointer;
}

/* Italic Control Styles */
.italic-control {
  padding: 1.25rem;
  background-color: #f8fafb;
  border-radius: 1rem;
  border: 1px solid #e2e8f0;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}
.word-toggles {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}
.word-toggle {
  padding: 0.5rem 1.25rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 0.75rem;
  background-color: #ffffff;
  color: #64748b;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.word-toggle:hover {
  background-color: #f1f5f9;
  border-color: #cbd5e1;
  color: #033d4a;
  transform: translateY(-1px);
}
.word-toggle.active {
  background: linear-gradient(135deg, #033d4a 0%, #0791b0 100%);
  border-color: transparent;
  color: #ffffff;
  font-style: italic;
  box-shadow: 0 4px 12px rgba(3, 61, 74, 0.25);
  transform: translateY(-1px);
}

/* Pagination Styles */
.pagination-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  background: white;
  color: #64748b;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.pagination-btn:hover:not(:disabled) {
  background: #f8fafc;
  color: #033d4a;
  border-color: #033d4a;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(3, 61, 74, 0.15);
}

.pagination-btn:active:not(:disabled) {
  transform: translateY(0);
}

.pagination-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
  background: #f1f5f9;
  border-color: #e2e8f0;
}

.page-indicator {
  font-size: 0.9rem;
  color: #64748b;
  background: #f8fafc;
  padding: 8px 16px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
}
</style>
