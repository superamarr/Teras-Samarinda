<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { facilityService } from '@/api/facilities'
import { useSwal } from '@/composables/useSwal'
import { resolveMediaUrl } from '@/utils/media'
import AdminContentWrapper from '@/components/admin/AdminContentWrapper.vue'

const router = useRouter()
const { success, error: showError, confirmDelete, toast } = useSwal()

// Tabs State
const activeTab = ref('list')
const tabs = [
  { id: 'list', name: 'Daftar Fasilitas', icon: 'bi-building' },
  { id: 'section', name: 'Section Beranda', icon: 'bi-layout-text-window' },
]

// List State
const facilities = ref([])
const searchQuery = ref('')
const isLoading = ref(true)
const draggedItem = ref(null)
const dragOverId = ref(null)

// Section Settings State
const sectionSettings = ref({
  section_title: '',
  section_subtitle: '',
  section_subtitle_extra: '',
  cta_text: '',
  cta_link: '',
  layout_type: 'default',
  section_title_italic: []
})
const isSavingSettings = ref(false)


const pageRoutes = [
  { label: 'Halaman Beranda', value: '/' },
  { label: 'Halaman Galeri', value: '/galeri' },
  { label: 'Halaman Event', value: '/events' },
  { label: 'Tentang Kami', value: '/about' },
]

const loadData = async () => {
  isLoading.value = true
  try {
    const [facilitiesRes, settingsRes] = await Promise.all([
      facilityService.getAll(),
      facilityService.getSettings(),
    ])

    if (facilitiesRes.data.success) {
      facilities.value = facilitiesRes.data.data || []
    }

    if (settingsRes.data.success && settingsRes.data.data) {
      const data = settingsRes.data.data
      sectionSettings.value = {
        section_title: data.section_title || '',
        section_subtitle: data.section_subtitle || '',
        section_subtitle_extra: data.section_subtitle_extra || '',
        cta_text: data.cta_text || '',
        cta_link: data.cta_link || '',
        layout_type: data.layout_type || 'default',
        section_title_italic: Array.isArray(data.section_title_italic) ? data.section_title_italic : []
      }
    }
  } catch (error) {
    console.error('Failed to load data:', error)
    showError('Gagal!', 'Tidak dapat memuat data')
  } finally {
    isLoading.value = false
  }
}

const saveSectionSettings = async () => {
  isSavingSettings.value = true
  try {
    const formData = new FormData()
    Object.keys(sectionSettings.value).forEach((key) => {
      if (key === 'section_title_italic') {
        formData.append(key, JSON.stringify(sectionSettings.value[key]))
      } else {
        formData.append(key, sectionSettings.value[key])
      }
    })

    await facilityService.updateSettings(formData)
    toast('success', 'Pengaturan section berhasil diperbarui')
  } catch (error) {
    showError('Gagal!', 'Tidak dapat menyimpan pengaturan section')
  } finally {
    isSavingSettings.value = false
  }
}


const filteredFacilities = computed(() => {
  return facilities.value.filter((facility) => {
    return facility.name?.toLowerCase().includes(searchQuery.value.toLowerCase())
  })
})

const getImageUrl = (image) => (image ? resolveMediaUrl(image) : '')
const goToDetail = (id) => router.push(`/admin/konten/facilities/${id}`)
const goToCreate = () => router.push('/admin/konten/facilities/new')

const deleteFacility = async (id) => {
  const result = await confirmDelete('fasilitas ini')
  if (!result.isConfirmed) return

  try {
    await facilityService.delete(id)
    facilities.value = facilities.value.filter((f) => f.id !== id)
    success('Dihapus!', 'Fasilitas berhasil dihapus')
  } catch (error) {
    showError('Gagal!', 'Tidak dapat menghapus fasilitas')
  }
}

