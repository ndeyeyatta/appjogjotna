<template>
  <AppLayout>
    <div class="bg-teal-700 px-4 pt-4 pb-6">
      <div class="flex items-center justify-between">
        <div><h1 class="text-white text-lg font-bold">Bonjour, {{ authStore.user?.prenom }} 👋</h1><p class="text-teal-200 text-sm">{{ dateAujourdhui }}</p></div>
        <button @click="$router.push('/notifications')" class="relative text-white text-2xl">🔔
          <span v-if="authStore.notifNonLues>0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ authStore.notifNonLues }}</span>
        </button>
      </div>
    </div>
    <div class="px-4 pt-2 pb-24">
      <div v-if="chargement" class="text-center py-10 text-gray-400">Chargement...</div>
      <template v-else>
        <div v-if="dashboard.prochaine_seance" class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4 flex items-center gap-3">
          <span class="text-2xl">📅</span>
          <div><p class="text-blue-800 text-sm font-semibold">Prochaine séance</p><p class="text-blue-600 text-xs">{{ formatDate(dashboard.prochaine_seance.date_seance) }} — {{ dashboard.prochaine_seance.heure }}</p></div>
        </div>
        <div class="flex items-center justify-between mb-3"><h2 class="text-gray-800 font-bold">Mes enfants</h2><span class="text-gray-400 text-sm">{{ dashboard.enfants?.length||0 }}</span></div>
        <div v-for="e in dashboard.enfants" :key="e.id" class="bg-white rounded-xl shadow-sm p-4 mb-3">
          <div class="flex items-start gap-3">
            <div class="w-14 h-14 rounded-full bg-teal-100 border-2 border-teal-500 flex items-center justify-center flex-shrink-0"><span class="text-teal-700 font-bold text-lg">{{ (e.prenom[0]+e.nom[0]).toUpperCase() }}</span></div>
            <div class="flex-1">
              <div class="flex items-center gap-2"><h3 class="font-bold text-gray-800">{{ e.prenom }} {{ e.nom }}</h3><span :class="e.alertes?.length>0?'bg-amber-100 text-amber-700':'bg-green-100 text-green-700'" class="text-xs px-2 py-0.5 rounded-full">{{ e.alertes?.length>0?'⚠ Alerte':'✓ Normal' }}</span></div>
              <p class="text-gray-400 text-xs">{{ e.age_ans }} — {{ e.village }}</p>
              <div class="flex gap-2 mt-2">
                <button @click="$router.push(`/enfants/${e.id}`)" class="flex-1 bg-blue-600 text-white text-xs py-2 rounded-lg font-semibold">Voir le suivi</button>
                <button @click="$router.push(`/observations/creer?enfant=${e.id}`)" class="px-3 py-2 bg-teal-50 border border-teal-300 text-teal-700 text-xs rounded-lg font-semibold">✏ Signaler</button>
              </div>
            </div>
          </div>
        </div>
        <button @click="$router.push('/observations/creer')" class="w-full bg-teal-700 text-white font-semibold py-4 rounded-xl mb-4 flex items-center justify-center gap-2"><span class="text-lg">✏</span><span>Signaler une observation</span></button>
        <div v-if="!dashboard.enfants?.length" class="text-center text-gray-400 py-8"><div class="text-4xl mb-2">👶</div><p>Aucun enfant inscrit</p></div>
      </template>
    </div>
    <BottomNav :active="0" role="parent" />
  </AppLayout>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';
import { useAuthStore } from '@/stores/authStore';
const authStore = useAuthStore();
const dashboard = ref({ enfants:[], prochaine_seance:null });
const chargement = ref(true);
const dateAujourdhui = computed(() => new Date().toLocaleDateString('fr-FR',{weekday:'long',day:'numeric',month:'long'}));
onMounted(async () => { try { const {data}=await axios.get('/api/dashboard/parent'); dashboard.value=data; } catch(e){} finally{chargement.value=false;} });
function formatDate(d){ return d?new Date(d).toLocaleDateString('fr-FR'):'—'; }
</script>
