import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))
  const isAuthenticated = computed(() => !!token.value)

  const register = async (data) => {
    const response = await api.post('/auth/register', data)
    token.value = response.data.token
    user.value = response.data.user
    localStorage.setItem('token', token.value)
    api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    return response.data
  }

  const login = async (credentials) => {
    const response = await api.post('/auth/login', credentials)
    token.value = response.data.token
    user.value = response.data.user
    localStorage.setItem('token', token.value)
    api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    return response.data
  }

  const logout = async () => {
    try {
      await api.post('/auth/logout')
    } catch {}
    user.value = null
    token.value = null
    localStorage.removeItem('token')
    delete api.defaults.headers.common['Authorization']
  }

  const getMe = async () => {
    const response = await api.get('/auth/me')
    user.value = response.data
    return response.data
  }

  const initializeAuth = async () => {
    if (token.value) {
      api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      if (!user.value) {
        try {
          await getMe()
        } catch {
          token.value = null
          localStorage.removeItem('token')
          delete api.defaults.headers.common['Authorization']
        }
      }
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    register,
    login,
    logout,
    getMe,
    initializeAuth,
  }
})
