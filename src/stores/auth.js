import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { authService } from '@/api/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const isAuthenticated = ref(false)
  const loading = ref(false)

  async function login(username, password) {
    loading.value = true
    try {
      const response = await authService.login(username, password)
      if (response.data.success) {
        user.value = response.data.data
        isAuthenticated.value = true
        return { success: true }
      }
      return { success: false, message: response.data.message }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Login failed',
      }
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await authService.logout()
    } catch (e) {
      // ignore
    }
    user.value = null
    isAuthenticated.value = false
  }

  async function checkAuth() {
    loading.value = true
    try {
      const response = await authService.check()
      if (response.data.success) {
        user.value = response.data.data
        isAuthenticated.value = true
        return true
      }
      return false
    } catch (error) {
      user.value = null
      isAuthenticated.value = false
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    user,
    isAuthenticated,
    loading,
    login,
    logout,
    checkAuth,
  }
})
