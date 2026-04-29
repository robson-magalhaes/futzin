<template>
  <div class="space-y-6">
    <router-link to="/groups" class="text-green-500 hover:underline">← Voltar para Grupos</router-link>

    <h1 class="text-3xl font-bold text-white">{{ group?.name }}</h1>

    <div class="grid md:grid-cols-3 gap-4">
      <div class="bg-slate-800 rounded-lg p-4 text-center">
        <p class="text-gray-400 mb-2">Membros</p>
        <p class="text-2xl font-bold text-blue-500">{{ group?.members?.length || 0 }}</p>
      </div>
      <div class="bg-slate-800 rounded-lg p-4 text-center">
        <p class="text-gray-400 mb-2">Mensalidade</p>
        <p class="text-2xl font-bold text-green-500">R$ {{ group?.monthly_fee }}</p>
      </div>
      <div class="bg-slate-800 rounded-lg p-4 text-center">
        <p class="text-gray-400 mb-2">Status</p>
        <p class="text-2xl font-bold text-yellow-500 capitalize">{{ group?.status }}</p>
      </div>
    </div>

    <div class="bg-slate-800 rounded-lg p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-white">Membros</h2>
        <button
          @click="showRanking = true"
          class="px-4 py-2 bg-blue-500 text-white rounded font-medium hover:bg-blue-600"
        >
          Ver Ranking
        </button>
      </div>
      <div class="space-y-2">
        <div v-for="member in group?.members" :key="member.id" class="bg-slate-700 p-3 rounded flex justify-between items-center">
          <div>
            <p class="font-bold text-white">{{ member.name }}</p>
            <p class="text-sm text-gray-400">{{ member.position }}</p>
          </div>
          <span class="text-xs bg-green-500 text-white px-2 py-1 rounded">{{ member.pivot.role }}</span>
        </div>
      </div>
    </div>

    <router-link :to="`/ranking/${group?.id}`" v-if="showRanking" class="hidden" />
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useGroupStore } from '@/stores/group'

const route = useRoute()
const groupStore = useGroupStore()
const group = ref(null)
const showRanking = ref(false)

onMounted(async () => {
  group.value = await groupStore.fetchGroup(route.params.id)
})
</script>
