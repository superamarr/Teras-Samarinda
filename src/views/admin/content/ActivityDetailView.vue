<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { activityService } from '@/api/activities'
import { useSwal } from '@/composables/useSwal'
import { resolveMediaUrl } from '@/utils/media'

const route = useRoute()
const router = useRouter()
const { success, error: showError, confirm, confirmDelete, close: closeSwal } = useSwal()

const isNew = computed(() => route.params.id === 'new' || !route.params.id)

const activity = ref({
  name: '',
  category: '',
  image: '',
})

const isSaving = ref(false)
const isLoading = ref(true)
const imagePreview = ref('')
const selectedImage = ref(null)
const dragOverImage = ref(false)
const shouldDeleteImage = ref(false)

const loadActivity = async () => {
  if (isNew.value) {
    isLoading.value = false
    return
  }

  try {
    const response = await activityService.getById(route.params.id)
    if (response.data.success && response.data.data) {
      const data = response.data.data
      activity.value = {
        name: data.name || '',
        category: data.category || '',
        image: data.image || '',
      }
      if (data.image) {
        imagePreview.value = resolveMediaUrl(data.image)
      }
    }
  } catch (error) {
    console.error('Failed to load activity:', error)
    await showError('Gagal!', 'Tidak dapat memuat data aktivitas')
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
  if (!isNew.value && activity.value.image) {
    shouldDeleteImage.value = true
  }
}

const saveActivity = async () => {
  const result = await confirm('Simpan Perubahan?', isNew.value ? 'Anda yakin ingin menambah aktivitas baru?' : 'Anda yakin ingin menyimpan perubahan aktivitas ini?')
  if (!result.isConfirmed) return

  isSaving.value = true
  try {
    const formData = new FormData()
    Object.keys(activity.value).forEach((key) => {
      if (key !== 'image') {
        formData.append(key, activity.value[key])
      }
    })
    
    // Deletion flag
    if (shouldDeleteImage.value) {
      formData.append('delete_image', 'true')
    }

    // New image upload
    if (selectedImage.value) {
      formData.append('image', selectedImage.value)
    }

    if (isNew.value) {
      await activityService.create(formData)
    } else {
      await activityService.update(route.params.id, formData)
    }

    closeSwal()
    await success('Berhasil!', `Aktivitas berhasil ${isNew.value ? 'dibuat' : 'diperbarui'}!`)
    router.push('/admin/konten/activities')
  } catch (error) {
    await showError('Gagal!', `Tidak dapat ${isNew.value ? 'membuat' : 'menyimpan'} aktivitas`)
  } finally {
    isSaving.value = false
  }
}

const goBack = () => router.push('/admin/konten/activities')

onMounted(loadActivity)
</script>

<template>
  <div class="content-view shadow-sm rounded-4 bg-white p-4 overflow-hidden">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4">
      <div class="d-flex align-items-center gap-3">
        <button @click="goBack" class="btn btn-outline-secondary rounded-circle btn-icon">
          <i class="bi bi-arrow-left"></i>
        </button>
        <div>
          <h2 class="fw-bold mb-0 text-dark">{{ isNew ? 'Tambah Aktivitas' : 'Edit Aktivitas' }}</h2>
          <p class="text-secondary small mb-0">{{ isNew ? 'Definisikan aktivitas atau program menarik di Teras Samarinda' : 'Perbarui detail program yang sedang berjalan' }}</p>
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else class="row g-4">
      <!-- Main Form Area -->
      <div class="col-lg-8">
        <div class="card border-0 bg-light rounded-4 mb-4">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-4 d-flex align-items-center text-dark">
              <i class="bi bi-info-circle me-2 text-primary"></i>Informasi Utama
            </h5>

            <div class="mb-4">
              <label class="form-label small fw-bold text-uppercase text-secondary">Nama Aktivitas / Program</label>
              <input v-model="activity.name" type="text" class="form-control stylish-input" placeholder="Misal: Senam Pagi Bersama atau Live Music" />
            </div>

            <div class="mb-4">
              <label class="form-label small fw-bold text-uppercase text-secondary">Kategori</label>
              <select v-model="activity.category" class="form-select stylish-input">
                <option value="">Pilih kategori</option>
                <option value="Olahraga">Olahraga</option>
                <option value="Hiburan">Hiburan</option>
                <option value="Edukasi">Edukasi</option>
                <option value="Budaya">Budaya</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>

           </div>
        </div>

        <div class="d-flex gap-2">
          <button @click="saveActivity" class="btn btn-primary px-5 py-3 rounded-3 shadow-sm fw-bold" :disabled="isSaving">
            <span v-if="isSaving" class="spinner-border spinner-border-sm me-2"></span>
            <i v-else class="bi bi-check2-circle me-2"></i>
            {{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
          <button @click="goBack" class="btn btn-outline-secondary px-4 py-3 rounded-3 fw-bold">Batal</button>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <!-- Media Card -->
        <div class="card border-0 bg-light rounded-4 mb-4">
          <div class="card-body p-4 text-center">
            <h5 class="fw-bold mb-4 text-start">Gambar Aktivitas</h5>
            <div
              class="upload-area-premium rounded-4 border-2 border-dashed bg-white d-flex flex-column align-items-center justify-content-center overflow-hidden position-relative"
              :class="{ 'drag-over': dragOverImage }"
              @dragover.prevent="dragOverImage = true"
              @dragleave="dragOverImage = false"
              @drop="handleImageDrop"
            >
              <template v-if="imagePreview">
                <img :src="imagePreview" alt="Preview" class="w-100 h-100 object-fit-cover rounded-4" />
                <div class="image-actions position-absolute">
                  <button @click="removeImage" class="btn btn-danger btn-icon-sm rounded-circle shadow" title="Hapus Gambar"><i class="bi bi-trash"></i></button>
                  <label class="btn btn-light btn-icon-sm rounded-circle shadow cursor-pointer mx-0" title="Ganti Gambar"><i class="bi bi-camera"></i><input type="file" @change="handleImageSelect" class="d-none" accept="image/*" /></label>
                </div>
              </template>
              <template v-else>
                <div class="p-4" @click="$refs.fileInput.click()">
                  <div class="upload-icon-circle mb-3 mx-auto"><i class="bi bi-image"></i></div>
                  <p class="text-dark fw-bold mb-1">Unggah Foto Program</p>
                  <p class="text-secondary small mb-3">Format WebP/JPG/PNG. Max 5MB</p>
                  <button class="btn btn-sm btn-outline-primary rounded-pill px-3">Pilih File</button>
                  <input ref="fileInput" type="file" class="d-none" @change="handleImageSelect" accept="image/*" />
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- Preview Card -->
        <div class="card border-0 bg-dark text-white rounded-4 overflow-hidden shadow-lg sticky-lg-top" style="top: 100px;">
          <div class="preview-hero-placeholder" :style="{ backgroundImage: imagePreview ? `url(${imagePreview})` : 'none', backgroundColor: '#033d4a' }">
            <div class="preview-overlay"></div>
            <div class="p-4 d-flex flex-column h-100 justify-content-end position-relative z-1">
              <h4 class="fw-bold mb-1">{{ activity.name || 'Nama Aktivitas' }}</h4>
            </div>
          </div>
          <div class="p-4 text-center">
             <div class="rounded-pill bg-white bg-opacity-10 px-3 py-2 small d-inline-block">{{ activity.category || 'Program Kami' }}</div>
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

.upload-area-premium {
  width: 100%;
  max-width: 100%;
  min-width: 0;
  aspect-ratio: 16/9;
  min-height: 200px;
  transition: all 0.3s ease;
  box-sizing: border-box;
}
.upload-area-premium.drag-over { border-color: #033d4a; background-color: rgba(3, 61, 74, 0.05) !important; }
.upload-icon-circle { width: 60px; height: 60px; border-radius: 50%; background-color: rgba(3, 61, 74, 0.06); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #033d4a; }
.image-actions {
  top: 0.35rem;
  right: 0.35rem;
  display: flex;
  gap: 0.35rem;
  justify-content: flex-end;
  max-width: calc(100% - 0.7rem);
  z-index: 2;
}

.image-actions .btn-icon-sm {
  width: 26px;
  height: 26px;
  font-size: 0.72rem;
}

.upload-area-premium > img {
  display: block;
  width: 100%;
  max-width: 100%;
}

.preview-hero-placeholder { height: 180px; background-size: cover; background-position: center; position: relative; }
.preview-overlay { position: absolute; top:0; left:0; right:0; bottom:0; background: linear-gradient(to top, rgba(0,0,0,0.08), transparent); }

.text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.w-fit-content { width: fit-content; }
.cursor-pointer { cursor: pointer; }
.z-1 { z-index: 1; }
.btn-primary { background-color: #033d4a; border-color: #033d4a; }
.btn-primary:hover { background-color: #022f3a; border-color: #022f3a; }
</style>
