<script setup>
import logoUrl from '@/assets/icons/logo.svg'
</script>

<template>
  <div class="maintenance-container d-flex flex-column align-items-center justify-content-center min-vh-100 bg-light text-center px-4">
    <div class="maintenance-content p-5 bg-white shadow-lg rounded-4 text-center">
      <img :src="logoUrl" alt="Logo TeraSamarinda" class="maintenance-logo mb-4" />
      <h1 class="fw-bold text-dark mb-3">Situs Sedang Dalam Perbaikan</h1>
      <p class="text-secondary fs-5 mb-4">
        Mohon maaf, saat ini website TeraSamarinda sedang dalam mode pemeliharaan (Maintenance). Kami sedang melakukan peningkatan sistem untuk pengalaman yang lebih baik. Silakan kembali beberapa saat lagi.
      </p>
    </div>
  </div>
</template>

<style scoped>
.maintenance-container {
  background: linear-gradient(135deg, #f0f7f9 0%, #e0f2f1 100%);
}

.maintenance-content {
  max-width: 600px;
  width: 100%;
  border-top: 5px solid #033D4A;
}

.maintenance-logo {
  width: 120px;
  height: auto;
}
</style>
