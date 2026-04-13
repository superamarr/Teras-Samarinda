<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { eventService } from '@/api/events'
import { useSwal } from '@/composables/useSwal'
import { resolveMediaUrl } from '@/utils/media'

const route = useRoute()
const router = useRouter()
const { success, error: showError, confirm, confirmDelete, close: closeSwal } = useSwal()

const isNew = computed(() => route.params.id === 'new' || !route.params.id)

const event = ref({
  name: '',
  category: '',
  date: '',
  endDate: '',
  location: '',
  description: '',
  highlights: '',
  ticketPrice: '',
  status: 'Aktif',
  isFeatured: false,
  image: '',
})

const isSaving = ref(false)
const isLoading = ref(true)
const imagePreview = ref('')
const selectedImage = ref(null)
const dragOverImage = ref(false)
const shouldDeleteImage = ref(false)

const loadEvent = async () => {
  if (isNew.value) {
    isLoading.value = false
    return
  }

  try {
    const response = await eventService.getById(route.params.id)
    if (response.data.success && response.data.data) {
      const data = response.data.data
      event.value = {
        name: data.name || '',
        category: data.category || '',
        date: data.date || '',
        endDate: data.end_date || '',
        location: data.location || '',
        description: data.description || '',
        highlights: data.highlights || '',
        ticketPrice: data.ticket_price || '',
        status: data.status || 'Aktif',
        isFeatured: data.is_featured == 1,
        image: data.image || '',
      }
      if (data.image) {
        imagePreview.value = resolveMediaUrl(data.image)
      }
    }
  } catch (error) {
    console.error('Failed to load event:', error)
    await showError('Gagal!', 'Tidak dapat memuat data event')
  } finally {
    isLoading.value = false
  }
}

const handleImageSelect = (event) => {
  const file = event.target.files[0]
  if (file) processImage(file)
}

const handleImageDrop = (event) => {
  event.preventDefault()
  dragOverImage.value = false
  const file = event.dataTransfer.files[0]
  if (file && file.type.startsWith('image/')) {
    processImage(file)
  }
}

const processImage = (file) => {
  const allowed = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png']

  if (!allowed.includes(file.type)) {
    showError('Format Tidak Valid', 'Hanya format WebP, JPG, dan PNG yang diizinkan')
    return
  }

  if (file.size > 5 * 1024 * 1024) {
    showError('File Terlalu Besar', 'Ukuran maksimal 5MB')
    return
  }

  selectedImage.value = file
  imagePreview.value = URL.createObjectURL(file)
  shouldDeleteImage.value = false
}

const removeImage = () => {
  imagePreview.value = ''
  selectedImage.value = null
  if (!isNew.value && event.value.image) {
    shouldDeleteImage.value = true
  }
}

const saveEvent = async () => {
  const result = await confirm('Simpan Perubahan?', isNew.value ? 'Anda yakin ingin menambah event baru?' : 'Anda yakin ingin menyimpan perubahan event ini?')
  if (!result.isConfirmed) return

  isSaving.value = true
  try {
    const formData = new FormData()
    formData.append('name', event.value.name)
    formData.append('category', event.value.category)
    formData.append('date', event.value.date)
    formData.append('end_date', event.value.endDate)
    formData.append('location', event.value.location)
    formData.append('description', event.value.description)
    formData.append('highlights', event.value.highlights)
    formData.append('ticket_price', event.value.ticketPrice)
    formData.append('status', event.value.status)
    formData.append('is_featured', event.value.isFeatured ? '1' : '0')
    
    // Deletion flag
    if (shouldDeleteImage.value) {
      formData.append('delete_image', 'true')
    }

    // New image upload
    if (selectedImage.value) {
      formData.append('image', selectedImage.value)
    }

    if (isNew.value) {
      await eventService.create(formData)
    } else {
      await eventService.update(route.params.id, formData)
    }

    closeSwal()
    await success('Berhasil!', `Event berhasil ${isNew.value ? 'dibuat' : 'diperbarui'}!`)
    router.push('/admin/konten/events')
  } catch (error) {
    await showError('Gagal!', `Tidak dapat ${isNew.value ? 'membuat' : 'menyimpan'} event`)
  } finally {
    isSaving.value = false
  }
}

const goBack = () => router.push('/admin/konten/events')

onMounted(() => {
  loadEvent()
})
</script>

