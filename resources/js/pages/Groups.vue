<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
      <div>
        <h1 class="text-3xl font-black text-white">Grupos</h1>
        <p class="text-gray-400 text-sm mt-1">Crie ou entre em um grupo para jogar</p>
      </div>
      <button
        @click="showCreateForm = !showCreateForm"
        class="px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl font-medium transition text-sm flex items-center gap-2 w-fit"
      >
        <span class="text-lg">+</span> Novo Grupo
      </button>
    </div>

    <!-- Create Group Form -->
    <div v-if="showCreateForm" class="bg-slate-800 border border-slate-700 rounded-2xl p-6">
      <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
        <span>🏟️</span> Criar novo grupo
      </h3>
      <form @submit.prevent="handleCreateGroup" class="space-y-4">
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Nome do Grupo *</label>
            <input
              v-model="form.name"
              type="text"
              class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition text-sm"
              placeholder="Ex: Futebol da Terça"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Mensalidade (R$)</label>
            <input
              v-model.number="form.monthly_fee"
              type="number"
              step="0.01"
              min="0"
              class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition text-sm"
              placeholder="0.00"
            />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-300 mb-1.5">Descrição</label>
          <textarea
            v-model="form.description"
            rows="2"
            class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition text-sm resize-none"
            placeholder="Descreva o grupo..."
          ></textarea>
        </div>
        <div class="flex gap-2">
          <button
            type="submit"
            :disabled="createLoading || !form.name"
            class="px-5 py-2.5 bg-green-500 hover:bg-green-600 disabled:opacity-50 text-white rounded-xl font-medium transition text-sm flex items-center gap-2"
          >
            <svg v-if="createLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            {{ createLoading ? 'Criando...' : 'Criar Grupo' }}
          </button>
          <button type="button" @click="showCreateForm = false" class="px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-gray-300 rounded-xl font-medium transition text-sm">
            Cancelar
          </button>
        </div>
      </form>
    </div>

    <!-- Join by code -->
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6">
      <h3 class="text-lg font-bold text-white mb-1 flex items-center gap-2">
        <span>🔑</span> Entrar em um Grupo
      </h3>
      <p class="text-gray-400 text-sm mb-4">Peça o código de convite ao administrador e entre aqui</p>

      <div v-if="joinSuccess" class="bg-green-500/10 border border-green-500/50 text-green-400 px-4 py-3 rounded-xl text-sm mb-4 flex items-center gap-2">
        ✓ {{ joinSuccess }}
      </div>
      <div v-if="joinError" class="bg-red-500/10 border border-red-500/50 text-red-400 px-4 py-3 rounded-xl text-sm mb-4 flex items-center gap-2">
        ⚠️ {{ joinError }}
      </div>

      <form @submit.prevent="handleJoin" class="flex gap-3">
        <input
          v-model="joinCode"
          type="text"
          maxlength="6"
          class="flex-1 px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition text-sm font-mono tracking-widest uppercase"
          placeholder="CÓDIGO"
          @input="joinCode = joinCode.toUpperCase()"
        />
        <button
          type="submit"
          :disabled="joinLoading || joinCode.length < 4"
          class="px-6 py-3 bg-blue-500 hover:bg-blue-600 disabled:opacity-50 text-white rounded-xl font-medium transition text-sm flex items-center gap-2 whitespace-nowrap"
        >
          <svg v-if="joinLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
          </svg>
          {{ joinLoading ? 'Entrando...' : 'Entrar' }}
        </button>
      </form>
    </div>

    <!-- My Groups list -->
    <div>
      <h2 class="text-xl font-bold text-white mb-4">Meus Grupos</h2>

      <div v-if="groupStore.loading" class="text-center py-10 text-gray-400">
        <svg class="animate-spin w-8 h-8 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
        Carregando grupos...
      </div>

      <div v-else-if="groupStore.groups.length === 0" class="bg-slate-800 border border-dashed border-slate-600 rounded-2xl p-10 text-center">
        <p class="text-5xl mb-4">🏟️</p>
        <p class="text-white font-medium mb-1">Você não está em nenhum grupo</p>
        <p class="text-gray-400 text-sm">Crie um novo grupo ou entre com um código de convite</p>
      </div>

      <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="group in groupStore.groups"
          :key="group.id"
          class="bg-slate-800 border border-slate-700 hover:border-green-500/50 rounded-2xl p-5 transition group cursor-pointer"
          @click="$router.push(`/groups/${group.id}`)"
        >
          <div class="flex justify-between items-start mb-3">
            <div class="w-11 h-11 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center text-xl">⚽</div>
            <div class="flex gap-1.5 items-center">
              <span
                class="text-xs px-2.5 py-1 rounded-full font-medium"
                :class="myRole(group) === 'admin' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-slate-700 text-gray-400'"
              >
                {{ myRole(group) === 'admin' ? '⭐ Admin' : '👤 Jogador' }}
              </span>
            </div>
          </div>
          <h3 class="text-lg font-bold text-white group-hover:text-green-400 transition">{{ group.name }}</h3>
          <p v-if="group.description" class="text-gray-400 text-sm mt-1 line-clamp-2">{{ group.description }}</p>
          <div class="flex justify-between items-center mt-4 pt-3 border-t border-slate-700">
            <span class="text-sm text-gray-400">💰 R$ {{ Number(group.monthly_fee).toFixed(2) }}</span>
            <span
              class="text-xs px-2 py-0.5 rounded-full"
              :class="group.status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-slate-600 text-gray-400'"
            >
              {{ group.status === 'active' ? 'Ativo' : 'Inativo' }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useGroupStore } from '@/stores/group'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const groupStore = useGroupStore()
const authStore = useAuthStore()

const showCreateForm = ref(false)
const createLoading = ref(false)
const form = ref({ name: '', description: '', monthly_fee: 0 })

const joinCode = ref('')
const joinLoading = ref(false)
const joinSuccess = ref('')
const joinError = ref('')

onMounted(() => {
  groupStore.fetchGroups()
})

const myRole = (group) => {
  const member = group.members?.find(m => m.id === authStore.user?.id)
  return member?.pivot?.role || 'player'
}

const handleCreateGroup = async () => {
  createLoading.value = true
  try {
    const group = await groupStore.createGroup(form.value)
    form.value = { name: '', description: '', monthly_fee: 0 }
    showCreateForm.value = false
    router.push(`/groups/${group.id}`)
  } finally {
    createLoading.value = false
  }
}

const handleJoin = async () => {
  joinLoading.value = true
  joinSuccess.value = ''
  joinError.value = ''
  try {
    const data = await groupStore.joinByCode(joinCode.value)
    joinSuccess.value = data.message || 'Entrou no grupo com sucesso!'
    joinCode.value = ''
    setTimeout(() => {
      if (data.group) router.push(`/groups/${data.group.id}`)
    }, 1000)
  } catch (err) {
    joinError.value = err.response?.data?.message || 'Código inválido. Verifique e tente novamente.'
  } finally {
    joinLoading.value = false
  }
}
</script>
