<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { heroService } from '@/api/hero'
import { useSwal } from '@/composables/useSwal'
import { resolveMediaUrl } from '@/utils/media'
import AdminContentWrapper from '@/components/admin/AdminContentWrapper.vue'

const { success, error: showError, confirm, confirmDelete, loading, close: closeSwal } = useSwal()

const heroData = ref({
  title_line1: 'Jelajahi Ikon Baru',
  title_line2: 'Kota Samarinda',
  title_line1_italic: [],
  title_line2_italic: [],
  subtitle: '',
  cta_text: 'LIHAT EVENT',
  cta_link: '/events',
  cta_text_secondary: 'BOOKING EVENT',
  cta_link_secondary: 'https://wa.me/6281522650048',
  use_video: false,
  video_file: '',
  background_image: '',
})

const isSaving = ref(false)
const isLoading = ref(true)
const imagePreview = ref('')
const videoPreview = ref('')
const selectedImage = ref(null)
const selectedVideo = ref(null)
const dragOverImage = ref(false)
const dragOverVideo = ref(false)
const uploadProgress = ref(0)

const pageRoutes = [
  { label: 'Beranda (Home)', value: '/' },
  { label: 'Galeri Foto (GalleryView)', value: '/galeri' },
  { label: 'Daftar Event (EventsView)', value: '/events' },
  { label: 'Detail Event (EventDetailView)', value: '/events/1' },
  { label: 'Tentang Samarinda (AboutDetailView)', value: '/tentang' },
]

const ctaLinkType = ref('internal')
const customCtaLink = ref('')

const onCtaLinkTypeChange = () => {
  if (ctaLinkType.value === 'custom') {
    heroData.value.cta_link = customCtaLink.value
  } else {
    customCtaLink.value = heroData.value.cta_link
    heroData.value.cta_link = ctaLinkType.value
  }
}

const getWords = (line) => {
  if (!line) return []
  return line.split(/\s+/).filter((w) => w.length > 0)
}

const isWordItalic = (lineKey, word) => {
  const italicArray = heroData.value[lineKey]
  if (!Array.isArray(italicArray)) return false
  return italicArray.includes(word)
}

const toggleWordItalic = (lineKey, word) => {
  const italicArray = heroData.value[lineKey]
  if (!Array.isArray(italicArray)) {
    heroData.value[lineKey] = []
  }

  const index = heroData.value[lineKey].indexOf(word)
  if (index > -1) {
    heroData.value[lineKey].splice(index, 1)
  } else {
    heroData.value[lineKey].push(word)
  }
}

const loadHero = async () => {
  try {
    const response = await heroService.get()
    if (response.data.success && response.data.data) {
      const data = response.data.data
      heroData.value = {
        title_line1: data.title_line1 || 'Jelajahi Ikon Baru',
        title_line2: data.title_line2 || 'Kota Samarinda',
        title_line1_italic: Array.isArray(data.title_line1_italic) ? data.title_line1_italic : [],
        title_line2_italic: Array.isArray(data.title_line2_italic) ? data.title_line2_italic : [],
        subtitle: data.subtitle || '',
        cta_text: data.cta_text || '',
        cta_link: data.cta_link || '/events',
        cta_text_secondary: data.cta_text_secondary || '',
        cta_link_secondary: data.cta_link_secondary || '',
        use_video: !!data.use_video,
        video_file: data.video_file || '',
        background_image: data.background_image || '',
      }

      const internalRoutes = pageRoutes.map((r) => r.value)
      if (internalRoutes.includes(heroData.value.cta_link)) {
        ctaLinkType.value = heroData.value.cta_link
      } else {
        ctaLinkType.value = 'custom'
        customCtaLink.value = heroData.value.cta_link
      }
      if (data.background_image) {
        imagePreview.value = resolveMediaUrl(data.background_image)
      }
      if (data.video_file) {
        videoPreview.value = resolveMediaUrl(data.video_file)
      }
    }
  } catch (err) {
    console.error('Failed to load hero data:', err)
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
    const confirmWebp = confirm(
      'File lebih dari 1MB. Disarankan menggunakan format WebP untuk performa lebih baik. Lanjutkan dengan format saat ini?',
    )
    if (!confirmWebp) return
  }

  selectedImage.value = file
  imagePreview.value = URL.createObjectURL(file)
}