<template>
  <div class="content-view shadow-sm rounded-4 bg-white overflow-hidden p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div class="d-flex align-items-center gap-3">
        <button @click="goBack" class="btn btn-outline-secondary rounded-circle btn-icon">
          <i class="bi bi-arrow-left"></i>
        </button>
        <div>
          <h2 class="fw-bold mb-0 text-dark">{{ isNew ? 'Tambah Event Baru' : 'Edit Event' }}</h2>
          <p class="text-secondary small mb-0">{{ isNew ? 'Buat pengumuman event menarik di Teras Samarinda' : 'Perbarui detail event yang sudah ada' }}</p>
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else class="row g-4">
      <!-- Main Form -->
      <div class="col-lg-8">
        <div class="card border-0 bg-light rounded-4 mb-4">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-4 d-flex align-items-center text-dark">
              <i class="bi bi-info-circle me-2 text-primary"></i>Informasi Utama
            </h5>

            <div class="mb-4">
              <label class="form-label small fw-bold text-uppercase text-secondary">Nama Event</label>
              <input
                v-model="event.name"
                type="text"
                class="form-control stylish-input"
                placeholder="Masukkan nama event yang menarik"
              />
            </div>

            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <label class="form-label small fw-bold text-uppercase text-secondary">Kategori</label>
                <select v-model="event.category" class="form-select stylish-input">
                  <option value="">Pilih kategori</option>
                  <option value="Seni & Budaya">Seni & Budaya</option>
                  <option value="Festival">Festival</option>
                  <option value="Workshop">Workshop</option>
                  <option value="Pameran">Pameran</option>
                  <option value="Konser">Konser</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-bold text-uppercase text-secondary">Status Publikasi</label>
                <select v-model="event.status" class="form-select stylish-input">
                  <option value="Aktif">Aktif (Tampil)</option>
                  <option value="Tidak Aktif">Tidak Aktif (Draft)</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-bold text-uppercase text-secondary">Landing Page</label>
                  <div class="form-check form-switch stylish-input p-1 d-flex align-items-center justify-content-between ps-3 pe-3 text-secondary">
                    <label class="form-check-label mb-0 fw-semibold cursor-pointer" for="featuredSwitch" :class="{'text-primary': event.isFeatured, 'text-secondary': !event.isFeatured}">
                      <i class="bi" :class="event.isFeatured ? 'bi-star-fill text-warning me-1' : 'bi-star me-1'"></i> 
                      Featured
                    </label>
                    <input class="form-check-input m-0 ms-2 cursor-pointer" type="checkbox" id="featuredSwitch" v-model="event.isFeatured" style="transform: scale(1.1)">
                  </div>
                  <div v-if="event.isFeatured" class="small mt-1 text-secondary">
                    <i class="bi bi-info-circle me-1"></i> Maksimal 3 event di Beranda.
                  </div>
              </div>
            </div>

            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary">Tanggal Mulai</label>
                <input v-model="event.date" type="date" class="form-control stylish-input" />
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary">Tanggal Selesai</label>
                <input v-model="event.endDate" type="date" class="form-control stylish-input" />
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label small fw-bold text-uppercase text-secondary">Lokasi Spesifik</label>
              <input
                v-model="event.location"
                type="text"
                class="form-control stylish-input"
                placeholder="Misal: Area Waterfront atau Teras Utama"
              />
            </div>

            <div class="mb-4">
              <label class="form-label small fw-bold text-uppercase text-secondary">Deskripsi Lengkap</label>
              <textarea
                v-model="event.description"
                class="form-control stylish-input"
                rows="6"
                placeholder="Ceritakan detail event ini kepada pengunjung..."
              ></textarea>
            </div>

            <div class="mb-4">
              <label class="form-label small fw-bold text-uppercase text-secondary">Highlights / Poin Penting</label>
              <textarea
                v-model="event.highlights"
                class="form-control stylish-input"
                rows="4"
                placeholder="- Musik Live sepanjang malam&#10;- Kuliner Khas Samarinda&#10;- Gratis tanpa dipungut biaya"
              ></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label small fw-bold text-uppercase text-secondary">Info Tiket / Harga</label>
              <input
                v-model="event.ticketPrice"
                type="text"
                class="form-control stylish-input"
                placeholder="Contoh: Gratis / Terbuka untuk Umum, atau Rp 50.000"
              />
            </div>
          </div>
        </div>

        <div class="d-flex gap-2 mb-4">
          <button @click="saveEvent" class="btn btn-primary px-5 py-3 rounded-3 shadow-sm fw-bold" :disabled="isSaving">
            <i class="bi bi-cloud-upload me-2" v-if="!isSaving"></i>
            <span class="spinner-border spinner-border-sm me-2" v-else></span>
            {{ isSaving ? 'Sedang Menyimpan...' : 'Simpan Perubahan' }}
          </button>
          <button @click="goBack" class="btn btn-outline-secondary px-4 py-3 rounded-3 fw-bold">Batalkan</button>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <!-- Image Upload Card -->
        <div class="card border-0 bg-light rounded-4 mb-4">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-4 text-dark">Cover Event</h5>
            
            <div
              class="upload-area-premium rounded-4 border-2 border-dashed bg-white overflow-hidden d-flex flex-column align-items-center justify-content-center"
              :class="{ 'drag-over': dragOverImage }"
              @dragover.prevent="dragOverImage = true"
              @dragleave="dragOverImage = false"
              @drop="handleImageDrop"
            >
              <template v-if="imagePreview">
                <div class="image-preview-wrapper w-100 h-100 position-relative">
                  <img :src="imagePreview" alt="Preview" class="w-100 h-100 object-fit-cover" />
                  <div class="image-actions position-absolute top-0 end-0 p-2 d-flex gap-2">
                    <button @click="removeImage" class="btn btn-danger btn-icon-sm rounded-circle shadow-sm" title="Hapus Gambar">
                      <i class="bi bi-trash"></i>
                    </button>
                    <label for="replaceImage" class="btn btn-light btn-icon-sm rounded-circle shadow-sm cursor-pointer" title="Ganti Gambar">
                      <i class="bi bi-camera"></i>
                    </label>
                  </div>
                </div>
              </template>
              <template v-else>
                <div class="text-center p-5 cursor-pointer w-100" @click="$refs.fileInput.click()">
                  <div class="upload-icon-circle mb-3 mx-auto">
                    <i class="bi bi-image"></i>
                  </div>
                  <p class="text-dark fw-bold mb-1">Unggah Foto Event</p>
                  <p class="text-secondary small mb-3">Format WebP/JPG/PNG. Max 5MB</p>
                  <button class="btn btn-sm btn-outline-primary rounded-pill px-3">Pilih File</button>
                </div>
              </template>
              <input
                ref="fileInput"
                id="replaceImage"
                type="file"
                class="d-none"
                accept="image/webp,image/jpeg,image/png"
                @change="handleImageSelect"
              />
            </div>
            <div class="mt-2 text-secondary x-small text-center opacity-75">
              <i class="bi bi-info-circle me-1"></i>Gunakan gambar landscape (16:9) untuk hasil terbaik.
            </div>
          </div>
        </div>

        <!-- Quick Preview Card -->
        <div class="card border-0 bg-dark text-white rounded-4 overflow-hidden shadow-lg sticky-lg-top" style="top: 100px;">
          <div 
            class="preview-hero-placeholder"
            :style="{ 
              backgroundImage: imagePreview ? `url(${imagePreview})` : 'none',
              backgroundColor: imagePreview ? 'transparent' : '#033d4a'
            }"
          >
            <div class="preview-overlay"></div>
            <div class="p-4 d-flex flex-column h-100 justify-content-end position-relative z-1">
              <div class="d-flex gap-2 mb-2">
                <span class="badge d-inline-block w-fit-content" :class="event.status === 'Aktif' ? 'bg-success' : 'bg-secondary'">{{ event.status }}</span>
                <span v-if="event.isFeatured" class="badge bg-warning text-dark d-inline-flex align-items-center"><i class="bi bi-star-fill me-1"></i>Featured</span>
              </div>
              <h4 class="fw-bold mb-1">{{ event.name || 'Judul Event' }}</h4>
              <p class="small opacity-75 mb-0"><i class="bi bi-calendar-event me-2"></i>{{ event.date || 'Tgl Event' }}</p>
            </div>
          </div>
          <div class="p-4 bg-dark">
            <p class="small opacity-75 mb-3 text-truncate-2">{{ event.description || 'Deskripsi singkat event akan tampil di sini...' }}</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="fw-bold text-primary">{{ event.ticketPrice || 'Gratis' }}</span>
              <div class="rounded-pill bg-white bg-opacity-10 px-3 py-1 small">{{ event.category || 'Kategori' }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.stylish-input {
  border: 1px solid #e2e8f0;
  padding: 0.85rem 1rem;
  border-radius: 0.85rem;
  background-color: #ffffff;
  transition: all 0.2s ease;
}

.stylish-input:focus {
  border-color: #033d4a;
  box-shadow: 0 0 0 4px rgba(3, 61, 74, 0.05);
}

.btn-icon {
  width: 42px;
  height: 42px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-icon-sm {
  width: 32px;
  height: 32px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
}

/* Uploader UI */
.upload-area-premium {
  aspect-ratio: 16/9;
  transition: all 0.3s ease;
  min-height: 200px;
}

.upload-area-premium.drag-over {
  border-color: #033d4a;
  background-color: rgba(3, 61, 74, 0.05) !important;
}

.upload-icon-circle {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-color: rgba(3, 61, 74, 0.06);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: #033d4a;
}

.image-preview-wrapper:hover .image-actions {
  opacity: 1;
}

/* Quick Preview Styling */
.preview-hero-placeholder {
  height: 200px;
  background-size: cover;
  background-position: center;
  position: relative;
}

.preview-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.8), transparent 70%);
}

.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.w-fit-content { width: fit-content; }
.cursor-pointer { cursor: pointer; }
.x-small { font-size: 0.7rem; }
.z-1 { z-index: 1; }

.btn-primary {
  background-color: #033d4a;
  border-color: #033d4a;
}
.btn-primary:hover {
  background-color: #022f3a;
  border-color: #022f3a;
}
</style>
