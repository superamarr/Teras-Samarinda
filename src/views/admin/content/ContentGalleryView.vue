<script setup>
import { ref, computed, onMounted } from 'vue'
import { galleryService } from '@/api/gallery'
import { useSwal } from '@/composables/useSwal'
import { resolveMediaUrl } from '@/utils/media'
import AdminContentWrapper from '@/components/admin/AdminContentWrapper.vue'
const { success, error: showError, confirm, confirmDelete, toast } = useSwal()

const images = ref([])
const searchQuery = ref('')
const filterCategory = ref('all')
const isLoading = ref(true)

const showAddModal = ref(false)
const showEditModal = ref(false)
const editingImage = ref(null)
const editPreview = ref('')

const newImage = ref({
  title: '',
  category: 'Pemandangan',
  image: null,
})
const imagePreview = ref('')
const dragOverImage = ref(false)
const isSaving = ref(false)
const isUpdating = ref(false)

const openEditModal = (img) => {
  editingImage.value = { ...img, newImage: null }
  editPreview.value = getImageUrl(img.url)
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  editingImage.value = null
  editPreview.value = ''
}

const handleEditImageSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    const allowed = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png']
    if (!allowed.includes(file.type)) {
      showError('Format Tidak Valid', 'Hanya format WebP, JPG, dan PNG yang diizinkan')
      return
    }
    if (file.size > 5 * 1024 * 1024) {
      showError('File Terlalu Besar', 'Ukuran maksimal 5MB')
      return
    }
    editingImage.value.newImage = file
    editPreview.value = URL.createObjectURL(file)
  }
}

const handleUpdateImage = async () => {
  if (!editingImage.value.title.trim()) {
    await showError('Judul Wajib', 'Silakan masukkan judul gambar')
    return
  }

  isUpdating.value = true
  try {
    const formData = new FormData()
    formData.append('title', editingImage.value.title)
    formData.append('category', editingImage.value.category)
    if (editingImage.value.newImage) {
      formData.append('image', editingImage.value.newImage)
    }

    await galleryService.update(editingImage.value.id, formData)
    await success('Berhasil!', 'Gambar berhasil diperbarui')
    closeEditModal()
    await loadImages()
  } catch (error) {
    await showError('Gagal!', 'Tidak dapat memperbarui gambar')
  } finally {
    isUpdating.value = false
  }
}

const categories = ['Pemandangan', 'Event', 'Budaya', 'Arsitektur', 'Alam']

const pageRoutes = [
  { label: 'Beranda (Home)', value: '/' },
  { label: 'Galeri Foto (GalleryView)', value: '/galeri' },
  { label: 'Daftar Event (EventsView)', value: '/events' },
  { label: 'Detail Event (EventDetailView)', value: '/events/1' },
  { label: 'Tentang Samarinda (AboutDetailView)', value: '/tentang' },
]

const activeTab = ref('list') // 'list', 'section', 'hero'

const tabs = [
  { id: 'list', name: 'Daftar Foto', icon: 'bi-images' },
  { id: 'section', name: 'Section Beranda', icon: 'bi-layout-text-window' },
  { id: 'hero', name: 'Hero Halaman', icon: 'bi-image' },
]
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
  page_hero_background_url: '',
})

const heroPreview = ref('')
const isSavingHero = ref(false)
const isSavingSettings = ref(false)

const loadImages = async () => {
  try {
    const [imagesRes, settingsRes] = await Promise.all([
      galleryService.getAll(),
      galleryService.getSettings(),
    ])

    if (imagesRes.data.success) {
      images.value = imagesRes.data.data || []
    }

    if (settingsRes.data.success && settingsRes.data.data) {
      const d = settingsRes.data.data
      sectionSettings.value = { 
        ...d,
        section_title_italic: Array.isArray(d.section_title_italic) ? d.section_title_italic : []
      }
      heroSettings.value = {
        page_hero_title: d.page_hero_title || '',
        page_hero_subtitle: d.page_hero_subtitle || '',
        page_hero_background_url: d.page_hero_background_url || '',
        page_hero_background: null,
      }
    }
  } catch (error) {
    console.error('Failed to load images:', error)
  } finally {
    isLoading.value = false
  }
}

const handleHeroMediaSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    const allowed = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png', 'video/mp4', 'video/webm']
    if (!allowed.includes(file.type)) {
      showError('Format Tidak Valid', 'Gunakan WebP/JPG/PNG untuk gambar atau MP4/WebM untuk video.')
      return
    }
    heroSettings.value.page_hero_background = file
    heroPreview.value = URL.createObjectURL(file)
  }
}

const saveSettings = async (type) => {
  const isHero = type === 'hero'
  const result = await confirm('Simpan Pengaturan?', 'Anda yakin ingin menyimpan perubahan pada pengaturan galeri ini?')
  if (!result.isConfirmed) return

  if (isHero) isSavingHero.value = true
  else isSavingSettings.value = true

  try {
    const formData = new FormData()

    if (isHero) {
      formData.append('page_hero_title', heroSettings.value.page_hero_title)
      formData.append('page_hero_subtitle', heroSettings.value.page_hero_subtitle)
      if (heroSettings.value.page_hero_background) {
        formData.append('page_hero_background', heroSettings.value.page_hero_background)
      }
      formData.append('section_title', sectionSettings.value.section_title)
      formData.append('section_subtitle', sectionSettings.value.section_subtitle)
      formData.append('cta_text', sectionSettings.value.cta_text)
      formData.append('cta_link', sectionSettings.value.cta_link)
      formData.append('layout_type', sectionSettings.value.layout_type)
    } else {
      formData.append('section_title', sectionSettings.value.section_title)
      formData.append('section_subtitle', sectionSettings.value.section_subtitle)
      formData.append('cta_text', sectionSettings.value.cta_text)
      formData.append('cta_link', sectionSettings.value.cta_link)
      formData.append('layout_type', sectionSettings.value.layout_type)
      formData.append('section_title_italic', JSON.stringify(sectionSettings.value.section_title_italic))
      formData.append('page_hero_title', heroSettings.value.page_hero_title)
      formData.append('page_hero_subtitle', heroSettings.value.page_hero_subtitle)
    }

    await galleryService.updateSettings(formData)
    await success('Berhasil!', 'Pengaturan berhasil diperbarui')
    if (isHero) {
      const res = await galleryService.getSettings()
      if (res.data.success && res.data.data) {
        const d = res.data.data
        heroSettings.value.page_hero_background_url = d.page_hero_background_url
        heroPreview.value = ''
        heroSettings.value.page_hero_background = null
      }
    }
  } catch (error) {
    showError('Gagal!', 'Tidak dapat menyimpan pengaturan')
  } finally {
    isSavingHero.value = false
    isSavingSettings.value = false
  }
}

const getHeroMediaUrl = (url) => {
  if (!url) return ''
  return resolveMediaUrl(url)
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

const isVideoHero = (url) => {
  if (!url) return false
  return url.match(/\.(mp4|webm|ogg)$/i)
}

const filteredImages = computed(() => {
  return images.value.filter((img) => {
    const matchSearch = img.title?.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchCategory = filterCategory.value === 'all' || img.category === filterCategory.value
    return matchSearch && matchCategory
  })
})

const itemsPerPage = 12
const currentPage = ref(1)
const totalPages = computed(() => Math.max(1, Math.ceil(filteredImages.value.length / itemsPerPage)))
const paginatedImages = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredImages.value.slice(start, start + itemsPerPage)
})

import { watch } from 'vue'
watch([searchQuery, filterCategory], () => {
  currentPage.value = 1
})

const openAddModal = () => {
  newImage.value = { title: '', category: 'Pemandangan', image: null }
  imagePreview.value = ''
  showAddModal.value = true
}

const closeAddModal = () => {
  showAddModal.value = false
  newImage.value = { title: '', category: 'Pemandangan', image: null }
  imagePreview.value = ''
}

const handleImageDrop = async (event) => {
  event.preventDefault()
  dragOverImage.value = false
  const file = event.dataTransfer.files[0]
  if (file && file.type.startsWith('image/')) {
    await processImage(file)
  }
}

const handleImageSelect = async (event) => {
  const file = event.target.files[0]
  if (file) await processImage(file)
}

