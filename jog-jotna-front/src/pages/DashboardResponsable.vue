<template>
  <AppLayout>
    <div class="bg-red-800 px-4 pt-4 pb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-white text-lg font-bold">{{ authStore.user?.prenom }} — Responsable</h1>
          <p class="text-red-200 text-sm">{{ dateAujourdhui }}</p>
        </div>
        <button @click="$router.push('/notifications')" class="relative text-white text-2xl">🔔
          <span v-if="authStore.notifNonLues > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ authStore.notifNonLues }}</span>
        </button>
      </div>
      <div class="grid grid-cols-3 gap-2 mt-4">
        <div v-for="k in kpis" :key="k.label" class="bg-white bg-opacity-20 rounded-xl p-3 text-center">
          <div class="text-white text-2xl font-bold">{{ k.val }}</div>
          <div class="text-red-200 text-xs">{{ k.label }}</div>
        </div>
      </div>
    </div>
    <div class="px-4 pt-4 pb-24">
      <div v-if="chargement" class="text-center py-10 text-gray-400">Chargement...</div>
      <template v-else>
        <h2 class="font-bold text-gray-800 mb-3">Enfants en alerte</h2>
        <div v-for="e in dashboard.enfants_alerte || []" :key="e.id"
          class="bg-white rounded-xl shadow-sm p-3 mb-2 flex items-center gap-3 border-l-4 border-red-500">
          <div class="flex-1">
            <p class="font-semibold text-sm">{{ e.nom }}</p>
            <p class="text-xs text-gray-400">{{ e.age }} ans — {{ e.alerte }}</p>
          </div>
          <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-semibold">{{ e.niveau }}</span>
        </div>
        <div class="grid grid-cols-2 gap-3 mt-4">
          <button @click="$router.push('/rapports')" class="bg-red-700 text-white rounded-xl p-4 text-center">
            <div class="text-2xl mb-1">📊</div><div class="text-sm font-semibold">Rapports</div>
          </button>
          <button @click="$router.push('/alertes')" class="bg-amber-600 text-white rounded-xl p-4 text-center">
            <div class="text-2xl mb-1">⚠</div><div class="text-sm font-semibold">Alertes</div>
          </button>
          <button v-if="authStore.user?.role === 'admin'" @click="$router.push('/admin/utilisateurs')"
            class="bg-violet-700 text-white rounded-xl p-4 text-center col-span-2">
            <div class="text-2xl mb-1">👥</div><div class="text-sm font-semibold">Gestion des comptes</div>
          </button>
        </div>
      </template>
    </div>
    <BottomNav :active="0" :role="authStore.user?.role === 'admin' ? 'admin' : 'responsable'" />
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/BottomNav.vue';
import { useAuthStore } from '@/stores/authStore';

const authStore = useAuthStore();
const dashboard = ref({});
const chargement = ref(true);
const dateAujourdhui = computed(() => new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' }));
const kpis = computed(() => [
  { val: dashboard.value.total_enfants || 0, label: 'Enfants' },
  { val: dashboard.value.alertes_actives || 0, label: 'Alertes' },
  { val: dashboard.value.observations_attente || 0, label: 'Signalements' },
]);

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/dashboard/responsable');
    dashboard.value = data;
  } catch {}
  finally { chargement.value = false; }
});
</script>