const removeImage = async () => {
  const result = await confirmDelete('gambar')
  if (!result.isConfirmed) return

  try {
    await heroService.deleteMedia('image')
    heroData.value.background_image = ''
    imagePreview.value = ''
    selectedImage.value = null
    await success('Dihapus!', 'Gambar berhasil dihapus')
  } catch (err) {
    await showError('Gagal!', 'Tidak dapat menghapus gambar')
  }
}

const handleVideoSelect = (event) => {
  const file = event.target.files[0]
  if (file) processVideo(file)
}

const handleVideoDrop = (event) => {
  event.preventDefault()
  dragOverVideo.value = false
  const file = event.dataTransfer.files[0]
  if (file && file.type.startsWith('video/')) {
    processVideo(file)
  }
}

const processVideo = (file) => {
  const allowed = ['video/webm', 'video/mp4']

  if (!allowed.includes(file.type)) {
    showError('Format Tidak Valid', 'Hanya format WebM dan MP4 yang diizinkan')
    return
  }

  if (file.size > 50 * 1024 * 1024) {
    showError('File Terlalu Besar', 'Ukuran video maksimal 50MB')
    return
  }

  selectedVideo.value = file
  videoPreview.value = URL.createObjectURL(file)
}

const removeVideo = async () => {
  const result = await confirmDelete('video')
  if (!result.isConfirmed) return

  try {
    await heroService.deleteMedia('video')
    heroData.value.video_file = ''
    heroData.value.use_video = false
    videoPreview.value = ''
    selectedVideo.value = null
    await success('Dihapus!', 'Video berhasil dihapus')
  } catch (err) {
    await showError('Gagal!', 'Tidak dapat menghapus video')
  }
}

const saveHero = async () => {
  const result = await confirm(
    'Simpan Perubahan?',
    'Anda yakin ingin menyimpan perubahan pada Hero Section?',
  )
  if (!result.isConfirmed) return

  isSaving.value = true
  uploadProgress.value = 10

  try {
    const formData = new FormData()
    formData.append('title_line1', heroData.value.title_line1)
    formData.append('title_line2', heroData.value.title_line2)
    formData.append('title_line1_italic', JSON.stringify(heroData.value.title_line1_italic || []))
    formData.append('title_line2_italic', JSON.stringify(heroData.value.title_line2_italic || []))
    formData.append('subtitle', heroData.value.subtitle)
    formData.append('cta_text', heroData.value.cta_text)
    formData.append('cta_link', heroData.value.cta_link)
    formData.append('cta_text_secondary', heroData.value.cta_text_secondary)
    formData.append('cta_link_secondary', heroData.value.cta_link_secondary)
    formData.append('use_video', heroData.value.use_video ? '1' : '0')

    uploadProgress.value = 30

    if (selectedImage.value) {
      formData.append('background_image', selectedImage.value)
    }

    if (selectedVideo.value) {
      formData.append('video_file', selectedVideo.value)
    }

    uploadProgress.value = 50

    await heroService.update(formData, (progress) => {
      uploadProgress.value = 50 + progress * 0.5
    })

    uploadProgress.value = 100
    closeSwal()
    await success('Berhasil!', 'Hero section berhasil diperbarui')
    await loadHero()
  } catch (err) {
    console.error('Failed to save hero data:', err)
    await showError('Gagal!', 'Terjadi kesalahan saat menyimpan perubahan')
  } finally {
    isSaving.value = false
    uploadProgress.value = 0
  }
}

onMounted(() => {
  loadHero()
})
</script>

