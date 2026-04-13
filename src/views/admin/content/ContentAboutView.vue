<script setup>
import { ref, onMounted } from 'vue'
import { aboutService } from '@/api/about'
import { useSwal } from '@/composables/useSwal'
import { resolveMediaUrl } from '@/utils/media'
import AdminContentWrapper from '@/components/admin/AdminContentWrapper.vue'

const { success, error: showError, confirm, confirmDelete, toast, close: closeSwal } = useSwal()

// Tabs State
const activeTab = ref('section')
const tabs = [
  { id: 'section', name: 'Section Beranda', icon: 'bi-layout-text-window' },
  { id: 'hero', name: 'Header Halaman', icon: 'bi-image' },
  { id: 'welcome', name: 'Welcome Area', icon: 'bi-megaphone' },
  { id: 'story', name: 'Story Area', icon: 'bi-book' },
]

const aboutData = ref({
  title: '',
  subtitle: '',
  content: '',
  vision: '',
  mission: '',
  button_text: 'BACA SELENGKAPNYA',
  button_link: '/tentang',
  layout_type: 'default',
  page_hero_title: '',
  page_hero_subtitle: '',
  welcome_title: '',
  welcome_text: '',
  story_title: '',
  story_text: '',
  title_italic: []
})

const isSaving = ref(false)
const isLoading = ref(true)

// Previews
const previewLeft = ref('')
const previewRight = ref('')
const previewHero = ref('')
const previewWelcome = ref('')
const previewStory = ref('')

// Files to upload
const fileLeft = ref(null)
const fileRight = ref(null)
const fileHero = ref(null)
const fileWelcome = ref(null)
const fileStory = ref(null)

const loadAbout = async () => {
  isLoading.value = true
  try {
    const response = await aboutService.get()
    if (response.data.success && response.data.data) {
      const data = response.data.data
      aboutData.value = {
        title: data.title || '',
        subtitle: data.subtitle || '',
        content: data.content || '',
        vision: data.vision || '',
        mission: data.mission || '',
        button_text: data.button_text || 'BACA SELENGKAPNYA',
        button_link: data.button_link || '/tentang',
        layout_type: data.layout_type || 'default',
        page_hero_title: data.page_hero_title || '',
        page_hero_subtitle: data.page_hero_subtitle || '',
        welcome_title: data.welcome_title || '',
        welcome_text: data.welcome_text || '',
        story_title: data.story_title || '',
        story_text: data.story_text || '',
        title_italic: Array.isArray(data.title_italic) ? data.title_italic : []
      }
      if (data.image_left) previewLeft.value = resolveMediaUrl(data.image_left)
      if (data.image_right) previewRight.value = resolveMediaUrl(data.image_right)
      if (data.page_hero_background) previewHero.value = resolveMediaUrl(data.page_hero_background)
      if (data.welcome_image) previewWelcome.value = resolveMediaUrl(data.welcome_image)
      if (data.story_background) previewStory.value = resolveMediaUrl(data.story_background)
    }
  } catch (error) {
    console.error('Failed to load about data:', error)
    showError('Gagal!', 'Tidak dapat memuat data')
  } finally {
    isLoading.value = false
  }
}

const handleFileSelect = (event, side) => {
  const file = event.target.files[0]
  if (file) processFile(file, side)
}

const processFile = (file, side) => {
  const allowed = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png']
  if (!allowed.includes(file.type)) {
    showError('Format Tidak Valid', 'Hanya format WebP, JPG, dan PNG yang diizinkan')
    return
  }
  if (file.size > 5 * 1024 * 1024) {
    showError('File Terlalu Besar', 'Ukuran maksimal 5MB')
    return
  }

  const url = URL.createObjectURL(file)
  if (side === 'left') { fileLeft.value = file; previewLeft.value = url; }
  else if (side === 'right') { fileRight.value = file; previewRight.value = url; }
  else if (side === 'hero') { fileHero.value = file; previewHero.value = url; }
  else if (side === 'welcome') { fileWelcome.value = file; previewWelcome.value = url; }
  else if (side === 'story') { fileStory.value = file; previewStory.value = url; }
}

