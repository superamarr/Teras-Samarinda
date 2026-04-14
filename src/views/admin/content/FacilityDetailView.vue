<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { facilityService } from '@/api/facilities'
import { useSwal } from '@/composables/useSwal'
import { resolveMediaUrl } from '@/utils/media'

const route = useRoute()
const router = useRouter()
const { success, error: showError, confirm, confirmDelete, close: closeSwal } = useSwal()

const isNew = computed(() => route.params.id === 'new' || !route.params.id)

const facility = ref({
  name: '',
  description: '',
  category: '',
  status: 'Aktif',
  image: '',
})

const isSaving = ref(false)
const isLoading = ref(true)
const imagePreview = ref('')
const selectedImage = ref(null)
const dragOverImage = ref(false)
const shouldDeleteImage = ref(false)

const loadFacility = async () => {
  if (isNew.value) {
    isLoading.value = false
    return
  }

  try {
    const response = await facilityService.getById(route.params.id)
    if (response.data.success && response.data.data) {
      const data = response.data.data
      facility.value = {
        name: data.name || '',
        description: data.description || '',
        category: data.category || '',
        status: data.status || 'Aktif',
        image: data.image || '',
      }
      if (data.image) {
        imagePreview.value = resolveMediaUrl(data.image)
      }
    }
  } catch (error) {
    console.error('Failed to load facility:', error)
    await showError('Gagal!', 'Tidak dapat memuat data fasilitas')
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
  if (!isNew.value && facility.value.image) {
    shouldDeleteImage.value = true
  }
}

const saveFacility = async () => {
  const result = await confirm('Simpan Perubahan?', isNew.value ? 'Anda yakin ingin menambah fasilitas baru?' : 'Anda yakin ingin menyimpan perubahan fasilitas ini?')
  if (!result.isConfirmed) return

  isSaving.value = true
  try {
    const formData = new FormData()
    Object.keys(facility.value).forEach((key) => {
      if (key !== 'image') {
        formData.append(key, facility.value[key])
      }
    })
    
    // Deletion flag for existing image
    if (shouldDeleteImage.value) {
      formData.append('delete_image', 'true')
    }

    // New image upload
    if (selectedImage.value) {
      formData.append('image', selectedImage.value)
    }

    if (isNew.value) {
      await facilityService.create(formData)
    } else {
      await facilityService.update(route.params.id, formData)
    }

    closeSwal()
    await success('Berhasil!', `Fasilitas berhasil ${isNew.value ? 'dibuat' : 'diperbarui'}!`)
    router.push('/admin/konten/facilities')
  } catch (error) {
    await showError('Gagal!', `Tidak dapat ${isNew.value ? 'membuat' : 'menyimpan'} fasilitas`)
  } finally {
    isSaving.value = false
  }
}

const goBack = () => router.push('/admin/konten/facilities')

onMounted(loadFacility)
</script>

<template>
  <div class="content-view shadow-sm rounded-4 bg-white p-4 overflow-hidden">
    <!-- Header Area -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4">
      <div class="d-flex align-items-center gap-3">
        <button @click="goBack" class="btn btn-outline-secondary rounded-circle btn-icon">
          <i class="bi bi-arrow-left"></i>
        </button>
        <div>
          <h2 class="fw-bold mb-0 text-dark">{{ isNew ? 'Tambah Fasilitas' : 'Edit Fasilitas' }}</h2>
          <p class="text-secondary small mb-0">{{ isNew ? 'Tambahkan sarana baru di area Teras Samarinda' : 'Perbarui informasi fasilitas pendukung' }}</p>
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else class="row g-4">
      <!-- Form Content -->
      <div class="col-lg-8">
        <div class="card border-0 bg-light rounded-4 mb-4 shadow-sm text-dark">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-4 d-flex align-items-center">
              <i class="bi bi-info-circle me-2 text-primary"></i>Informasi Fasilitas
            </h5>

            <div class="mb-4">
              <label class="form-label small fw-bold text-uppercase text-secondary">Nama Fasilitas</label>
              <input v-model="facility.name" type="text" class="form-control stylish-input" placeholder="Misal: Dermaga Wisata atau Area Parkir" />
            </div>

            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary">Kategori</label>
                <select v-model="facility.category" class="form-select stylish-input">
                  <option value="">Pilih kategori</option>
                  <option value="Utama">Utama</option>
                  <option value="Pendukung">Pendukung</option>
                  <option value="Umum">Umum</option>
                  <option value="Kesehatan">Kesehatan</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary">Status</label>
                <select v-model="facility.status" class="form-select stylish-input">
                  <option value="Aktif">Tersedia (Aktif)</option>
                  <option value="Tidak Aktif">Tidak Tersedia / Perbaikan</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label small fw-bold text-uppercase text-secondary">Deskripsi Singkat Fasilitas</label>
              <textarea v-model="facility.description" class="form-control stylish-input" rows="5" placeholder="Jelaskan fungsi dan kegunaan fasilitas ini bagi pengunjung..."></textarea>
            </div>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button @click="saveFacility" class="btn btn-primary px-5 py-3 rounded-3 shadow-sm fw-bold" :disabled="isSaving">
            <span v-if="isSaving" class="spinner-border spinner-border-sm me-2"></span>
            <i v-else class="bi bi-cloud-check me-2"></i>
            {{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
          <button @click="goBack" class="btn btn-outline-secondary px-4 py-3 rounded-3 fw-bold">Batalkan</button>
        </div>
      </div>

      <!-- Sidebar Area -->
      <div class="col-lg-4">
        <!-- Media Manager -->
        <div class="card border-0 bg-light rounded-4 mb-4">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-4 text-dark">Foto Fasilitas</h5>
            <div
              class="upload-area-premium rounded-4 border-2 border-dashed bg-white d-flex flex-column align-items-center justify-content-center overflow-hidden position-relative"
              :class="{ 'drag-over': dragOverImage }"
              @dragover.prevent="dragOverImage = true"
              @dragleave="dragOverImage = false"
              @drop="handleImageDrop"
            >
              <template v-if="imagePreview">
                <img :src="imagePreview" alt="Preview" class="w-100 h-100 object-fit-cover rounded-4" />
                <div class="image-actions position-absolute top-0 end-0 p-2 d-flex gap-2">
                  <button @click="removeImage" class="btn btn-danger btn-icon-sm rounded-circle shadow" title="Hapus Gambar"><i class="bi bi-trash"></i></button>
                  <label class="btn btn-light btn-icon-sm rounded-circle shadow cursor-pointer m-0" title="Ubah Gambar">
                    <i class="bi bi-camera"></i>
                    <input type="file" @change="handleImageSelect" class="d-none" accept="image/*" />
                  </label>
                </div>
              </template>
              <template v-else>
                <div class="p-4 text-center" @click="$refs.fileInput.click()">
                  <div class="upload-icon-circle mb-3 mx-auto"><i class="bi bi-building"></i></div>
                  <p class="text-dark fw-bold mb-1">Unggah Foto Fasilitas</p>
                  <p class="text-secondary small mb-3">Landscape (16:9) direkomendasikan</p>
                  <button class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">Pilih Dokumen</button>
                  <input ref="fileInput" type="file" class="d-none" @change="handleImageSelect" accept="image/*" />
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- Real-time Preview -->
        <div class="card border-0 bg-dark text-white rounded-4 overflow-hidden shadow-lg sticky-lg-top" style="top: 100px;">
          <div class="preview-hero-placeholder" :style="{ backgroundImage: imagePreview ? `url(${imagePreview})` : 'none', backgroundColor: '#033d4a' }">
            <div class="preview-overlay"></div>
            <div class="p-4 d-flex flex-column h-100 justify-content-end position-relative z-1">
              <span class="badge mb-2 w-fit-content" :class="facility.status === 'Aktif' ? 'bg-success' : 'bg-danger'">{{ facility.status }}</span>
              <h4 class="fw-bold mb-0">{{ facility.name || 'Judul Fasilitas' }}</h4>
              <p class="x-small opacity-50 mb-0 mt-1 uppercase ls-1">{{ facility.category || 'Kategori Fasilitas' }}</p>
            </div>
          </div>
          <div class="p-4 bg-dark">
             <p class="small opacity-75 text-truncate-3 lh-base mb-0">{{ facility.description || 'Deskripsi singkat fasilitas ini akan memberikan informasi penting bagi pengunjung yang memerlukannya di area Teras Samarinda.' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.stylish-input { border: 1px solid #e2e8f0; padding: 0.85rem 1rem; border-radius: 0.85rem; background-color: #ffffff; transition: all 0.2s ease; }
.stylish-input:focus { border-color: #033d4a; box-shadow: 0 0 0 4px rgba(3, 61, 74, 0.05); }

.btn-icon { width: 42px; height: 42px; padding: 0; display: flex; align-items: center; justify-content: center; }
.btn-icon-sm { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; }

.upload-area-premium { aspect-ratio: 16/9; min-height: 180px; transition: all 0.3s ease; cursor: pointer; }
.upload-area-premium.drag-over { border-color: #033d4a; background-color: rgba(3, 61, 74, 0.05) !important; }
.upload-icon-circle { width: 50px; height: 50px; border-radius: 50%; background-color: rgba(3, 61, 74, 0.06); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #033d4a; }

.preview-hero-placeholder { height: 160px; background-size: cover; background-position: center; position: relative; }
.preview-overlay { position: absolute; top:0; left:0; right:0; bottom:0; background: linear-gradient(to top, rgba(0,0,0,0.08), transparent); }

.text-truncate-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.ls-1 { letter-spacing: 1px; }
.w-fit-content { width: fit-content; }
.cursor-pointer { cursor: pointer; }
.z-1 { z-index: 1; }
.btn-primary { background-color: #033d4a; border-color: #033d4a; }
.btn-primary:hover { background-color: #022f3a; border-color: #022f3a; }
</style>
