<template>
  <div id="app" class="min-h-screen bg-linear-to-br from-slate-900 via-slate-900 to-slate-800">

    <header v-if="!isAuthPage && authStore.isAuthenticated" class="bg-slate-950/80 backdrop-blur border-b border-slate-800 sticky top-0 z-50">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <router-link to="/dashboard" class="flex items-center gap-2 text-white">
            <span class="text-2xl">⚽</span>
            <span class="text-xl font-black text-green-400">Futzin</span>
          </router-link>

          <!-- Desktop nav -->
          <div class="hidden md:flex items-center gap-6">
            <router-link to="/dashboard" class="text-gray-400 hover:text-white text-sm font-medium transition-colors" active-class="!text-green-400">Dashboard</router-link>
            <router-link to="/groups" class="text-gray-400 hover:text-white text-sm font-medium transition-colors" active-class="!text-green-400">Grupos</router-link>
            <router-link to="/matches" class="text-gray-400 hover:text-white text-sm font-medium transition-colors" active-class="!text-green-400">Partidas</router-link>
          </div>

          <!-- User menu (desktop) -->
          <div class="hidden md:flex items-center gap-3 relative">
            <button @click="showMenu = !showMenu" class="flex items-center gap-2 text-gray-300 hover:text-white transition">
              <span class="w-9 h-9 bg-green-500 rounded-full flex items-center justify-center text-sm font-bold text-white">
                {{ authStore.user?.name?.charAt(0)?.toUpperCase() }}
              </span>
              <span class="text-sm font-medium">{{ authStore.user?.name?.split(' ')[0] }}</span>
              <svg class="w-4 h-4 transition" :class="showMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <div v-if="showMenu" class="absolute right-0 top-12 w-52 bg-slate-800 border border-slate-700 rounded-xl shadow-xl overflow-hidden z-50">
              <div class="px-4 py-3 border-b border-slate-700">
                <p class="text-white font-medium text-sm">{{ authStore.user?.name }}</p>
                <p class="text-gray-400 text-xs truncate">{{ authStore.user?.email }}</p>
              </div>
              <router-link to="/subscription" @click="showMenu = false" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-300 hover:bg-slate-700 hover:text-white transition">
                <span>⭐</span> Assinatura
              </router-link>
              <router-link to="/payments" @click="showMenu = false" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-300 hover:bg-slate-700 hover:text-white transition">
                <span>💳</span> Pagamentos
              </router-link>
              <div class="border-t border-slate-700">
                <button @click="logout" class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm text-red-400 hover:bg-slate-700 transition">
                  <span>🚪</span> Sair
                </button>
              </div>
            </div>
          </div>

          <!-- Mobile hamburger -->
          <button @click="mobileOpen = !mobileOpen" class="md:hidden text-gray-300 hover:text-white p-2">
            <svg v-if="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Mobile nav -->
        <div v-if="mobileOpen" class="md:hidden py-3 border-t border-slate-800 space-y-1">
          <div class="flex items-center gap-3 px-3 py-2 mb-2">
            <span class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-sm font-bold text-white">
              {{ authStore.user?.name?.charAt(0)?.toUpperCase() }}
            </span>
            <div>
              <p class="text-white font-medium text-sm">{{ authStore.user?.name }}</p>
              <p class="text-gray-400 text-xs">{{ authStore.user?.email }}</p>
            </div>
          </div>
          <router-link to="/dashboard" @click="mobileOpen = false" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-slate-800 hover:text-white text-sm font-medium transition">Dashboard</router-link>
          <router-link to="/groups" @click="mobileOpen = false" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-slate-800 hover:text-white text-sm font-medium transition">Grupos</router-link>
          <router-link to="/matches" @click="mobileOpen = false" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-slate-800 hover:text-white text-sm font-medium transition">Partidas</router-link>
          <router-link to="/subscription" @click="mobileOpen = false" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-slate-800 hover:text-white text-sm font-medium transition">Assinatura</router-link>
          <router-link to="/payments" @click="mobileOpen = false" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-slate-800 hover:text-white text-sm font-medium transition">Pagamentos</router-link>
          <button @click="logout" class="w-full text-left block px-3 py-2.5 rounded-lg text-red-400 hover:bg-slate-800 text-sm font-medium transition">Sair</button>
        </div>
      </nav>
    </header>

    <main :class="isAuthPage ? '' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8'">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from './stores/auth'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()
const showMenu = ref(false)
const mobileOpen = ref(false)

const isAuthPage = computed(() => ['/login', '/register'].includes(route.path))

const logout = async () => {
  showMenu.value = false
  mobileOpen.value = false
  await authStore.logout()
  router.push('/login')
}
</script>
