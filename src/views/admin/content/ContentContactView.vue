<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { contactService } from '@/api/contact'
import { useSwal } from '@/composables/useSwal'
import AdminContentWrapper from '@/components/admin/AdminContentWrapper.vue'
const router = useRouter()
const { success, error: showError, confirm, toast } = useSwal()

// Tabs State
const activeTab = ref('section')
const tabs = [
  { id: 'section', name: 'Section Beranda', icon: 'bi-layout-text-window' },
  { id: 'info', name: 'Detail Kontak & Peta', icon: 'bi-geo-alt' },
  { id: 'social', name: 'Media Sosial', icon: 'bi-share' },
]

// Data State
const contactData = ref({
  title: '',
  description: '',
  email: '',
  phone: '',
  whatsapp: '',
  address: '',
  facebook: '',
  instagram: '',
  youtube: '',
  mapEmbed: '',
  operatingHours: '',
  cta_text: '',
  cta_link: '',
  title_italic: []
})

const pageRoutes = [
  { label: 'Beranda (Home)', value: '/' },
  { label: 'Galeri Foto', value: '/galeri' },
  { label: 'Daftar Event', value: '/events' },
  { label: 'Tentang Samarinda', value: '/tentang' },
]

const isLoading = ref(true)
const isSaving = ref(false)

const loadContact = async () => {
  isLoading.value = true
  try {
    const response = await contactService.get()
    if (response.data.success && response.data.data) {
      const d = response.data.data
      contactData.value = { 
        ...contactData.value, 
        ...d,
        title_italic: Array.isArray(d.title_italic) ? d.title_italic : []
      }
    }
  } catch (error) {
    console.error('Failed to load contact data:', error)
    showError('Gagal!', 'Tidak dapat memuat data kontak')
  } finally {
    isLoading.value = false
  }
}

const saveContact = async () => {
  const result = await confirm('Simpan Perubahan', 'Apakah Anda yakin ingin menyimpan perubahan pada halaman kontak?')
  if (!result.isConfirmed) return
  
  isSaving.value = true
  try {
    const response = await contactService.update(contactData.value)
    if (response.data.success) {
      toast('success', 'Perubahan berhasil disimpan')
      await loadContact()
    } else {
      showError('Gagal!', response.data.message || 'Terjadi kesalahan saat menyimpan')
    }
  } catch (error) {
    showError('Gagal!', 'Tidak dapat menghubungi server')
  } finally {
    isSaving.value = false
  }
}

const getWords = (line) => {
  if (!line) return []
  return line.split(/\s+/).filter((w) => w.length > 0)
}

const isWordItalic = (field, word) => {
  if (field === 'title') {
    return contactData.value.title_italic.includes(word)
  }
  return false
}

const toggleWordItalic = (field, word) => {
  if (field === 'title') {
    const list = [...contactData.value.title_italic]
    const index = list.indexOf(word)
    if (index > -1) {
      list.splice(index, 1)
    } else {
      list.push(word)
    }
    contactData.value.title_italic = list
  }
}

onMounted(loadContact)
</script>

