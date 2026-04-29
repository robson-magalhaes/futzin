<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold text-white">Dashboard</h1>
      <router-link to="/groups" class="px-4 py-2 bg-green-500 text-white rounded font-medium hover:bg-green-600">
        + Novo Grupo
      </router-link>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="bg-slate-800 rounded-lg p-4 text-center">
        <p class="text-gray-400 mb-2">Assinatura</p>
        <p v-if="subscription" class="text-2xl font-bold text-green-500 capitalize">{{ subscription.plan }}</p>
        <p v-else class="text-2xl font-bold text-red-500">Inativa</p>
      </div>

      <div class="bg-slate-800 rounded-lg p-4 text-center">
        <p class="text-gray-400 mb-2">Grupos</p>
        <p class="text-2xl font-bold text-blue-500">{{ groupStore.groups.length }}</p>
      </div>

      <div class="bg-slate-800 rounded-lg p-4 text-center">
        <p class="text-gray-400 mb-2">Próximas Partidas</p>
        <p class="text-2xl font-bold text-yellow-500">2</p>
      </div>
    </div>

    <div class="bg-slate-800 rounded-lg p-6">
      <h2 class="text-xl font-bold text-white mb-4">Meus Grupos</h2>
      <div v-if="groupStore.groups.length === 0" class="text-gray-400">
        Nenhum grupo ainda. <router-link to="/groups" class="text-green-500 hover:underline">Criar um novo</router-link>
      </div>
      <div v-else class="space-y-2">
        <div v-for="group in groupStore.groups" :key="group.id" class="bg-slate-700 p-4 rounded flex justify-between items-center">
          <div>
            <h3 class="font-bold text-white">{{ group.name }}</h3>
            <p class="text-sm text-gray-400">Mensalidade: R$ {{ group.monthly_fee }}</p>
          </div>
          <router-link :to="`/groups/${group.id}`" class="text-green-500 hover:underline">Ver</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useGroupStore } from '@/stores/group'
import { useSubscriptionStore } from '@/stores/subscription'

const groupStore = useGroupStore()
const subscriptionStore = useSubscriptionStore()
const subscription = ref(null)

onMounted(async () => {
  await groupStore.fetchGroups()
  subscription.value = await subscriptionStore.fetchCurrent()
})
</script>
