<template>
  <AppLayout>
    <div class="bg-blue-800 px-4 pt-4 pb-6">
      <div class="flex items-center justify-between">
        <div><h1 class="text-white text-lg font-bold">{{ authStore.user?.prenom }} - Encadreur</h1><p class="text-blue-200 text-sm">{{ dateAujourdhui }}</p></div>
        <button @click="$router.push('/notifications')" class="relative text-white text-2xl">🔔
          <span v-if="authStore.notifNonLues>0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ authStore.notifNonLues }}</span>
        </button>
      </div>
      <div class="flex gap-2 mt-4">
        <div v-for="k in kpis" :key="k.label" class="flex-1 bg-white bg-opacity-20 rounded-xl p-3 text-center"><div class="text-white text-2xl font-bold">{{ k.val }}</div><div class="text-blue-200 text-xs">{{ k.label }}</div></div>
      </div>
    </div>
    <div class="px-4 pt-2 pb-24">
      <div v-if="chargement" class="text-center py-10 text-gray-400">Chargement...</div>
      <template v-else>
        <div class="flex items-center justify-between mb-2"><h2 class="font-bold text-gray-800">⚠ Alertes actives</h2><span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ alertes.length }}</span></div>
        <div v-for="a in alertes.slice(0,3)" :key="a.id" class="bg-white rounded-xl shadow-sm p-3 mb-2 flex items-center gap-3" :class="a.niveau==='urgent'?'border-l-4 border-red-500':'border-l-4 border-amber-400'">
          <div class="w-9 h-9 rounded-full flex items-center justify-center" :class="a.niveau==='urgent'?'bg-red-100':'bg-amber-100'"><span class="font-bold text-sm" :class="a.niveau==='urgent'?'text-red-700':'text-amber-700'">{{ (a.enfant?.prenom||'?')[0] }}</span></div>
          <div class="flex-1"><p class="font-semibold text-sm truncate">{{ a.enfant?.prenom }} {{ a.enfant?.nom }}</p><p class="text-xs text-gray-400 truncate">{{ a.type_alerte }}</p></div>
          <span :class="a.niveau==='urgent'?'bg-red-100 text-red-700':'bg-amber-100 text-amber-700'" class="text-xs px-2 py-1 rounded-full font-semibold">{{ a.niveau }}</span>
        </div>
        <div v-if="dashboard.observations_attente > 0" class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-4 flex items-center justify-between">
          <p class="text-amber-800 text-sm font-semibold">{{ dashboard.observations_attente }} signalement(s) à valider</p>
          <button @click="$router.push('/observations/validation')" class="bg-amber-600 text-white text-xs px-3 py-2 rounded-lg font-semibold">Voir</button>
        </div>
        <div class="grid grid-cols-2 gap-3 mt-4">
          <button @click="$router.push('/evaluations/creer')" class="bg-blue-600 text-white rounded-xl p-4 text-center"><div class="text-2xl mb-1">📋</div><div class="text-sm font-semibold">Évaluation</div></button>
          <button @click="$router.push('/observations/validation')" class="bg-amber-600 text-white rounded-xl p-4 text-center"><div class="text-2xl mb-1">📝</div><div class="text-sm font-semibold">Signalements</div></button>
          <button @click="$router.push('/alertes')" class="bg-red-600 text-white rounded-xl p-4 text-center"><div class="text-2xl mb-1">⚠</div><div class="text-sm font-semibold">Alertes</div></button>
          <button @click="$router.push('/seances/creer')" class="bg-violet-600 text-white rounded-xl p-4 text-center"><div class="text-2xl mb-1">📅</div><div class="text-sm font-semibold">Séance</div></button>
          <button @click="$router.push('/enfants')" class="bg-gray-700 text-white rounded-xl p-4 text-center"><div class="text-2xl mb-1">👶</div><div class="text-sm font-semibold">Enfants</div></button>
        </div>
      </template>
    </div>
    <BottomNav :active="0" role="encadreur" />
  </AppLayout>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';
import { useAuthStore } from '@/stores/authStore';
const authStore = useAuthStore();
const dashboard = ref({}); const alertes = ref([]); const chargement = ref(true);
const dateAujourdhui = computed(()=>new Date().toLocaleDateString('fr-FR',{weekday:'long',day:'numeric',month:'long'}));
const kpis = computed(()=>[{val:dashboard.value.mes_enfants||0,label:'Enfants'},{val:dashboard.value.alertes_actives||0,label:'Alertes'},{val:dashboard.value.observations_attente||0,label:'Signalements'}]);
onMounted(async()=>{ try{ const [d,a]=await Promise.all([axios.get('/api/dashboard/encadreur'),axios.get('/api/alertes')]); dashboard.value=d.data; alertes.value=a.data.data||[]; }catch(e){} finally{chargement.value=false;} });
</script>
