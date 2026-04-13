<script setup>
import { ref, onMounted } from 'vue'
import { systemService } from '@/api/system'
import { useSwal } from '@/composables/useSwal'
import AdminContentWrapper from '@/components/admin/AdminContentWrapper.vue'

const { success, error: showError, toast } = useSwal()

const settings = ref({
  website_title: '',
  maintenance_mode: 0
})

const isLoading = ref(true)
const isSaving = ref(false)
const wasValidated = ref(false)
const formRef = ref(null)

const loadSettings = async () => {
  try {
    const res = await systemService.getSettings()
    if (res.data.success && res.data.data) {
      settings.value = {
        website_title: res.data.data.website_title,
        maintenance_mode: res.data.data.maintenance_mode
      }
    }
  } catch (error) {
    showError('Gagal!', 'Tidak dapat memuat pengaturan sistem')
  } finally {
    isLoading.value = false
  }
}

const saveSettings = async (event) => {
  const form = formRef.value
  if (form && !form.checkValidity()) {
    if (event) {
      event.preventDefault()
      event.stopPropagation()
    }
    wasValidated.value = true
    return
  }

  const result = await useSwal().confirm('Simpan Peraturan', 'Apakah Anda yakin ingin menyimpan perubahan konfigurasi website ini?')
  if (!result.isConfirmed) return
  
  isSaving.value = true
  try {
    const res = await systemService.updateSettings(settings.value)
    if (res.data.success) {
      document.title = settings.value.website_title
      toast('success', 'Pengaturan sistem berhasil disimpan')
    }
  } catch (error) {
    showError('Gagal!', error.response?.data?.message || 'Gagal menyimpan pengaturan')
  } finally {
    isSaving.value = false
  }
}

const toggleMaintenance = async () => {
  settings.value.maintenance_mode = settings.value.maintenance_mode === 1 ? 0 : 1
  await saveSettings()
}

onMounted(() => {
  loadSettings()
})
</script>

<template>
  <AdminContentWrapper
    pageTitle="Pengaturan Website"
    pageDescription="Kelola konfigurasi inti website, judul halaman, dan status maintenance."
  >
    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else class="row g-4 max-w-800">
      
      <!-- General Settings -->
      <div class="col-12">
        <div class="card border-0 bg-white shadow-sm rounded-4 p-4 h-100">
          <h5 class="fw-bold mb-4 d-flex align-items-center text-dark">
            <i class="bi bi-globe me-2 text-primary"></i> Identitas Website
          </h5>
          
          <form ref="formRef" @submit.prevent="saveSettings" :class="{'was-validated': wasValidated}" novalidate>
            <div class="mb-4">
              <label class="form-label fw-medium text-secondary">Judul Website (Browser Title) *</label>
              <input 
                v-model="settings.website_title" 
                type="text" 
                class="form-control border-2 py-2 px-3 focus-ring" 
                placeholder="Contoh: TeraSamarinda" 
                required
              />
              <div class="invalid-feedback">Judul website tidak boleh kosong.</div>
              <div class="form-text mt-2 text-secondary">
                Judul ini akan ditampilkan di judul tab peramban dan cuplikan mesin pencari (SEO).
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Maintenance Mode -->
      <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100" :class="settings.maintenance_mode === 1 ? 'bg-danger-subtle' : 'bg-white'">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h5 class="fw-bold d-flex align-items-center mb-2" :class="settings.maintenance_mode === 1 ? 'text-danger' : 'text-dark'">
                <i class="bi bi-tools me-2"></i> Mode Pemeliharaan (Maintenance)
              </h5>
              <p class="mb-0 text-secondary" style="max-width: 500px;">
                Jika mode ini diaktifkan, pengunjung reguler akan dialihkan ke halaman pemberitahuan perbaikan.
                Kamu (Admin) tetap bisa login dan mengakses dashboard.
              </p>
            </div>
            
            <div class="form-check form-switch custom-switch flex-shrink-0 ms-3 mt-1">
              <input 
                class="form-check-input mt-0 cursor-pointer" 
                type="checkbox" 
                role="switch" 
                id="maintenanceToggle"
                :checked="settings.maintenance_mode === 1"
                @change="toggleMaintenance"
              >
            </div>
          </div>
          
          <div v-if="settings.maintenance_mode === 1" class="alert alert-danger mb-0 mt-3 border-0 rounded-3 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
            <div>
              <strong>Perhatian!</strong> Website saat ini <u>tidak bisa diakses publik</u>. Semua trafiks pengunjung dialihkan.
            </div>
          </div>
          <div v-else class="alert alert-success mb-0 mt-3 border-0 rounded-3 bg-success-subtle text-success d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-3 fs-4"></i>
            <div>
              Website terpantau langsung. Mode pemeliharaan sedang dimatikan.
            </div>
          </div>
        </div>
      </div>

      <!-- Save Button -->
      <div class="col-12 mt-5 text-end">
        <button
          @click="(e) => saveSettings(e)"
          class="btn btn-primary px-5 py-2 rounded-3 fw-bold shadow-sm"
          :disabled="isSaving"
        >
          <span v-if="isSaving" class="spinner-border spinner-border-sm me-2"></span>
          Simpan Perubahan
        </button>
      </div>

    </div>
  </AdminContentWrapper>
</template>

<style scoped>
.max-w-800 {
  max-width: 800px;
}
.card {
  border-color: #f0f0f0 !important;
  transition: all 0.3s ease;
}
.bg-danger-subtle {
  background-color: #fee2e2 !important;
}
.text-danger {
  color: #dc2626 !important;
}
.custom-switch .form-check-input {
  width: 3.5rem;
  height: 1.75rem;
}
.custom-switch .form-check-input:checked {
  background-color: #dc2626;
  border-color: #dc2626;
}
.cursor-pointer {
  cursor: pointer;
}
</style>