<template>
  <AdminContentWrapper
    pageTitle="Kelola Hero Section"
    pageDescription="Manajemen teks utama, tombol aksi, dan media latar belakang Hero halaman utama."
    :showSaveButton="true"
    saveButtonText="Simpan Perubahan"
    saveButtonIcon="bi-check-circle"
    @action-save="saveHero"
    :isSaving="isSaving"
  >
    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else class="row fade-in">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 bg-light mb-4">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-4 border-bottom border-2 pb-3">Judul Utama</h5>

            <div class="mb-4">
              <label class="form-label small fw-bold text-uppercase text-secondary">Baris 1</label>
              <input
                v-model="heroData.title_line1"
                type="text"
                class="form-control mb-2 border-2 py-2 px-3 rounded-3"
                placeholder="Contoh: Jelajahi Ikon Baru"
              />
              <div class="italic-control border-2">
                <small class="text-secondary d-block mb-2">Klik kata untuk toggle italic:</small>
                <div class="word-toggles">
                  <button
                    v-for="word in getWords(heroData.title_line1)"
                    :key="'line1-' + word"
                    type="button"
                    class="word-toggle"
                    :class="{ active: isWordItalic('title_line1_italic', word) }"
                    @click="toggleWordItalic('title_line1_italic', word)"
                  >
                    {{ word }}
                  </button>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label small fw-bold text-uppercase text-secondary">Baris 2</label>
              <input
                v-model="heroData.title_line2"
                type="text"
                class="form-control mb-2 border-2 py-2 px-3 rounded-3"
                placeholder="Contoh: Kota Samarinda"
              />
              <div class="italic-control border-2">
                <small class="text-secondary d-block mb-2">Klik kata untuk toggle italic:</small>
                <div class="word-toggles">
                  <button
                    v-for="word in getWords(heroData.title_line2)"
                    :key="'line2-' + word"
                    type="button"
                    class="word-toggle"
                    :class="{ active: isWordItalic('title_line2_italic', word) }"
                    @click="toggleWordItalic('title_line2_italic', word)"
                  >
                    {{ word }}
                  </button>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label small fw-bold text-uppercase text-secondary">Subtitle</label>
              <textarea
                v-model="heroData.subtitle"
                class="form-control border-2 py-2 px-3 rounded-3"
                rows="3"
                placeholder="Masukkan deskripsi singkat"
              ></textarea>
            </div>

            <h5 class="fw-bold mb-4 border-bottom border-2 pb-3 mt-5">
              <i class="bi bi-cursor-fill me-2 text-primary"></i>Tombol Aksi (CTA)
            </h5>

            <div class="row mb-4">
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary"
                  >Teks Tombol Utama</label
                >
                <input
                  v-model="heroData.cta_text"
                  type="text"
                  class="form-control border-2 py-2 px-3 rounded-3"
                  placeholder="Contoh: LIHAT EVENT"
                />
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary"
                  >Link Tujuan</label
                >
                <select
                  v-model="ctaLinkType"
                  @change="onCtaLinkTypeChange"
                  class="form-select border-2 py-2 px-3 rounded-3 mb-2"
                >
                  <option v-for="route in pageRoutes" :key="route.value" :value="route.value">
                    {{ route.label }}
                  </option>
                  <option value="custom">-- Custom URL --</option>
                </select>
                <input
                  v-if="ctaLinkType === 'custom'"
                  v-model="customCtaLink"
                  @input="heroData.cta_link = customCtaLink"
                  type="url"
                  class="form-control border-2 py-2 px-3 rounded-3"
                  placeholder="https://..."
                />
              </div>
            </div>

            <div class="row mb-4">
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary"
                  >Teks Tombol Sekunder</label
                >
                <input
                  v-model="heroData.cta_text_secondary"
                  type="text"
                  class="form-control border-2 py-2 px-3 rounded-3"
                  placeholder="Contoh: BOOKING EVENT"
                />
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-secondary"
                  >Link WhatsApp/External</label
                >
                <input
                  v-model="heroData.cta_link_secondary"
                  type="url"
                  class="form-control border-2 py-2 px-3 rounded-3"
                  placeholder="https://wa.me/628..."
                />
              </div>
            </div>

            <h5 class="fw-bold mb-4 border-bottom border-2 pb-3 mt-5">
              <i class="bi bi-play-circle me-2 text-primary"></i>Media Latar Belakang
            </h5>

            <div class="mb-4">
              <div class="form-check form-switch mb-3">
                <input
                  class="form-check-input"
                  type="checkbox"
                  role="switch"
                  id="useVideoSwitch"
                  v-model="heroData.use_video"
                />
                <label
                  class="form-check-label small fw-bold text-uppercase text-secondary ms-2"
                  for="useVideoSwitch"
                >
                  <i class="bi bi-film me-1"></i>
                  Gunakan Video sebagai background
                </label>
              </div>

              <div v-if="heroData.use_video" class="alert alert-info border-2 rounded-3">
                <i class="bi bi-info-circle me-2"></i>
                Upload video format <strong>WebM</strong>. Jika video gagal dimuat, gambar fallback
                akan ditampilkan. <br /><small
                  >Disarankan: kompres video untuk performa lebih baik.</small
                >
              </div>

              <div v-if="heroData.use_video" class="mb-3">
                <label class="form-label small fw-bold text-uppercase text-secondary">
                  <i class="bi bi-film me-1"></i>File Video
                </label>
                <div
                  class="upload-area border border-2 rounded-3 p-4 text-center bg-white"
                  :class="{ 'drag-over': dragOverVideo, 'has-file': videoPreview }"
                  @dragover.prevent="dragOverVideo = true"
                  @dragleave="dragOverVideo = false"
                  @drop="handleVideoDrop"
                >
                  <div v-if="videoPreview" class="preview-container">
                    <div class="video-preview">
                      <i class="bi bi-film fs-1 text-primary"></i>
                      <p class="mb-1 fw-semibold">Video Selected</p>
                      <small class="text-muted">{{
                        selectedVideo?.name || 'Video sebelumnya'
                      }}</small>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm mt-2" @click="removeVideo">
                      <i class="bi bi-trash me-1"></i> Hapus Video
                    </button>
                  </div>
                  <div v-else class="upload-placeholder">
                    <i class="bi bi-cloud-arrow-up fs-1 text-secondary"></i>
                    <p class="text-secondary mb-2">Klik atau drag file untuk upload</p>
                    <small class="text-muted">Format: WebM, MP4 (Max 50MB)</small>
                    <input
                      type="file"
                      class="form-control mt-3 border-2"
                      accept="video/webm,video/mp4"
                      @change="handleVideoSelect"
                    />
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold text-uppercase text-secondary">
                  <i class="bi bi-image me-1"></i>
                  {{ heroData.use_video ? 'Gambar Fallback' : 'Gambar Latar Belakang' }}
                </label>
                <div
                  class="upload-area border border-2 rounded-3 p-4 text-center bg-white"
                  :class="{ 'drag-over': dragOverImage, 'has-file': imagePreview }"
                  @dragover.prevent="dragOverImage = true"
                  @dragleave="dragOverImage = false"
                  @drop="handleImageDrop"
                >
                  <div v-if="imagePreview" class="preview-container">
                    <img :src="imagePreview" alt="Preview" class="preview-image" />
                    <button
                      type="button"
                      class="btn btn-danger btn-sm remove-btn"
                      @click="removeImage"
                    >
                      <i class="bi bi-trash"></i> Hapus
                    </button>
                  </div>
                  <div v-else class="upload-placeholder">
                    <i class="bi bi-cloud-arrow-up fs-1 text-secondary"></i>
                    <p class="text-secondary mb-2">Klik atau drag file untuk upload</p>
                    <small class="text-muted"
                      >Format: WebP, JPG, PNG (Max 5MB, disarankan WebP jika &gt;1MB)</small
                    >
                    <input
                      type="file"
                      class="form-control mt-3 border-2"
                      accept="image/webp,image/jpeg,image/png"
                      @change="handleImageSelect"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div v-if="isSaving" class="mb-3">
              <div class="progress" style="height: 10px">
                <div
                  class="progress-bar progress-bar-striped progress-bar-animated"
                  role="progressbar"
                  :style="{ width: uploadProgress + '%' }"
                ></div>
              </div>
              <small class="text-muted">Mengupload... {{ Math.round(uploadProgress) }}%</small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 sticky-top bg-light" style="top: 20px">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-eye me-2 text-primary"></i>Preview</h5>
            <div
              class="preview-box rounded-3 overflow-hidden"
              :style="{
                background: imagePreview
                  ? `linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url(${imagePreview}) center/cover`
                  : 'linear-gradient(135deg, #033d4a 0%, #0791b0 100%)',
              }"
            >
              <div class="preview-content p-3 text-white">
                <h2 class="preview-title mb-2">
                  <template
                    v-for="(word, index) in getWords(heroData.title_line1)"
                    :key="'prev-line1-' + index"
                  >
                    <span :class="{ 'fst-italic': isWordItalic('title_line1_italic', word) }">{{
                      word
                    }}</span
                    >{{ index < getWords(heroData.title_line1).length - 1 ? ' ' : '' }}
                  </template>
                  <br />
                  <template
                    v-for="(word, index) in getWords(heroData.title_line2)"
                    :key="'prev-line2-' + index"
                  >
                    <span :class="{ 'fst-italic': isWordItalic('title_line2_italic', word) }">{{
                      word
                    }}</span
                    >{{ index < getWords(heroData.title_line2).length - 1 ? ' ' : '' }}
                  </template>
                </h2>
                <p class="preview-subtitle small mb-3">
                  {{ heroData.subtitle || 'Subtitle akan muncul di sini...' }}
                </p>
                <div class="d-flex flex-column gap-2">
                  <button class="btn btn-light btn-sm fw-bold">
                    {{ heroData.cta_text || 'CTA 1' }}
                  </button>
                  <button class="btn btn-outline-light btn-sm fw-bold">
                    {{ heroData.cta_text_secondary || 'CTA 2' }}
                  </button>
                </div>
              </div>
              <div v-if="heroData.use_video" class="video-indicator p-2">
                <span class="badge bg-danger">
                  <i class="bi bi-play-fill me-1"></i>Video Mode
                </span>
              </div>
            </div>
            <div class="mt-3 p-3 bg-white border border-2 rounded-3 shadow-sm border-dashed">
              <h6 class="fw-bold mb-2">Tips:</h6>
              <ul class="small text-secondary mb-0 p-0 ps-3">
                <li>Klik kata untuk toggle <em>italic</em> pada kata tersebut</li>
                <li>Tombol <strong>aktif</strong> = kata akan tampil italic</li>
                <li>Gunakan video <strong>WebM</strong> untuk kompatibilitas browser terbaik</li>
                <li>Gunakan WebP untuk gambar &gt;1MB</li>
              </ul>
            </div>
          </div>
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
}
.btn-primary:hover {
  background-color: #022f3a;
  border-color: #022f3a;
}
.btn-primary:disabled {
  background-color: #6c757d;
  border-color: #6c757d;
}
.form-check-input:checked {
  background-color: #033d4a;
  border-color: #033d4a;
}
.upload-area {
  border: 2px dashed #dee2e6;
  transition: all 0.3s ease;
  background: #f8fafb;
}
.upload-area.drag-over {
  border-color: #033d4a;
  background: rgba(3, 61, 74, 0.05);
}
.upload-area.has-file {
  border-style: solid;
  padding: 1rem;
}
.preview-container {
  position: relative;
}
.preview-image {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 0.5rem;
}
.remove-btn {
  position: absolute;
  top: 10px;
  right: 10px;
}
.preview-box {
  min-height: 280px;
  position: relative;
}
.preview-content {
  position: relative;
  z-index: 1;
}
.preview-title {
  font-family: var(--font-family-serif), 'Instrument Serif', serif;
  font-size: 1.2rem;
  line-height: 1.2;
  white-space: pre-wrap;
  word-spacing: 0.1em;
}
.preview-subtitle {
  opacity: 0.9;
  line-height: 1.4;
}
.video-indicator {
  position: absolute;
  top: 0;
  right: 0;
}
.video-preview {
  padding: 1rem;
}
.alert-info {
  background-color: #e7f6ff;
  border-color: #b8e2ff;
  color: #0c5460;
}
.border-bottom {
  border-color: #e2e8f0 !important;
}
.border-top {
  border-color: #e2e8f0 !important;
}
.progress-bar {
  background-color: #033d4a;
}
.italic-control {
  padding: 0.75rem;
  background-color: #f8fafb;
  border-radius: 0.5rem;
  border: 1px solid #e2e8f0;
}
.word-toggles {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}
.word-toggle {
  padding: 0.35rem 0.75rem;
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  background-color: #ffffff;
  color: #495057;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}
.word-toggle:hover {
  background-color: #e9ecef;
  border-color: #adb5bd;
}
.word-toggle.active {
  background-color: #033d4a;
  border-color: #033d4a;
  color: #ffffff;
  font-style: italic;
}
</style>
