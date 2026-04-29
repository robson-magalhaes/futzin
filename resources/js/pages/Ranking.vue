<template>
  <div class="space-y-6">
    <h1 class="text-3xl font-bold text-white">Ranking</h1>

    <div class="bg-slate-800 rounded-lg overflow-hidden">
      <table class="w-full">
        <thead class="bg-slate-700">
          <tr>
            <th class="px-4 py-3 text-left text-gray-300">Posição</th>
            <th class="px-4 py-3 text-left text-gray-300">Jogador</th>
            <th class="px-4 py-3 text-center text-gray-300">Partidas</th>
            <th class="px-4 py-3 text-center text-gray-300">Gols</th>
            <th class="px-4 py-3 text-center text-gray-300">Assistências</th>
            <th class="px-4 py-3 text-center text-gray-300">MVPs</th>
            <th class="px-4 py-3 text-center text-gray-300">Nota Média</th>
            <th class="px-4 py-3 text-center text-gray-300">Pontuação</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ranking in rankings" :key="ranking.id" class="border-t border-slate-700 hover:bg-slate-700">
            <td class="px-4 py-3 font-bold text-yellow-500">{{ ranking.position }}º</td>
            <td class="px-4 py-3 text-white font-medium">{{ ranking.user?.name }}</td>
            <td class="px-4 py-3 text-center text-gray-300">{{ ranking.matches_played }}</td>
            <td class="px-4 py-3 text-center text-gray-300">{{ ranking.goals }}</td>
            <td class="px-4 py-3 text-center text-gray-300">{{ ranking.assists }}</td>
            <td class="px-4 py-3 text-center text-gray-300">{{ ranking.mvp_count }}</td>
            <td class="px-4 py-3 text-center text-gray-300">{{ ranking.average_rating?.toFixed(1) }}</td>
            <td class="px-4 py-3 text-center font-bold text-green-500">{{ ranking.total_score?.toFixed(1) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useRankingStore } from '@/stores/ranking'

const route = useRoute()
const rankingStore = useRankingStore()
const rankings = ref([])

onMounted(async () => {
  rankings.value = await rankingStore.fetchGroupRanking(route.params.groupId)
})
</script>
