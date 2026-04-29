<template>
  <div class="min-h-screen flex">
    <!-- Left panel (decorative, hidden on mobile) -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-green-900 via-slate-900 to-slate-950 relative overflow-hidden items-center justify-center flex-col gap-8 p-12">
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-64 h-64 rounded-full border-4 border-green-400"></div>
        <div class="absolute bottom-20 right-10 w-48 h-48 rounded-full border-4 border-green-400"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 rounded-full border border-green-400"></div>
      </div>
      <div class="text-center z-10">
        <div class="text-8xl mb-6">⚽</div>
        <h1 class="text-5xl font-black text-white mb-3">Futzin</h1>
        <p class="text-green-400 text-xl font-medium">Seu futebol organizado</p>
        <p class="text-gray-400 mt-4 max-w-xs text-sm leading-relaxed">
          Gerencie grupos, partidas, rankings e muito mais com seus amigos.
        </p>
      </div>
      <div class="flex gap-6 z-10">
        <div class="text-center">
          <p class="text-3xl font-black text-green-400">🏆</p>
          <p class="text-gray-400 text-xs mt-1">Ranking</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-black text-blue-400">👥</p>
          <p class="text-gray-400 text-xs mt-1">Grupos</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-black text-yellow-400">⭐</p>
          <p class="text-gray-400 text-xs mt-1">MVP</p>
        </div>
      </div>
    </div>

    <!-- Right panel: login form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 bg-slate-900">
      <div class="w-full max-w-md">
        <!-- Mobile logo -->
        <div class="lg:hidden text-center mb-10">
          <span class="text-5xl">⚽</span>
          <h1 class="text-3xl font-black text-white mt-2">Futzin</h1>
          <p class="text-green-400 text-sm mt-1">Seu futebol organizado</p>
        </div>

        <div class="bg-slate-800 rounded-2xl p-8 border border-slate-700 shadow-2xl">
          <h2 class="text-2xl font-bold text-white mb-1">Bem-vindo de volta!</h2>
          <p class="text-gray-400 text-sm mb-7">Entre na sua conta para continuar</p>

          <div v-if="errorMsg" class="bg-red-500/10 border border-red-500/50 text-red-400 px-4 py-3 rounded-xl text-sm mb-5 flex items-center gap-2">
            <span>⚠️</span> {{ errorMsg }}
          </div>

          <form @submit.prevent="handleLogin" class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
              <input
                v-model="form.email"
                type="email"
                autocomplete="email"
                class="w-full px-4 py-3 bg-slate-700 border rounded-xl text-white placeholder-gray-500 focus:outline-none transition text-sm"
                :class="errors.email ? 'border-red-500' : 'border-slate-600 focus:border-green-500'"
                placeholder="seu@email.com"
              />
              <p v-if="errors.email" class="text-red-400 text-xs mt-1.5">{{ Array.isArray(errors.email) ? errors.email[0] : errors.email }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-300 mb-1.5">Senha</label>
              <div class="relative">
                <input
                  v-model="form.password"
                  :type="showPass ? 'text' : 'password'"
                  autocomplete="current-password"
                  class="w-full px-4 py-3 pr-12 bg-slate-700 border rounded-xl text-white placeholder-gray-500 focus:outline-none transition text-sm"
                  :class="errors.password ? 'border-red-500' : 'border-slate-600 focus:border-green-500'"
                  placeholder="Sua senha"
                />
                <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-200 transition text-lg">
                  {{ showPass ? '🙈' : '👁️' }}
                </button>
              </div>
              <p v-if="errors.password" class="text-red-400 text-xs mt-1.5">{{ Array.isArray(errors.password) ? errors.password[0] : errors.password }}</p>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full py-3 bg-green-500 hover:bg-green-600 disabled:bg-green-900 disabled:text-green-700 text-white font-bold rounded-xl transition flex items-center justify-center gap-2 text-sm"
            >
              <svg v-if="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
              </svg>
              {{ loading ? 'Entrando...' : 'Entrar' }}
            </button>
          </form>

          <p class="text-center text-gray-400 text-sm mt-6">
            Não tem conta?
            <router-link to="/register" class="text-green-400 font-semibold hover:underline ml-1">Cadastre-se grátis</router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const form = ref({ email: '', password: '' })
const errors = ref({})
const errorMsg = ref('')
const loading = ref(false)
const showPass = ref(false)

const handleLogin = async () => {
  loading.value = true
  errors.value = {}
  errorMsg.value = ''

  try {
    await authStore.login(form.value)
    router.push('/dashboard')
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else if (error.response?.data?.message) {
      errorMsg.value = error.response.data.message
    } else {
      errorMsg.value = 'Erro ao conectar. Tente novamente.'
    }
  } finally {
    loading.value = false
  }
}
</script>