// Drag & Drop
const onDragStart = (facility) => (draggedItem.value = facility)
const onDragOver = (e, facility) => {
  e.preventDefault()
  if (draggedItem.value && draggedItem.value.id !== facility.id) {
    dragOverId.value = facility.id
  }
}
const onDragLeave = () => (dragOverId.value = null)
const onDrop = async (e, targetFacility) => {
  e.preventDefault()
  if (!draggedItem.value || draggedItem.value.id === targetFacility.id) {
    draggedItem.value = null
    dragOverId.value = null
    return
  }

  const draggedIndex = facilities.value.findIndex((f) => f.id === draggedItem.value.id)
  const targetIndex = facilities.value.findIndex((f) => f.id === targetFacility.id)

  if (draggedIndex !== -1 && targetIndex !== -1) {
    const [item] = facilities.value.splice(draggedIndex, 1)
    facilities.value.splice(targetIndex, 0, item)
    try {
      await facilityService.sort(facilities.value.map((f) => f.id))
      toast('success', 'Urutan berhasil disimpan')
    } catch (error) {
      showError('Gagal!', 'Tidak dapat menyimpan urutan')
    }
  }

  draggedItem.value = null
  dragOverId.value = null
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
    pageTitle="Manajemen Facilities"
    pageDescription="Kelola sarana dan prasarana yang tersedia di Teras Samarinda"
    :tabs="tabs"
    v-model="activeTab"
    showAddButton
    addButtonText="Tambah Fasilitas"
    @action-add="goToCreate"
  >
    <!-- Tab Content -->
    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <template v-else>
      <!-- Tab: List -->
      <div v-if="activeTab === 'list'" class="fade-in">
        <div class="row mb-4">
          <div class="col-md-6">
            <div class="input-group search-group">
              <span class="input-group-text bg-white border-end-0 border-2">
                <i class="bi bi-search text-secondary"></i>
              </span>
              <input
                v-model="searchQuery"
                type="text"
                class="form-control border-start-0 border-2 ps-0 py-2 px-3"
                placeholder="Cari fasilitas..."
              />
            </div>
          </div>
        </div>

        <div
          v-if="facilities.length === 0"
          class="text-center py-5 bg-light rounded-4 border-2 border-dashed"
        >
          <i class="bi bi-building fs-1 text-secondary mb-3 d-block"></i>
          <p class="text-secondary">Belum ada data fasilitas.</p>
        </div>

        <div v-else class="table-responsive rounded-4 border">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr>
                <th
                  class="px-4 py-3 text-secondary small fw-bold text-uppercase"
                  style="width: 50px"
                ></th>
                <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">
                  Informasi Fasilitas
                </th>
                <th class="px-4 py-3 text-secondary small fw-bold text-uppercase text-end">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="facility in filteredFacilities"
                :key="facility.id"
                draggable="true"
                @dragstart="onDragStart(facility)"
                @dragover="onDragOver($event, facility)"
                @dragleave="onDragLeave"
                @drop="onDrop($event, facility)"
                class="cursor-grab transition-all"
                :class="{ 'bg-primary bg-opacity-5': dragOverId === facility.id }"
              >
                <td class="px-4 py-3 text-center">
                  <i class="bi bi-grip-vertical fs-5 text-secondary opacity-50"></i>
                </td>
                <td class="px-4 py-3">
                  <div class="d-flex align-items-center gap-3">
                    <div class="facility-img-wrapper rounded-3 overflow-hidden shadow-sm">
                      <img
                        v-if="facility.image"
                        :src="getImageUrl(facility.image)"
                        :alt="facility.name"
                        class="img-fluid"
                      />
                      <div v-else class="img-placeholder"><i class="bi bi-building"></i></div>
                    </div>
                    <div class="fw-bold text-dark">{{ facility.name }}</div>
                  </div>
                </td>
                <td class="px-4 py-3 text-end">
                  <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                    <button
                      @click="goToDetail(facility.id)"
                      class="btn btn-white btn-sm px-3"
                      title="Edit"
                    >
                      <i class="bi bi-pencil text-primary"></i>
                    </button>
                    <button
                      @click="deleteFacility(facility.id)"
                      class="btn btn-white btn-sm px-3 border-start"
                      title="Hapus"
                    >
                      <i class="bi bi-trash text-danger"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab: Section Settings -->
      <div v-if="activeTab === 'section'" class="fade-in max-w-800 mx-auto">
        <div class="card border-0 bg-light rounded-4 p-4">
          <h5 class="fw-bold mb-4 d-flex align-items-center">
            <i class="bi bi-gear-fill me-2 text-primary"></i>Pengaturan Section Fasilitas
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
                >Sub-Judul (Deskripsi Singkat)</label
              >
              <textarea
                v-model="sectionSettings.section_subtitle"
                class="form-control border-2 py-2 px-3 rounded-3"
                rows="3"
              ></textarea>
            </div>

          </div>

          <div class="mt-5 text-end">
            <button
              @click="saveSectionSettings"
              class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm"
              :disabled="isSavingSettings"
            >
              <span v-if="isSavingSettings" class="spinner-border spinner-border-sm me-2"></span>
              Simpan Settings
            </button>
          </div>
        </div>
      </div>

    </template>
  </AdminContentWrapper>
</template>

<style scoped>
.content-view {
  min-height: 600px;
}
.nav-tabs .nav-link {
  color: #64748b;
}
.nav-tabs .nav-link.active {
  background-color: #fff;
  color: #033d4a;
  box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.03);
}
.nav-tabs .nav-link:hover:not(.active) {
  color: #033d4a;
  background-color: rgba(3, 61, 74, 0.05);
}

.search-group .input-group-text {
  border-color: #e2e8f0;
}
.search-group .form-control:focus {
  border-color: #033d4a;
  z-index: 0;
}

.facility-img-wrapper {
  width: 80px;
  height: 50px;
  flex-shrink: 0;
  background-color: #f1f5f9;
}
.facility-img-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.img-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: #cbd5e1;
}

.cursor-grab {
  cursor: grab;
}
.transition-all {
  transition: all 0.2s ease;
}

.hero-preview-box {
  height: 300px;
  background-size: cover;
  background-position: center;
  background-color: #033d4a;
}
.hero-preview-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.7));
}

.max-w-800 {
  max-width: 800px;
}
.btn-white {
  background: #fff;
  color: #64748b;
  border: 1px solid #e2e8f0;
}
.btn-white:hover {
  background: #f8fafc;
  color: #033d4a;
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
</style>