const processImage = async (file) => {
  const sizeMB = file.size / (1024 * 1024)
  const allowed = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png']

  if (!allowed.includes(file.type)) {
    showError('Format Tidak Valid', 'Hanya format WebP, JPG, dan PNG yang diizinkan')
    return
  }

  if (file.size > 5 * 1024 * 1024) {
    showError('File Terlalu Besar', 'Ukuran maksimal 5MB')
    return
  }

  if (sizeMB > 1) {
    const result = await confirm(
      'Format File',
      'File lebih dari 1MB. Disarankan menggunakan format WebP untuk performa lebih baik. Lanjutkan dengan format saat ini?'
    )
    if (!result.isConfirmed) return
  }

  newImage.value.image = file
  imagePreview.value = URL.createObjectURL(file)
}

const saveNewImage = async () => {
  if (!newImage.value.image) {
    await showError('Gambar Wajib', 'Silakan upload gambar terlebih dahulu')
    return
  }

  if (!newImage.value.title.trim()) {
    await showError('Judul Wajib', 'Silakan masukkan judul gambar')
    return
  }

  isSaving.value = true
  try {
    const formData = new FormData()
    formData.append('title', newImage.value.title)
    formData.append('category', newImage.value.category)
    formData.append('image', newImage.value.image)

    await galleryService.create(formData)
    await success('Berhasil!', 'Gambar berhasil ditambahkan')
    closeAddModal()
    await loadImages()
  } catch (error) {
    await showError('Gagal!', 'Tidak dapat menambahkan gambar')
  } finally {
    isSaving.value = false
  }
}

const deleteImage = async (id) => {
  const result = await confirmDelete('gambar ini')
  if (!result.isConfirmed) return

  try {
    await galleryService.delete(id)
    images.value = images.value.filter((i) => i.id !== id)
    await success('Dihapus!', 'Gambar berhasil dihapus')
  } catch (error) {
    await showError('Gagal!', 'Tidak dapat menghapus gambar')
  }
}

const toggleFeatured = async (id) => {
  try {
    await galleryService.toggleFeatured(id)
    const img = images.value.find((i) => i.id === id)
    if (img) {
      img.featured = !img.featured
      await toast('success', img.featured ? 'Ditambahkan ke Featured' : 'Dihapus dari Featured')
    }
  } catch (error) {
    await showError('Gagal!', 'Tidak dapat mengubah status featured')
  }
}

const getImageUrl = (url) => {
  if (!url) return ''
  return resolveMediaUrl(url)
}

onMounted(() => {
  loadImages()
})
</script>

