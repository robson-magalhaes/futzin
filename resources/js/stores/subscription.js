import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useSubscriptionStore = defineStore('subscription', () => {
  const subscription = ref(null)
  const loading = ref(false)

  const fetchCurrent = async () => {
    loading.value = true
    try {
      const response = await api.get('/subscriptions/current')
      subscription.value = response.data
      return response.data
    } catch (error) {
      subscription.value = null
      return null
    } finally {
      loading.value = false
    }
  }

  const subscribe = async (plan) => {
    loading.value = true
    try {
      const response = await api.post('/subscriptions', { plan })
      subscription.value = response.data.subscription
      return response.data
    } finally {
      loading.value = false
    }
  }

  const cancel = async () => {
    loading.value = true
    try {
      const response = await api.post('/subscriptions/cancel')
      subscription.value = null
      return response.data
    } finally {
      loading.value = false
    }
  }

  return {
    subscription,
    loading,
    fetchCurrent,
    subscribe,
    cancel,
  }
})