const removeImage = async (side) => {
  let fieldName = ''
  if (side === 'left') fieldName = 'image_left'
  else if (side === 'right') fieldName = 'image_right'
  else if (side === 'hero') fieldName = 'page_hero_background'
  else if (side === 'welcome') fieldName = 'welcome_image'
  else if (side === 'story') fieldName = 'story_background'

  const result = await confirmDelete('gambar ini')
  if (!result.isConfirmed) return

  try {
    await aboutService.deleteMedia(fieldName)
    if (side === 'left') { previewLeft.value = ''; fileLeft.value = null; }
    else if (side === 'right') { previewRight.value = ''; fileRight.value = null; }
    else if (side === 'hero') { previewHero.value = ''; fileHero.value = null; }
    else if (side === 'welcome') { previewWelcome.value = ''; fileWelcome.value = null; }
    else if (side === 'story') { previewStory.value = ''; fileStory.value = null; }
    
    toast('success', 'Gambar berhasil dihapus')
  } catch (err) {
    showError('Gagal!', 'Tidak dapat menghapus gambar')
  }
}

const saveAbout = async () => {
  const result = await confirm('Simpan Perubahan?', 'Anda yakin ingin menyimpan perubahan pada bagian About ini?')
  if (!result.isConfirmed) return

  isSaving.value = true
  try {
    const formData = new FormData()
    
    Object.keys(aboutData.value).forEach(key => {
      if (key === 'title_italic') {
        formData.append(key, JSON.stringify(aboutData.value[key]))
      } else {
        formData.append(key, aboutData.value[key])
      }
    })

    if (fileLeft.value) formData.append('image_left', fileLeft.value)
    if (fileRight.value) formData.append('image_right', fileRight.value)
    if (fileHero.value) formData.append('page_hero_background', fileHero.value)
    if (fileWelcome.value) formData.append('welcome_image', fileWelcome.value)
    if (fileStory.value) formData.append('story_background', fileStory.value)

    await aboutService.update(formData)
    toast('success', 'Perubahan About Section berhasil disimpan')
    await loadAbout()
    
    // Clear file objects post successful save
    fileLeft.value = null
    fileRight.value = null
    fileHero.value = null
    fileWelcome.value = null
    fileStory.value = null
  } catch (error) {
    showError('Gagal!', 'Tidak dapat menyimpan perubahan')
  } finally {
    isSaving.value = false
  }
}

const pageRoutes = [
  { label: 'Beranda (Home)', value: '/' },
  { label: 'Galeri Foto (GalleryView)', value: '/galeri' },
  { label: 'Daftar Event (EventsView)', value: '/events' },
  { label: 'Detail Event (EventDetailView)', value: '/events/1' },
  { label: 'Tentang Samarinda (AboutDetailView)', value: '/tentang' },
]

const getWords = (line) => {
  if (!line) return []
  return line.split(/\s+/).filter((w) => w.length > 0)
}

const isWordItalic = (word) => {
  return aboutData.value.title_italic.includes(word)
}

const toggleWordItalic = (word) => {
  const list = [...aboutData.value.title_italic]
  const index = list.indexOf(word)
  if (index > -1) {
    list.splice(index, 1)
  } else {
    list.push(word)
  }
  aboutData.value.title_italic = list
}

onMounted(loadAbout)
</script>