<template>
  <AdminContentWrapper
    pageTitle="Kelola Gallery"
    pageDescription="Manajemen galeri foto dan pengaturan tampilan galeri"
    :tabs="tabs"
    v-model="activeTab"
    showAddButton
    addButtonText="Tambah Foto"
    @action-add="openAddModal"
  >
    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <template v-else>
      <!-- Tab: List -->
      <div v-if="activeTab === 'list'" class="fade-in">
        <div class="row g-3 mb-4">
          <div class="col-md-7">
            <div class="input-group search-group shadow-sm rounded-3 overflow-hidden">
              <span class="input-group-text bg-white border-end-0 border-2">
                <i class="bi bi-search text-primary"></i>
              </span>
              <input
                v-model="searchQuery"
                type="text"
                class="form-control border-start-0 border-2 ps-0 py-2 px-3"
                placeholder="Cari foto berdasarkan judul..."
              />
            </div>
          </div>
          <div class="col-md-5">
            <div class="d-flex gap-2">
              <select v-model="filterCategory" class="form-select border-2 py-2 px-3 shadow-sm rounded-3">
                <option value="all">Semua Kategori</option>
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
              </select>
            </div>
          </div>
        </div>

        <div v-if="filteredImages.length === 0" class="text-center py-5 bg-white rounded-4 border-2 border-dashed shadow-sm">
          <div class="py-4">
            <i class="bi bi-images fs-1 text-secondary opacity-25"></i>
            <p class="text-secondary mt-3 fw-medium">Tidak ada foto yang ditemukan.</p>
            <button @click="openAddModal" class="btn btn-sm btn-outline-primary rounded-pill px-4 mt-2">
              Tambah Foto Baru
            </button>
          </div>
        </div>

        <div v-else class="row g-4">
          <div v-for="(img, index) in paginatedImages" :key="img.id" class="col-xl-3 col-lg-4 col-sm-6">
            <div class="gallery-card border-0 rounded-4 overflow-hidden h-100 bg-white shadow-sm position-relative">
              <div
                class="card-img-wrapper position-relative"
                :style="{
                  backgroundImage: img.url
                    ? `url(${getImageUrl(img.url)})`
                    : 'linear-gradient(135deg, #033d4a 0%, #0791b0 100%)',
                }"
              >
                <div class="card-overlay">
                  <div class="action-buttons d-flex gap-2 scale-up">
                    <button
                      @click="toggleFeatured(img.id)"
                      class="action-btn featured-btn shadow"
                      :class="{ active: img.featured }"
                      :title="img.featured ? 'Hapus dari Unggulan' : 'Jadikan Unggulan'"
                    >
                      <i class="bi" :class="img.featured ? 'bi-star-fill' : 'bi-star'"></i>
                    </button>
                    <button @click="openEditModal(img)" class="action-btn edit-btn shadow" title="Edit Detail">
                      <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button @click="deleteImage(img.id)" class="action-btn delete-btn shadow" title="Hapus Foto">
                      <i class="bi bi-trash-fill"></i>
                    </button>
                  </div>
                </div>
                <div class="card-badges position-absolute top-0 start-0 w-100 p-3 d-flex justify-content-between align-items-start pointer-events-none">
                  <span class="category-badge shadow-sm">{{ img.category }}</span>
                  <span v-if="img.featured" class="featured-badge shadow-sm">
                    <i class="bi bi-star-fill"></i>
                  </span>
                </div>
              </div>
              <div class="card-body p-3">
                <h6 class="fw-bold text-dark mb-1 text-truncate" :title="img.title">
                  {{ img.title || 'Tanpa Judul' }}
                </h6>
                <div class="d-flex align-items-center justify-content-between mt-2">
                   <div class="d-flex align-items-center text-secondary smaller">
                    <i class="bi bi-clock-history me-1"></i>
                    {{ new Date(img.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) }}
                  </div>
                  <div class="text-primary smaller fw-bold">
                    <i class="bi bi-aspect-ratio me-1"></i>
                    16:9
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="filteredImages.length > itemsPerPage" class="pagination-container d-flex justify-content-between align-items-center mt-5 px-2">
          <div class="pagination-info">
            <span class="text-secondary small">Menampilkan <strong>{{ paginatedImages.length }}</strong> dari <strong>{{ filteredImages.length }}</strong> foto</span>
          </div>
          <div class="pagination-controls d-flex align-items-center gap-3">
            <button 
              class="pagination-btn" 
              :disabled="currentPage === 1" 
              @click="currentPage--"
              title="Sebelumnya"
            >
              <i class="bi bi-chevron-left"></i>
            </button>
            
            <div class="pagination-pages d-flex align-items-center gap-2">
              <span class="page-indicator">Halaman <strong>{{ currentPage }}</strong> dari <strong>{{ totalPages }}</strong></span>
            </div>

            <button 
              class="pagination-btn" 
              :disabled="currentPage === totalPages" 
              @click="currentPage++"
              title="Selanjutnya"
            >
              <i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Tab: Section Settings -->
      <div v-if="activeTab === 'section'" class="fade-in max-w-800 mx-auto">
        <div class="card border-0 bg-light rounded-4 p-4">
          <h5 class="fw-bold mb-4 d-flex align-items-center">
            <i class="bi bi-gear-fill me-2 text-primary"></i>Pengaturan Section Header (Landing Page)
          </h5>
          
          <div class="row g-4">
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-secondary">Judul Section Utama</label>
              <input v-model="sectionSettings.section_title" type="text" class="form-control border-2 py-2 px-3 rounded-3" />
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
              <label class="form-label small fw-bold text-uppercase text-secondary">Sub-Judul Section</label>
              <textarea v-model="sectionSettings.section_subtitle" class="form-control border-2 py-2 px-3 rounded-3" rows="3"></textarea>
            </div>

            <div class="col-md-4">
              <label class="form-label small fw-bold text-uppercase text-secondary">Teks Tombol CTA</label>
              <input v-model="sectionSettings.cta_text" type="text" class="form-control border-2 py-2 px-3 rounded-3" />
            </div>

            <div class="col-md-4">
              <label class="form-label small fw-bold text-uppercase text-secondary">Link Tombol CTA</label>
              <select v-model="sectionSettings.cta_link" class="form-select border-2 py-2 px-3 rounded-3">
                <option value="">Pilih Tujuan...</option>
                <option v-for="route in pageRoutes" :key="route.value" :value="route.value">
                  {{ route.label }}
                </option>
              </select>
            </div>
          </div>

          <div class="mt-5 text-end">
            <button @click="saveSettings('section')" class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm" :disabled="isSavingSettings">
              <span v-if="isSavingSettings" class="spinner-border spinner-border-sm me-2"></span>
              Simpan Settings
            </button>
          </div>
        </div>
      </div>

      <!-- Tab: Hero Settings -->
      <div v-if="activeTab === 'hero'" class="fade-in max-w-800 mx-auto">
        <div class="card border-0 bg-light rounded-4 overflow-hidden mb-4 shadow-sm">
          <div class="hero-preview-box position-relative">
            <div v-if="heroPreview || heroSettings.page_hero_background_url" class="position-relative w-100 h-100">
              <template v-if="heroPreview">
                <video
                  v-if="heroSettings.page_hero_background?.type.startsWith('video/')"
                  :src="heroPreview"
                  autoplay muted loop class="w-100 h-100 object-fit-cover"
                ></video>
                <img v-else :src="heroPreview" class="w-100 h-100 object-fit-cover" />
              </template>
              <template v-else>
                <video
                  v-if="isVideoHero(heroSettings.page_hero_background_url)"
                  :src="getHeroMediaUrl(heroSettings.page_hero_background_url)"
                  autoplay muted loop class="w-100 h-100 object-fit-cover"
                ></video>
                <img
                  v-else
                  :src="getHeroMediaUrl(heroSettings.page_hero_background_url)"
                  class="w-100 h-100 object-fit-cover"
                />
              </template>
              <div class="hero-preview-overlay"></div>
              <div class="hero-preview-content position-absolute top-50 start-50 translate-middle text-center text-white w-100 z-1 p-5">
                <h2 class="display-6 fw-bold mb-2">{{ heroSettings.page_hero_title || 'Hero Title' }}</h2>
                <p class="lead opacity-75">{{ heroSettings.page_hero_subtitle || 'Hero Subtitle' }}</p>
              </div>
            </div>
            <div v-else class="hero-preview-content h-100 d-flex flex-column align-items-center justify-content-center text-secondary bg-dark text-white">
              <i class="bi bi-image fs-1 opacity-25"></i>
              <span class="small mt-2">Belum ada media</span>
            </div>
            
            <div class="position-absolute bottom-0 end-0 p-3 z-2">
              <div class="d-flex gap-2">
                <button @click="heroSettings.page_hero_background_url = ''; heroPreview = ''" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm" v-if="heroPreview || heroSettings.page_hero_background_url">
                  <i class="bi bi-trash me-1"></i>Hapus Media
                </button>
                <label class="btn btn-sm btn-light rounded-pill px-3 shadow-sm cursor-pointer">
                  <i class="bi bi-camera me-1"></i>Ganti Media
                  <input type="file" @change="handleHeroMediaSelect" class="d-none" accept="image/*,video/*" />
                </label>
              </div>
            </div>
          </div>

          <div class="card-body p-4 bg-white">
            <h5 class="fw-bold mb-4 d-flex align-items-center">
              <i class="bi bi-pencil-square me-2 text-primary"></i>Detail Hero Header
            </h5>
            
            <div class="row g-4">
              <div class="col-12">
                <label class="form-label small fw-bold text-uppercase text-secondary">Judul Hero Halaman</label>
                <input v-model="heroSettings.page_hero_title" type="text" class="form-control border-2 py-2 px-3 rounded-3" />
              </div>
              
              <div class="col-12">
                <label class="form-label small fw-bold text-uppercase text-secondary">Subjudul Hero Halaman</label>
                <textarea v-model="heroSettings.page_hero_subtitle" class="form-control border-2 py-2 px-3 rounded-3" rows="3"></textarea>
              </div>
            </div>

            <div class="mt-5 text-end">
              <button @click="saveSettings('hero')" class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm" :disabled="isSavingHero">
                <span v-if="isSavingHero" class="spinner-border spinner-border-sm me-2"></span>
                Simpan Hero
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Add Modal -->
    <div v-if="showAddModal" class="modal-backdrop" @click.self="closeAddModal">
      <div class="modal-content-custom">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold">Tambah Foto Gallery</h5>
          <button type="button" class="btn-close" @click="closeAddModal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Judul Foto</label>
            <input
              v-model="newImage.title"
              type="text"
              class="form-control"
              placeholder="Masukkan judul foto"
            />
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Kategori</label>
            <select v-model="newImage.category" class="form-select">
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Gambar</label>
            <div
              class="upload-area border rounded-3 p-4 text-center"
              :class="{ 'drag-over': dragOverImage, 'has-file': imagePreview }"
              @dragover.prevent="dragOverImage = true"
              @dragleave="dragOverImage = false"
              @drop="handleImageDrop"
            >
              <div v-if="imagePreview" class="preview-container">
                <img :src="imagePreview" alt="Preview" class="preview-image" />
              </div>
              <div v-else class="upload-placeholder">
                <i class="bi bi-cloud-arrow-up fs-1 text-secondary"></i>
                <p class="text-secondary mb-2">Klik atau drag file untuk upload</p>
                <small class="text-muted">Format: WebP, JPG, PNG (Max 5MB)</small>
                <input
                  type="file"
                  class="form-control mt-3"
                  accept="image/webp,image/jpeg,image/png"
                  @change="handleImageSelect"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-secondary" @click="closeAddModal">
            Batal
          </button>
          <button type="button" class="btn btn-primary" @click="saveNewImage" :disabled="isSaving">
            {{ isSaving ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </div>
    </div>
    <!-- Edit Modal -->
    <div v-if="showEditModal" class="modal-backdrop" @click.self="closeEditModal">
      <div class="modal-content-custom">
        <div class="modal-header border-0 pb-0 d-flex justify-content-between">
          <h5 class="modal-title fw-bold">Edit Foto Gallery</h5>
          <button type="button" class="btn-close" @click="closeEditModal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold small">Judul Foto</label>
            <input
              v-model="editingImage.title"
              type="text"
              class="form-control"
              placeholder="Masukkan judul foto"
            />
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Kategori</label>
            <select v-model="editingImage.category" class="form-select">
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Ganti Gambar (Opsional)</label>
            <div
              class="upload-area border rounded-3 overflow-hidden text-center position-relative"
              :class="{ 'has-file': editPreview }"
              style="height: 200px"
            >
              <img
                v-if="editPreview"
                :src="editPreview"
                alt="Preview"
                class="w-100 h-100 object-fit-cover"
              />
              <div class="position-absolute top-50 start-50 translate-middle w-100">
                <input
                  type="file"
                  class="form-control opacity-0 position-absolute w-100 h-100 top-0 start-0"
                  style="cursor: pointer; z-index: 2"
                  accept="image/webp,image/jpeg,image/png"
                  @change="handleEditImageSelect"
                />
                <div
                  v-if="!editingImage.newImage"
                  class="bg-dark bg-opacity-50 text-white p-2 rounded-2 d-inline-block"
                >
                  <i class="bi bi-camera me-1"></i>Ganti Foto
                </div>
              </div>
            </div>
            <small class="text-muted mt-2 d-block"
              >Biarkan kosong jika tidak ingin mengganti gambar.</small
            >
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-secondary" @click="closeEditModal">
            Batal
          </button>
          <button
            type="button"
            class="btn btn-primary px-4"
            @click="handleUpdateImage"
            :disabled="isUpdating"
          >
            {{ isUpdating ? 'Memperbarui...' : 'Simpan Perubahan' }}
          </button>
        </div>
        </div>
    </div>
  </AdminContentWrapper>
</template>

<style scoped>
.content-view {
  padding: 0;
}
.form-control,
.form-select {
  border-color: #e2e8f0;
  padding: 0.75rem 1rem;
}
.form-control:focus,
.form-select:focus {
  border-color: #033d4a;
  box-shadow: 0 0 0 3px rgba(3, 61, 74, 0.1);
}
.btn-primary {
  background-color: #033d4a;
  border-color: #033d4a;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background-color: #022f3a;
  border-color: #022f3a;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(3, 61, 74, 0.2);
}

/* Gallery Card Styles */
.gallery-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  background: white;
  border: 1px solid #f1f5f9 !important;
}

.gallery-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 30px rgba(3, 61, 74, 0.12) !important;
}