<template>
  <AdminContentWrapper
    pageTitle="Manajemen Kontak"
    pageDescription="Kelola informasi lokasi, jam operasional, dan link sosial media"
    :tabs="tabs"
    v-model="activeTab"
    :showSaveButton="true"
    saveButtonText="Simpan Perubahan"
    saveButtonIcon="bi-check-lg"
    @action-save="saveContact"
    :isSaving="isSaving"
  >

    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
    </div>

    <template v-else>
      <!-- Tab: Section Settings -->
      <div v-if="activeTab === 'section'" class="fade-in max-w-800">
        <div class="card border-0 bg-light rounded-4 p-4 mb-4">
          <h5 class="fw-bold mb-4 d-flex align-items-center">
            <i class="bi bi-megaphone me-2 text-primary"></i>Heading Section
          </h5>
          <div class="row g-4">
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-secondary"
                >Judul Section Utama</label
              >
              <input
                v-model="contactData.title"
                type="text"
                class="form-control border-2 py-2 px-3 rounded-3"
                placeholder="Contoh: Kunjungi Kami"
              />
              <div class="italic-control mt-2">
                <small class="text-secondary d-block mb-2">Pilih kata untuk italic:</small>
                <div class="word-toggles">
                  <button
                    v-for="word in getWords(contactData.title)"
                    :key="'title-' + word"
                    type="button"
                    class="word-toggle"
                    :class="{ active: isWordItalic('title', word) }"
                    @click="toggleWordItalic('title', word)"
                  >
                    {{ word }}
                  </button>
                </div>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-secondary"
                >Deskripsi Singkat</label
              >
              <textarea
                v-model="contactData.description"
                class="form-control border-2 py-2 px-3 rounded-3"
                rows="3"
                placeholder="Informasi singkat lokasi..."
              ></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-secondary"
                >Teks Tombol CTA</label
              >
              <input
                v-model="contactData.cta_text"
                type="text"
                class="form-control border-2 py-2 px-3 rounded-3"
              />
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-secondary"
                >Tujuan Link CTA</label
              >
              <input
                v-model="contactData.cta_link"
                type="text"
                class="form-control border-2 py-2 px-3 rounded-3"
                placeholder="https://..."
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Tab: Info & Maps -->
      <div v-if="activeTab === 'info'" class="fade-in max-w-800">
        <div class="card border-0 bg-light rounded-4 p-4 mb-4">
          <h5 class="fw-bold mb-4 d-flex align-items-center">
            <i class="bi bi-geo-alt-fill me-2 text-danger"></i>Informasi Fisik & Peta
          </h5>
          <div class="row g-4">
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-secondary"
                >Alamat Lengkap</label
              >
              <textarea
                v-model="contactData.address"
                class="form-control border-2 py-2 px-3 rounded-3"
                rows="2"
              ></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-secondary"
                >Jam Operasional</label
              >
              <input
                v-model="contactData.operatingHours"
                type="text"
                class="form-control border-2 py-2 px-3 rounded-3"
                placeholder="Contoh: Senin - Minggu (24 Jam)"
              />
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-secondary"
                >Email Kantor</label
              >
              <input
                v-model="contactData.email"
                type="email"
                class="form-control border-2 py-2 px-3 rounded-3"
              />
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-secondary"
                >Nomor Telepon</label
              >
              <input
                v-model="contactData.phone"
                type="text"
                class="form-control border-2 py-2 px-3 rounded-3"
              />
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-secondary"
                >Embed Google Maps (HTML Iframe)</label
              >
              <textarea
                v-model="contactData.mapEmbed"
                class="form-control border-2 py-2 px-3 rounded-3 text-code"
                rows="4"
                placeholder='<iframe src="..." ...></iframe>'
              ></textarea>
              <div v-if="contactData.mapEmbed" class="mt-3 p-2 bg-white border border-2 rounded-3">
                <p class="small text-secondary mb-2 fw-bold">
                  <i class="bi bi-eye me-1"></i>Map Preview:
                </p>
                <div class="map-preview" v-html="contactData.mapEmbed"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab: Social Media -->
      <div v-if="activeTab === 'social'" class="fade-in max-w-800">
        <div class="card border-0 bg-light rounded-4 p-4 mb-4">
          <h5 class="fw-bold mb-4 d-flex align-items-center">
            <i class="bi bi-share-fill me-2 text-info"></i>Link Media Sosial
          </h5>
          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-secondary"
                >Facebook URL</label
              >
              <div class="input-group">
                <span class="input-group-text bg-white border-2 border-end-0 py-2 px-3"
                  ><i class="bi bi-facebook text-primary"></i
                ></span>
                <input
                  v-model="contactData.facebook"
                  type="url"
                  class="form-control border-2 py-2 px-3"
                  placeholder="https://facebook.com/..."
                />
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-secondary"
                >Instagram URL</label
              >
              <div class="input-group">
                <span class="input-group-text bg-white border-2 border-end-0 py-2 px-3"
                  ><i class="bi bi-instagram text-danger"></i
                ></span>
                <input
                  v-model="contactData.instagram"
                  type="url"
                  class="form-control border-2 py-2 px-3"
                  placeholder="https://instagram.com/..."
                />
              </div>
            </div>
          </div>
          <div class="mt-4 p-3 bg-white border border-2 rounded-3 border-dashed">
            <p class="small text-secondary mb-0">
              <i class="bi bi-info-circle me-1"></i>Pastikan URL menggunakan format lengkap
              <strong>https://</strong>
            </p>
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

.max-w-800 {
  max-width: 800px;
}

.fade-in {
  animation: fadeIn 0.3s ease-out;
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

.text-code {
  font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
  font-size: 0.85rem;
  color: #033d4a;
}

.map-preview :deep(iframe) {
  width: 100%;
  height: 250px;
  border: 0;
  border-radius: 12px;
}

.border-dashed {
  border-style: dashed !important;
}

.transition-all {
  transition: all 0.2s ease-in-out;
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