<template>
  <AdminContentWrapper
    pageTitle="Kelola About Section"
    pageDescription="Manajemen konten dan gambar pada halaman Tentang Samarinda."
    :tabs="tabs"
    v-model="activeTab"
    :showSaveButton="true"
    saveButtonText="Simpan Perubahan"
    saveButtonIcon="bi-check-circle"
    @action-save="saveAbout"
    :isSaving="isSaving"
  >
    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <template v-else>
        <!-- Tab: Section Beranda -->
        <div v-if="activeTab === 'section'" class="row g-4 fade-in">
          <!-- Main Content & Layout -->
          <div class="col-lg-8">
            <!-- Text Content Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
              <div class="card-body p-4">
                <h5 class="fw-bold mb-4 d-flex align-items-center">
                    <i class="bi bi-layout-text-window me-2 text-primary"></i>Informasi Section Beranda
                </h5>
                <div class="row g-3">
                  <div class="col-md-12 mb-3">
                    <label class="form-label small fw-bold text-uppercase text-secondary">Judul (Title)</label>
                    <input v-model="aboutData.title" type="text" class="form-control border-2 py-2 px-3 rounded-3" />
                    <div class="italic-control mt-2">
                        <small class="text-secondary d-block mb-2">Pilih kata untuk italic:</small>
                        <div class="word-toggles">
                          <button
                            v-for="word in getWords(aboutData.title)"
                            :key="'about-title-' + word"
                            type="button"
                            class="word-toggle"
                            :class="{ active: isWordItalic(word) }"
                            @click="toggleWordItalic(word)"
                          >
                            {{ word }}
                          </button>
                        </div>
                    </div>
                  </div>
                </div>

                <div class="mb-4">
                  <label class="form-label small fw-bold text-uppercase text-secondary">Konten Utama</label>
                  <textarea v-model="aboutData.content" class="form-control border-2 py-2 px-3 rounded-3" rows="4"></textarea>
                </div>

                <div class="row g-3">
                  <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-uppercase text-secondary">Teks Tombol</label>
                    <input v-model="aboutData.button_text" type="text" class="form-control border-2 py-2 px-3 rounded-3" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-uppercase text-secondary">Link Tombol</label>
                    <select v-model="aboutData.button_link" class="form-select border-2 py-2 px-3 rounded-3">
                      <option value="">Pilih Tujuan...</option>
                      <option v-for="route in pageRoutes" :key="route.value" :value="route.value">
                        {{ route.label }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- Media & Layout Settings -->
          <div class="col-lg-4">
            <!-- Layout Config -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
              <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Pengaturan Layout</h5>
                <div class="mb-0">
                  <label class="form-label small fw-bold text-uppercase text-secondary">Tipe Tampilan</label>
                  <select v-model="aboutData.layout_type" class="form-select border-2 py-2 px-3 rounded-3">
                    <option value="default">Default Design</option>
                    <option value="reversed">Reversed Position</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Image Left -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
              <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Gambar Kiri</h5>
                <div
                  class="upload-area border border-2 rounded-3 p-3 text-center bg-white"
                  :class="{ 'has-file': previewLeft }"
                >
                  <div v-if="previewLeft" class="preview-container">
                    <img :src="previewLeft" alt="Preview Left" class="preview-image" />
                    <button type="button" class="btn btn-danger btn-sm remove-btn" @click="removeImage('left')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                  <div v-else class="upload-placeholder">
                    <i class="bi bi-image fs-2 text-secondary mb-2"></i>
                    <p class="small text-secondary mb-2">Pilih gambar kiri</p>
                    <input type="file" class="form-control form-control-sm border-2 rounded-3" accept="image/*" @change="handleFileSelect($event, 'left')" />
                  </div>
                </div>
              </div>
            </div>

            <!-- Image Right -->
            <div class="card border-0 shadow-sm rounded-4 bg-light">
              <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Gambar Kanan</h5>
                <div
                  class="upload-area border border-2 rounded-3 p-3 text-center bg-white"
                  :class="{ 'has-file': previewRight }"
                >
                  <div v-if="previewRight" class="preview-container">
                    <img :src="previewRight" alt="Preview Right" class="preview-image" />
                    <button type="button" class="btn btn-danger btn-sm remove-btn" @click="removeImage('right')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                  <div v-else class="upload-placeholder">
                    <i class="bi bi-image fs-2 text-secondary mb-2"></i>
                    <p class="small text-secondary mb-2">Pilih gambar kanan</p>
                    <input type="file" class="form-control form-control-sm border-2 rounded-3" accept="image/*" @change="handleFileSelect($event, 'right')" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab: Hero -->
        <div v-if="activeTab === 'hero'" class="fade-in max-w-800 mx-auto">
            <div class="card border-0 bg-light rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-4 d-flex align-items-center">
                    <i class="bi bi-image text-primary me-2"></i> Pengaturan Hero (Halaman Detail)
                </h5>
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Judul Hero</label>
                        <input v-model="aboutData.page_hero_title" type="text" class="form-control border-2 py-2 px-3 rounded-3" />
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Sub-Judul Hero</label>
                        <textarea v-model="aboutData.page_hero_subtitle" class="form-control border-2 py-2 px-3 rounded-3" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Background Hero</label>
                        <div class="upload-area border border-2 rounded-3 p-3 text-center bg-white" :class="{ 'has-file': previewHero }">
                            <div v-if="previewHero" class="preview-container">
                                <img :src="previewHero" alt="Preview Hero" class="preview-image" />
                                <button type="button" class="btn btn-danger btn-sm remove-btn" @click="removeImage('hero')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div v-else class="upload-placeholder">
                                <i class="bi bi-upload fs-2 text-secondary mb-2"></i>
                                <p class="small text-secondary mb-2">Upload gambar untuk Background Hero</p>
                                <input type="file" class="form-control form-control-sm border-2 rounded-3 w-50 mx-auto" accept="image/*" @change="handleFileSelect($event, 'hero')" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Welcome Area -->
        <div v-if="activeTab === 'welcome'" class="fade-in max-w-800 mx-auto">
            <div class="card border-0 bg-light rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-4 d-flex align-items-center">
                    <i class="bi bi-megaphone text-success me-2"></i> Welcome Area (Halaman Detail)
                </h5>
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Judul Sambutan</label>
                        <input v-model="aboutData.welcome_title" type="text" class="form-control border-2 py-2 px-3 rounded-3" />
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Paragraf Sambutan</label>
                        <textarea v-model="aboutData.welcome_text" class="form-control border-2 py-2 px-3 rounded-3" rows="6" placeholder="Bisa dipisahkan dengan enter untuk membuat paragraf baru"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Gambar Sambutan (Bentuk Melengkung)</label>
                        <div class="upload-area border border-2 rounded-3 p-3 text-center bg-white" :class="{ 'has-file': previewWelcome }">
                            <div v-if="previewWelcome" class="preview-container">
                                <img :src="previewWelcome" alt="Preview Welcome" class="preview-image" style="aspect-ratio: 1/1" />
                                <button type="button" class="btn btn-danger btn-sm remove-btn" @click="removeImage('welcome')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div v-else class="upload-placeholder">
                                <i class="bi bi-camera fs-2 text-secondary mb-2"></i>
                                <p class="small text-secondary mb-2">Upload gambar (Rasio 1:1 atau Persegi disarankan)</p>
                                <input type="file" class="form-control form-control-sm border-2 rounded-3 w-50 mx-auto" accept="image/*" @change="handleFileSelect($event, 'welcome')" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Story Area -->
        <div v-if="activeTab === 'story'" class="fade-in max-w-800 mx-auto">
            <div class="card border-0 bg-light rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-4 d-flex align-items-center">
                    <i class="bi bi-book text-warning me-2"></i> Story Area (Halaman Detail)
                </h5>
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Judul Cerita/Sejarah</label>
                        <input v-model="aboutData.story_title" type="text" class="form-control border-2 py-2 px-3 rounded-3" />
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Paragraf Cerita</label>
                        <textarea v-model="aboutData.story_text" class="form-control border-2 py-2 px-3 rounded-3" rows="6" placeholder="Bisa dipisahkan dengan enter untuk membuat paragraf baru"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Background Area Cerita</label>
                        <div class="upload-area border border-2 rounded-3 p-3 text-center bg-white" :class="{ 'has-file': previewStory }">
                            <div v-if="previewStory" class="preview-container">
                                <img :src="previewStory" alt="Preview Story" class="preview-image" />
                                <button type="button" class="btn btn-danger btn-sm remove-btn" @click="removeImage('story')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div v-else class="upload-placeholder">
                                <i class="bi bi-image-fill fs-2 text-secondary mb-2"></i>
                                <p class="small text-secondary mb-2">Upload gambar untuk Background Story</p>
                                <input type="file" class="form-control form-control-sm border-2 rounded-3 w-50 mx-auto" accept="image/*" @change="handleFileSelect($event, 'story')" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </template>
  </AdminContentWrapper>
</template>

<style scoped>
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

.max-w-800 { max-width: 800px; }
.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Upload Area Styles */
.upload-area {
  border: 2px dashed #dee2e6;
  transition: all 0.3s ease;
  background: #f8fafb;
}
.upload-area.has-file {
  border-style: solid;
  padding: 0.5rem;
}
.preview-container {
  position: relative;
}
.preview-image {
  width: 100%;
  aspect-ratio: 16/9;
  object-fit: cover;
  border-radius: 0.5rem;
}
.remove-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Italic Control Styles */
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
  padding: 0.5rem 1rem;
  border: 1px solid #dee2e6;
  border-radius: 1rem;
  background-color: #ffffff;
  color: #495057;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.word-toggle:hover {
  background-color: #f8fafb;
  border-color: #033d4a;
  color: #033d4a;
  transform: translateY(-1px);
}
.word-toggle.active {
  background: linear-gradient(135deg, #033d4a 0%, #0791b0 100%);
  border-color: transparent;
  color: #ffffff;
  font-style: italic;
  box-shadow: 0 4px 12px rgba(3, 61, 74, 0.2);
  transform: translateY(-2px);
}
</style>
