<template>
  <div class="space-y-6">
    <h1 class="text-3xl font-bold text-white">Assinatura</h1>

    <div v-if="!subscription" class="space-y-4">
      <p class="text-gray-300">Você não possui uma assinatura ativa. Escolha um plano:</p>
      
      <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
          <h3 class="text-xl font-bold text-white mb-2">Básico</h3>
          <p class="text-3xl font-bold text-green-500 mb-4">R$ 29,90</p>
          <p class="text-gray-400 text-sm mb-4">/mês</p>
          <ul class="space-y-2 mb-6 text-sm text-gray-300">
            <li>✓ Participar de grupos</li>
            <li>✓ Ranking básico</li>
            <li>✓ Avaliações</li>
          </ul>
          <button @click="subscribe('basic')" :disabled="loading" class="w-full px-4 py-2 bg-green-500 text-white rounded font-medium hover:bg-green-600">
            {{ loading ? 'Processando...' : 'Assinar' }}
          </button>
        </div>

        <div class="bg-slate-800 rounded-lg p-6 border-2 border-green-500">
          <div class="bg-green-500 text-white px-3 py-1 rounded inline-block text-sm font-bold mb-2">Mais Popular</div>
          <h3 class="text-xl font-bold text-white mb-2">Premium</h3>
          <p class="text-3xl font-bold text-green-500 mb-4">R$ 59,90</p>
          <p class="text-gray-400 text-sm mb-4">/mês</p>
          <ul class="space-y-2 mb-6 text-sm text-gray-300">
            <li>✓ Tudo do Básico</li>
            <li>✓ Criar grupos</li>
            <li>✓ Gerenciar partidas</li>
            <li>✓ Relatórios avançados</li>
          </ul>
          <button @click="subscribe('premium')" :disabled="loading" class="w-full px-4 py-2 bg-green-500 text-white rounded font-medium hover:bg-green-600">
            {{ loading ? 'Processando...' : 'Assinar' }}
          </button>
        </div>

        <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
          <h3 class="text-xl font-bold text-white mb-2">Enterprise</h3>
          <p class="text-3xl font-bold text-green-500 mb-4">R$ 99,90</p>
          <p class="text-gray-400 text-sm mb-4">/mês</p>
          <ul class="space-y-2 mb-6 text-sm text-gray-300">
            <li>✓ Tudo do Premium</li>
            <li>✓ Suporte prioritário</li>
            <li>✓ API access</li>
            <li>✓ Customizações</li>
          </ul>
          <button @click="subscribe('enterprise')" :disabled="loading" class="w-full px-4 py-2 bg-green-500 text-white rounded font-medium hover:bg-green-600">
            {{ loading ? 'Processando...' : 'Assinar' }}
          </button>
        </div>
      </div>
    </div>

    <div v-else class="bg-slate-800 rounded-lg p-6">
      <h2 class="text-2xl font-bold text-white mb-4">Sua Assinatura</h2>
      <div class="space-y-4">
        <p class="text-gray-300">Plano: <span class="font-bold text-green-500 capitalize">{{ subscription.plan }}</span></p>
        <p class="text-gray-300">Valor: <span class="font-bold text-green-500">R$ {{ subscription.price }}</span>/mês</p>
        <p class="text-gray-300">Válida até: <span class="font-bold">{{ formatDate(subscription.ends_at) }}</span></p>
        
        <button
          @click="handleCancel"
          :disabled="loading"
          class="px-6 py-2 bg-red-500 text-white rounded font-medium hover:bg-red-600 disabled:bg-gray-500"
        >
          {{ loading ? 'Cancelando...' : 'Cancelar Assinatura' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useSubscriptionStore } from '@/stores/subscription'

const subscriptionStore = useSubscriptionStore()
const subscription = ref(null)
const loading = ref(false)

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

const subscribe = async (plan) => {
  loading.value = true
  try {
    const result = await subscriptionStore.subscribe(plan)
    subscription.value = result.subscription
  } finally {
    loading.value = false
  }
}

const handleCancel = async () => {
  if (confirm('Deseja realmente cancelar sua assinatura?')) {
    loading.value = true
    try {
      await subscriptionStore.cancel()
      subscription.value = null
    } finally {
      loading.value = false
    }
  }
}

onMounted(async () => {
  subscription.value = await subscriptionStore.fetchCurrent()
})
</script>
