import './bootstrap'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'

import Login from './pages/Login.vue'
import Register from './pages/Register.vue'
import Dashboard from './pages/Dashboard.vue'
import Groups from './pages/Groups.vue'
import GroupDetail from './pages/GroupDetail.vue'
import Matches from './pages/Matches.vue'
import MatchDetail from './pages/MatchDetail.vue'
import Ranking from './pages/Ranking.vue'
import Subscription from './pages/Subscription.vue'
import PaymentHistory from './pages/PaymentHistory.vue'

import { useAuthStore } from './stores/auth'

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/login', component: Login, meta: { guest: true } },
  { path: '/register', component: Register, meta: { guest: true } },
  { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/groups', component: Groups, meta: { requiresAuth: true } },
  { path: '/groups/:id', component: GroupDetail, meta: { requiresAuth: true } },
  { path: '/matches', component: Matches, meta: { requiresAuth: true } },
  { path: '/matches/:id', component: MatchDetail, meta: { requiresAuth: true } },
  { path: '/ranking/:groupId', component: Ranking, meta: { requiresAuth: true } },
  { path: '/subscription', component: Subscription, meta: { requiresAuth: true } },
  { path: '/payments', component: PaymentHistory, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  await authStore.initializeAuth()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
})

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')
