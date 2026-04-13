<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useSwal } from '@/composables/useSwal'
import loginBg from '@/assets/images/image 5.jpg'

const router = useRouter()
const authStore = useAuthStore()
const { error: showError } = useSwal()

const username = ref('')
const password = ref('')
const showPassword = ref(false)
const isLoading = ref(false)

const handleLogin = async () => {
  if (!username.value || !password.value) {
    await showError('Login Gagal', 'Username dan password harus diisi')
    return
  }

  isLoading.value = true

  const result = await authStore.login(username.value, password.value)

  if (result.success) {
    router.push('/admin/dashboard')
  } else {
    await showError('Login Gagal', result.message)
  }

  isLoading.value = false
}
</script>

<template>
  <div class="login-page container-fluid p-0">
    <div class="row g-0 vh-100">
      <!-- Left Side: Image & Message (Hidden on mobile) -->
      <div class="col-lg-6 d-none d-lg-block position-relative overflow-hidden p-4">
        <div class="branding-card h-100 position-relative rounded-4 overflow-hidden">
          <img :src="loginBg" alt="Login Background" class="login-bg-img" />
          <div class="branding-overlay"></div>

          <div class="branding-content p-5 d-flex flex-column h-100 justify-content-end">
            <h1
              v-motion-slide-visible-bottom
              class="text-white mb-3 serif-font"
              style="font-size: 46px; line-height: 1.1; letter-spacing: -0.5px"
            >
              Kelola <i>semuanya</i> dalam satu <br />
              dashboard <i>modern</i>.
            </h1>
            <p
              v-motion-slide-visible-bottom
              :delay="200"
              class="text-white-50 max-width-text"
              style="font-size: 18px"
            >
              Masuk untuk mengelola event, galeri, dan informasi Teras Samarinda. Hadirkan
              pengalaman wisata yang lebih menarik dan terorganisir.
            </p>
          </div>
        </div>
      </div>

      <!-- Right Side: Login Form -->
      <div class="col-lg-6 d-flex align-items-center justify-content-center p-4">
        <div class="login-form-container" v-motion-fade>
          <div class="text-center text-lg-start mb-5">
            <h2 class="display-5 serif-font mb-2">Selamat Datang Kembali!</h2>
            <p class="text-secondary">
              Masuk ke dashboard untuk mengelola konten, event, dan informasi Teras Samarinda dengan
              mudah.
            </p>
          </div>

          <form @submit.prevent="handleLogin">
            <div class="mb-4">
              <label class="form-label fw-semibold">Username</label>
              <input
                v-model="username"
                type="text"
                class="form-control form-control-lg custom-input"
                placeholder="Masukin username kamu"
                required
              />
            </div>

            <div class="mb-5">
              <label class="form-label fw-semibold">Password</label>
              <div class="password-input-wrapper">
                <input
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  class="form-control form-control-lg custom-input"
                  placeholder="Masukkan password"
                  required
                />
                <span class="password-toggle" @click="showPassword = !showPassword">
                  <i :class="showPassword ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                </span>
              </div>
            </div>

            <button
              type="submit"
              class="btn btn-teal btn-lg w-100 py-3 fw-bold"
              :disabled="isLoading"
            >
              <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
              {{ isLoading ? 'Masuk...' : 'Masuk Sekarang' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

.login-page {
  background-color: #ffffff;
  overflow-x: hidden;
}

.serif-font {
  font-family: var(--font-family-serif);
}

.branding-card {
  height: 100%;
}

.login-bg-img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.branding-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom, rgba(3, 61, 74, 0.2), rgba(3, 61, 74, 0.9));
}

.branding-content {
  position: relative;
  z-index: 2;
}

.branding-content p {
  font-family: 'Roboto', sans-serif;
}

.max-width-text {
  max-width: 450px;
}

.login-form-container {
  width: 100%;
  max-width: 450px;
}

.custom-input {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  padding: 0.75rem 1rem;
  font-size: 0.95rem;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.custom-input:focus {
  border-color: #033d4a;
  box-shadow: 0 0 0 3px rgba(3, 61, 74, 0.1);
}

.btn-teal {
  background: linear-gradient(to right, #0791b0, #033d4a);
  color: #ffffff;
  border: none;
  border-radius: 8px;
  font-family: 'Inter', sans-serif;
  font-size: 0.95rem;
  font-weight: 700;
  transition: all 0.3s ease;
}

.btn-teal:hover {
  background: linear-gradient(to right, #067a96, #022b35);
  transform: translateY(-2px);
}

.cursor-pointer {
  cursor: pointer;
}

.password-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.password-input-wrapper .custom-input {
  padding-right: 45px;
  width: 100%;
}

.password-toggle {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #6b7280;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  transition: color 0.2s ease;
}

.password-toggle:hover {
  color: #033d4a;
}

.password-toggle i {
  pointer-events: none;
}

@media (max-width: 991px) {
  .login-form-container {
    max-width: 400px;
  }
}
</style>