.card-img-wrapper {
  height: 180px;
  background-size: cover;
  background-position: center;
  background-color: #f8fafb;
}

.card-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(3, 61, 74, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: all 0.35s ease;
  backdrop-filter: blur(4px);
}

.gallery-card:hover .card-overlay {
  opacity: 1;
}

.action-buttons {
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  transform: translateY(20px);
}

.gallery-card:hover .action-buttons {
  transform: translateY(0);
}

.action-btn {
  width: 38px;
  height: 38px;
  border-radius: 12px;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  color: #033d4a;
  transition: all 0.25s ease;
}

.action-btn:hover {
  transform: scale(1.15);
  background: #f8fafc;
}

.featured-btn.active {
  background: #ffc107;
  color: white;
}

.edit-btn:hover {
  background: #3b82f6;
  color: white;
}

.delete-btn:hover {
  background: #dc3545;
  color: white;
}

.category-badge {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(4px);
  color: #033d4a;
  font-size: 0.65rem;
  font-weight: 800;
  padding: 5px 12px;
  border-radius: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.featured-badge {
  background: #ffc107;
  color: white;
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  font-size: 0.8rem;
}

.smaller {
  font-size: 0.75rem;
}

.pointer-events-none {
  pointer-events: none;
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

.category-tag {
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #033d4a;
  background: rgba(3, 61, 74, 0.05);
  padding: 4px 10px;
  border-radius: 6px;
  letter-spacing: 0.5px;
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(3, 61, 74, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
  backdrop-filter: blur(4px);
}

.modal-content-custom {
  background-color: white;
  border-radius: 1.5rem;
  width: 100%;
  max-width: 550px;
  max-height: 90vh;
  overflow-y: auto;
  padding: 2rem;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
}

.upload-area {
  border: 2px dashed #e2e8f0;
  transition: all 0.3s ease;
  background: #f8fafb;
  cursor: pointer;
  border-radius: 1rem;
}

.upload-area.drag-over {
  border-color: #033d4a;
  background: rgba(3, 61, 74, 0.05);
}

.upload-area.has-file {
  border-style: solid;
  border-color: #033d4a;
  padding: 1rem;
}

.preview-container {
  position: relative;
  border-radius: 0.75rem;
  overflow: hidden;
}

.preview-image {
  width: 100%;
  height: 250px;
  object-fit: cover;
}

.form-control,
.form-select {
  border-radius: 10px;
  padding: 0.75rem 1rem;
  border-color: #e2e8f0;
  background-color: #f8fafb;
}

.form-control:focus {
  background-color: white;
  box-shadow: 0 0 0 4px rgba(3, 61, 74, 0.05);
}

.hero-preview-box {
  height: 250px;
  background-color: #f8fafb;
  position: relative;
}

.preview-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(3, 61, 74, 0.8);
  color: white;
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 0.75rem;
  backdrop-filter: blur(4px);
}

.admin-tabs .nav-link {
  color: #64748b;
  border-bottom: 2px solid transparent !important;
}

.admin-tabs .nav-link.active {
  color: #033d4a;
  border-bottom-color: #033d4a !important;
  background: transparent;
}

.transition-all {
  transition: all 0.3s ease;
}

.object-fit-cover {
  object-fit: cover;
}
</style>
