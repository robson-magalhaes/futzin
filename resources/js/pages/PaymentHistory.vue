<template>
  <div class="space-y-6">
    <h1 class="text-3xl font-bold text-white">Meus Pagamentos</h1>

    <div class="bg-slate-800 rounded-lg overflow-hidden">
      <table class="w-full">
        <thead class="bg-slate-700">
          <tr>
            <th class="px-4 py-3 text-left text-gray-300">Grupo</th>
            <th class="px-4 py-3 text-left text-gray-300">Valor</th>
            <th class="px-4 py-3 text-left text-gray-300">Vencimento</th>
            <th class="px-4 py-3 text-left text-gray-300">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="payout in payouts" :key="payout.id" class="border-t border-slate-700 hover:bg-slate-700">
            <td class="px-4 py-3 text-white">{{ payout.group?.name }}</td>
            <td class="px-4 py-3 text-green-500 font-bold">R$ {{ payout.amount }}</td>
            <td class="px-4 py-3 text-gray-300">{{ formatDate(payout.due_date) }}</td>
            <td class="px-4 py-3">
              <span :class="getStatusClass(payout.status)" class="px-2 py-1 rounded text-xs font-bold">
                {{ payout.status }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import api from '@/services/api'

const payouts = ref([])

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

const getStatusClass = (status) => {
  const classes = {
    'paid': 'bg-green-500 text-white',
    'pending': 'bg-yellow-500 text-white',
    'overdue': 'bg-red-500 text-white',
  }
  return classes[status] || 'bg-gray-500 text-white'
}

onMounted(async () => {
  try {
    const response = await api.get('/payouts/my')
    payouts.value = response.data
  } catch (error) {
    console.error('Erro ao buscar pagamentos:', error)
  }
})
</script>
