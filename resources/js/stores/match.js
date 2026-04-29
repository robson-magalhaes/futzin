import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useMatchStore = defineStore('match', () => {
  const matches = ref([])
  const currentMatch = ref(null)
  const loading = ref(false)

  const createMatch = async (data) => {
    loading.value = true
    try {
      const response = await api.post('/matches', data)
      matches.value.push(response.data)
      return response.data
    } finally {
      loading.value = false
    }
  }

  const fetchMatch = async (id) => {
    loading.value = true
    try {
      const response = await api.get(`/matches/${id}`)
      currentMatch.value = response.data
      return response.data
    } finally {
      loading.value = false
    }
  }

  const finishMatch = async (matchId, data) => {
    loading.value = true
    try {
      const response = await api.post(`/matches/${matchId}/finish`, data)
      currentMatch.value = response.data.match
      return response.data
    } finally {
      loading.value = false
    }
  }

  return {
    matches,
    currentMatch,
    loading,
    createMatch,
    fetchMatch,
    finishMatch,
  }
})
