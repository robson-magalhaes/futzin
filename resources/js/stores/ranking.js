import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useRankingStore = defineStore('ranking', () => {
  const rankings = ref([])
  const playerStats = ref(null)
  const loading = ref(false)

  const fetchGroupRanking = async (groupId) => {
    loading.value = true
    try {
      const response = await api.get(`/rankings/group/${groupId}`)
      rankings.value = response.data
      return response.data
    } finally {
      loading.value = false
    }
  }

  const fetchPlayerStats = async (playerId, groupId) => {
    loading.value = true
    try {
      const response = await api.get(`/rankings/player/${playerId}/group/${groupId}`)
      playerStats.value = response.data
      return response.data
    } finally {
      loading.value = false
    }
  }

  return {
    rankings,
    playerStats,
    loading,
    fetchGroupRanking,
    fetchPlayerStats,
  }
})
