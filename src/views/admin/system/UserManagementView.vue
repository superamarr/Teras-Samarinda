<script setup>
import { ref, computed, onMounted } from 'vue'
import { userService } from '@/api/users'
import { useSwal } from '@/composables/useSwal'
import AdminContentWrapper from '@/components/admin/AdminContentWrapper.vue'

const { success, error: showError, confirm, confirmDelete, toast } = useSwal()

// State
const users = ref([])
const isLoading = ref(true)
const searchQuery = ref('')
const showModal = ref(false)
const isSaving = ref(false)

// Form State
const formMode = ref('add') // 'add' or 'edit'
const userId = ref(null)
const formData = ref({
  username: '',
  role: 'admin',
  password: ''
})

const formRef = ref(null)
const wasValidated = ref(false)

const loadUsers = async () => {
  isLoading.value = true
  try {
    const res = await userService.getAll()
    if (res.data.success) {
      users.value = res.data.data
    }
  } catch (err) {
    showError('Gagal', 'Tidak dapat memuat data user')
  } finally {
    isLoading.value = false
  }
}

const filteredUsers = computed(() => {
  return users.value.filter(u => 
    u.username.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const openAddModal = () => {
  formMode.value = 'add'
  userId.value = null
  wasValidated.value = false
  formData.value = { username: '', role: 'admin', password: '' }
  showModal.value = true
}

const openEditModal = (user) => {
  formMode.value = 'edit'
  userId.value = user.id
  wasValidated.value = false
  formData.value = {
    username: user.username,
    role: user.role,
    password: '' // empty password so we don't accidentally update it
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveUser = async (event) => {
  const form = formRef.value
  if (!form.checkValidity()) {
    event.preventDefault()
    event.stopPropagation()
    wasValidated.value = true
    return
  }
  
  const result = await confirm('Simpan Perubahan', 'Apakah Anda yakin ingin menyimpan data akun ini?')
  if (!result.isConfirmed) return

  isSaving.value = true
  try {
    if (formMode.value === 'add') {
      await userService.create(formData.value)
      toast('success', 'User berhasil ditambahkan')
    } else {
      await userService.update(userId.value, formData.value)
      toast('success', 'Data user berhasil diperbarui')
    }
    closeModal()
    loadUsers()
  } catch (err) {
    showError('Gagal', err.response?.data?.message || 'Terjadi kesalahan saat menyimpan data.')
  } finally {
    isSaving.value = false
  }
}

const destroyUser = async (id) => {
  const result = await confirmDelete('akun user ini')
  if (result.isConfirmed) {
    try {
      await userService.delete(id)
      success('Dihapus!', 'Akun user telah dihapus.')
      loadUsers()
    } catch (err) {
      showError('Gagal', err.response?.data?.message || 'Tidak dapat menghapus user ini')
    }
  }
}

onMounted(loadUsers)
</script>

<template>
  <AdminContentWrapper
    pageTitle="Manajemen Sistem & User"
    pageDescription="Kelola akun tim admin dan batasan akses ke dashboard"
    showAddButton
    addButtonText="Tambah Akun Admin"
    @action-add="openAddModal"
  >
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
            placeholder="Cari username..."
          />
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else class="table-responsive bg-white rounded-4 border shadow-sm">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light border-bottom">
          <tr>
            <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">Informasi User</th>
            <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">Role / Hak Akses</th>
            <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">Tgl Dibuat</th>
            <th class="px-4 py-3 text-secondary small fw-bold text-uppercase text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="filteredUsers.length === 0">
            <td colspan="4" class="text-center py-5 text-secondary">
              <i class="bi bi-person-x fs-1 mb-3 d-block"></i>
              Tidak ada data user.
            </td>
          </tr>
          <tr v-for="user in filteredUsers" :key="user.id">
            <td class="px-4 py-3">
              <div class="d-flex align-items-center gap-3">
                <div class="user-avatar bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-person-fill fs-5"></i>
                </div>
                <div>
                  <div class="fw-bold text-dark">{{ user.username }}</div>
                </div>
              </div>
            </td>
            <td class="px-4 py-3">
              <span class="badge px-3 py-2 rounded-bill" :class="user.role === 'superadmin' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary'">
                <i class="bi me-1" :class="user.role === 'superadmin' ? 'bi-shield-check' : 'bi-person-badge'"></i>
                {{ user.role === 'superadmin' ? 'Super Administrator' : 'Administrator' }}
              </span>
            </td>
            <td class="px-4 py-3 text-secondary">
              {{ new Date(user.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) }}
            </td>
            <td class="px-4 py-3 text-end">
               <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                <button
                  @click="openEditModal(user)"
                  class="btn btn-white btn-sm px-3"
                  title="Edit"
                >
                  <i class="bi bi-pencil text-primary"></i>
                </button>
                <button
                  @click="destroyUser(user.id)"
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

    <!-- Modal User Form -->
    <div v-if="showModal" class="modal-backdrop fade show" style="z-index: 1040;"></div>
    <div 
      v-if="showModal" 
      class="modal fade show d-block" 
      tabindex="-1"
      style="z-index: 1050; background: rgba(0,0,0,0.5);"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
          <div class="modal-header bg-light border-bottom-0 pb-0 pt-4 px-4">
            <h5 class="modal-title fw-bold text-dark">
              <i class="bi bi-person-circle me-2 text-primary"></i>
              {{ formMode === 'add' ? 'Tambah Akun Admin Baru' : 'Edit Akun Admin' }}
            </h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body p-4">
            <form ref="formRef" @submit.prevent="saveUser" :class="{'was-validated': wasValidated}" novalidate>
              
              <div class="mb-3">
                <label class="form-label fw-medium small text-secondary">Username *</label>
                <input 
                  type="text" 
                  class="form-control border-2" 
                  v-model="formData.username" 
                  required 
                  :disabled="formMode === 'edit'"
                />
                <div class="invalid-feedback">Username wajb diisi.</div>
                <small v-if="formMode === 'edit'" class="text-danger">Username tidak dapat diubah setelah dibuat.</small>
              </div>
              
              <div class="mb-3">
                <label class="form-label fw-medium small text-secondary">Role / Hak Akses *</label>
                <select class="form-select border-2" v-model="formData.role" required>
                  <option value="admin">Administrator (Standard)</option>
                  <option value="superadmin">Super Administrator (Akses Sistem Utama)</option>
                </select>
              </div>
              
              <div class="mb-4">
                <label class="form-label fw-medium small text-secondary">
                  Password {{ formMode === 'add' ? '*' : '(Kosongkan jika tidak ingin diubah)' }}
                </label>
                <input 
                  type="password" 
                  class="form-control border-2" 
                  v-model="formData.password" 
                  :required="formMode === 'add'" 
                  minlength="6"
                />
                <div class="invalid-feedback">
                  {{ formMode === 'add' ? 'Password wajib diisi (minimal 6 karakter).' : 'Password harus minimal 6 karakter.' }}
                </div>
              </div>
              
              <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-light px-4 fw-medium text-secondary" @click="closeModal">Batal</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold" :disabled="isSaving">
                  <span v-if="isSaving" class="spinner-border spinner-border-sm me-2"></span>
                  Simpan Akun
                </button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminContentWrapper>
</template>

<style scoped>
.search-group .input-group-text {
  border-color: #e2e8f0;
}
.search-group .form-control:focus {
  border-color: #033D4A;
  z-index: 0;
  box-shadow: none;
}

.user-avatar {
  width: 42px;
  height: 42px;
  font-size: 1.25rem;
}

.bg-primary-subtle {
  background-color: #e0f2f1 !important;
}

.bg-danger-subtle {
  background-color: #fee2e2 !important;
}

.rounded-bill {
  border-radius: 50rem;
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
</style>
