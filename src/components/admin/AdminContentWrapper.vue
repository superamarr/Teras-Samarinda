<script setup>
const props = defineProps({
  pageTitle: { type: String, required: true },
  pageDescription: { type: String, default: '' },
  tabs: { type: Array, default: () => [] },
  modelValue: { type: String, default: '' },
  showAddButton: { type: Boolean, default: false },
  alwaysShowAddButton: { type: Boolean, default: false },
  addButtonText: { type: String, default: 'Tambah Data' },
  showSaveButton: { type: Boolean, default: false },
  saveButtonText: { type: String, default: 'Simpan Perubahan' },
  saveButtonIcon: { type: String, default: 'bi-check-circle' },
  isSaving: { type: Boolean, default: false }
})

const emit = defineEmits(['update:modelValue', 'action-add', 'action-save'])
</script>

<template>
  <div class="content-view shadow-sm rounded-4 bg-white overflow-hidden admin-content-wrapper">
    <!-- Header with Tabs -->
    <div class="px-4 pt-4 border-bottom bg-light header-area">
      <div class="d-flex justify-content-between align-items-center" :class="{ 'mb-4': tabs.length > 0, 'pb-4': tabs.length === 0 }">
        <div>
          <h2 class="fw-bold mb-1 text-dark">{{ pageTitle }}</h2>
          <p v-if="pageDescription" class="text-secondary small mb-0">{{ pageDescription }}</p>
        </div>
        <div class="d-flex gap-2">
          <!-- Slot for other header actions / buttons -->
          <slot name="header-actions"></slot>
          
          <button 
            v-if="showAddButton && (alwaysShowAddButton || modelValue === 'list' || tabs.length === 0)" 
            @click="emit('action-add')" 
            class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center"
          >
            <i class="bi bi-plus-lg me-2"></i>{{ addButtonText }}
          </button>
        </div>
      </div>

      <ul v-if="tabs && tabs.length > 0" class="nav nav-tabs border-0 gap-2 mt-2">
        <li v-for="tab in tabs" :key="tab.id" class="nav-item">
          <button
            @click="emit('update:modelValue', tab.id)"
            class="nav-link border-0 px-4 py-3 rounded-top-4 fw-bold transition-all user-select-none"
            :class="{ active: modelValue === tab.id }"
          >
            <i v-if="tab.icon" class="bi me-2" :class="tab.icon"></i>
            {{ tab.name }}
          </button>
        </li>
      </ul>
    </div>

    <!-- Tab Content Area -->
    <div class="p-4 bg-white min-vh-50 body-area d-flex flex-column">
      <div class="flex-grow-1">
        <slot></slot>
      </div>
      
      <!-- Footer Save Action -->
      <div v-if="showSaveButton" class="mt-5 pt-4 border-top d-flex justify-content-end align-items-center mt-auto">
        <button 
          @click="emit('action-save')" 
          class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center fw-bold"
          :disabled="isSaving"
        >
          <span v-if="isSaving" class="spinner-border spinner-border-sm me-2"></span>
          <i v-else-if="saveButtonIcon" :class="saveButtonIcon" class="me-2"></i>
          {{ saveButtonText }}
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.admin-content-wrapper { 
  min-height: 600px;
  display: flex;
  flex-direction: column;
}
.header-area {
  flex-shrink: 0;
}
.body-area {
  flex-grow: 1;
}

.nav-tabs {
  margin-bottom: -1px;
}
.nav-tabs .nav-link { 
  color: #64748b; 
  background-color: transparent;
}
.nav-tabs .nav-link.active {
  background-color: #fff;
  color: #033d4a;
  box-shadow: 0 -4px 10px rgba(0,0,0,0.03);
  position: relative;
  z-index: 2;
}
.nav-tabs .nav-link.active::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  right: 0;
  height: 2px;
  background: #fff;
}
.nav-tabs .nav-link:hover:not(.active) { 
  color: #033d4a; 
  background-color: rgba(3,61,74,0.05); 
}

.min-vh-50 { min-height: 50vh; }
.transition-all { transition: all 0.2s ease; }

/* Global Form Standard Styles strictly for scoped inside this wrapper if they cascade, but usually vue scoping prevents it. We will use these classes explicitly in the views instead. 
.form-control.border-2 {
    border-color: #e2e8f0;
}
.form-control.border-2:focus {
    border-color: #033d4a;
    box-shadow: 0 0 0 3px rgba(3, 61, 74, 0.1);
}
*/
</style>
