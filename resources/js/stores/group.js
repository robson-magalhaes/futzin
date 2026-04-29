import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useGroupStore = defineStore('group', () => {
  const groups = ref([])
  const currentGroup = ref(null)
  const loading = ref(false)

  const fetchGroups = async () => {
    loading.value = true
    try {
      const response = await api.get('/groups')
      groups.value = response.data
    } finally {
      loading.value = false
    }
  }

  const fetchGroup = async (id) => {
    loading.value = true
    try {
      const response = await api.get(`/groups/${id}`)
      currentGroup.value = response.data
      return response.data
    } finally {
      loading.value = false
    }
  }

  const createGroup = async (data) => {
    const response = await api.post('/groups', data)
    groups.value.push(response.data)
    return response.data
  }

  const updateGroup = async (id, data) => {
    const response = await api.put(`/groups/${id}`, data)
    const index = groups.value.findIndex(g => g.id === id)
    if (index !== -1) groups.value[index] = response.data
    return response.data
  }

  const deleteGroup = async (id) => {
    await api.delete(`/groups/${id}`)
    groups.value = groups.value.filter(g => g.id !== id)
  }

  const joinByCode = async (code) => {
    const response = await api.post('/groups/join', { join_code: code })
    if (response.data.group) {
      groups.value.push(response.data.group)
    }
    return response.data
  }

  const addMember = async (groupId, userId) => {
    return await api.post(`/groups/${groupId}/add-member`, { user_id: userId })
  }

  const removeMember = async (groupId, userId) => {
    return await api.delete(`/groups/${groupId}/members/${userId}`)
  }

  const confirmPresence = async (groupId) => {
    return await api.post(`/groups/${groupId}/confirm-presence`)
  }

  const promoteMember = async (groupId, userId) => {
    return await api.put(`/groups/${groupId}/members/${userId}/promote`)
  }

  const demoteMember = async (groupId, userId) => {
    return await api.put(`/groups/${groupId}/members/${userId}/demote`)
  }

  const generateCode = async (groupId) => {
    const response = await api.post(`/groups/${groupId}/generate-code`)
    if (currentGroup.value?.id === groupId) {
      currentGroup.value.join_code = response.data.join_code
    }
    return response.data.join_code
  }

  const fetchPosts = async (groupId) => {
    const response = await api.get(`/groups/${groupId}/posts`)
    return response.data.data || response.data
  }

  const createPost = async (groupId, content) => {
    const response = await api.post(`/groups/${groupId}/posts`, { content })
    return response.data
  }

  const deletePost = async (groupId, postId) => {
    await api.delete(`/groups/${groupId}/posts/${postId}`)
  }

  return {
    groups,
    currentGroup,
    loading,
    fetchGroups,
    fetchGroup,
    createGroup,
    updateGroup,
    deleteGroup,
    joinByCode,
    addMember,
    removeMember,
    confirmPresence,
    promoteMember,
    demoteMember,
    generateCode,
    fetchPosts,
    createPost,
    deletePost,
  }
})
