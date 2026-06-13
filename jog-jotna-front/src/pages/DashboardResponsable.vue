<template>
  <AppLayout>
    <div class="bg-red-800 px-4 pt-4 pb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-white text-lg font-bold">{{ authStore.user?.prenom }} - Responsable</h1>
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
        <div v-if="dashboard.seances_semaine != null" class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4 flex items-center gap-3">
          <span class="text-2xl">📅</span>
          <p class="text-blue-800 text-sm"><strong>{{ dashboard.seances_semaine }}</strong> séance(s) planifiée(s) cette semaine</p>
        </div>

        <h2 class="font-bold text-gray-800 mb-3">Enfants en alerte</h2>
        <div v-for="e in dashboard.enfants_alerte || []" :key="e.id"
          class="bg-white rounded-xl shadow-sm p-3 mb-2 flex items-center gap-3 border-l-4 border-red-500">
          <div class="flex-1">
            <p class="font-semibold text-sm">{{ e.nom }}</p>
            <p class="text-xs text-gray-400">{{ e.age }} - {{ e.alerte }}</p>
          </div>
          <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-semibold">{{ e.niveau }}</span>
        </div>
        <p v-if="!(dashboard.enfants_alerte || []).length" class="text-gray-400 text-sm mb-4">Aucun enfant en alerte urgente.</p>

        <h2 class="font-bold text-gray-800 mb-3 mt-4">Répartition par village</h2>
        <div v-for="v in dashboard.par_village || []" :key="v.village"
          class="bg-white rounded-xl shadow-sm p-3 mb-2 flex justify-between items-center">
          <div>
            <p class="font-semibold text-sm">{{ v.village }}</p>
            <p class="text-xs text-gray-400">{{ v.total }} enfant(s)</p>
          </div>
          <span class="text-xs px-2 py-1 rounded-full" :class="v.alertes > 0 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700'">
            {{ v.alertes }} alerte(s)
          </span>
        </div>

        <h2 class="font-bold text-gray-800 mb-3 mt-4">Tendance nutritionnelle (6 mois)</h2>
        <div class="bg-white rounded-xl shadow-sm p-3 mb-4 overflow-x-auto">
          <div class="flex gap-2 min-w-max">
            <div v-for="t in dashboard.tendance_nutri || []" :key="t.mois" class="text-center px-2">
              <p class="text-xs text-gray-500 mb-1">{{ t.mois }}</p>
              <div class="flex flex-col gap-0.5">
                <span class="text-xs text-green-600">✓ {{ t.normaux }}</span>
                <span class="text-xs text-amber-600">⚠ {{ t.moderes }}</span>
                <span class="text-xs text-red-600">⛔ {{ t.severes }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3 mt-4">
          <button @click="$router.push('/rapports')" class="bg-red-700 text-white rounded-xl p-4 text-center">
            <div class="text-2xl mb-1">📊</div><div class="text-sm font-semibold">Rapports</div>
          </button>
          <button @click="$router.push('/alertes')" class="bg-amber-600 text-white rounded-xl p-4 text-center">
            <div class="text-2xl mb-1">⚠</div><div class="text-sm font-semibold">Alertes</div>
          </button>
          <button v-if="authStore.user?.role === 'admin'" @click="$router.push('/admin/utilisateurs')"
            class="bg-violet-700 text-white rounded-xl p-4 text-center">
            <div class="text-2xl mb-1">👥</div><div class="text-sm font-semibold">Comptes</div>
          </button>
          <button v-if="authStore.user?.role === 'admin'" @click="$router.push('/admin/config')"
            class="bg-violet-600 text-white rounded-xl p-4 text-center">
            <div class="text-2xl mb-1">⚙</div><div class="text-sm font-semibold">Config</div>
          </button>
          <button v-if="authStore.user?.role === 'admin'" @click="$router.push('/admin/sauvegarde')"
            class="bg-violet-800 text-white rounded-xl p-4 text-center col-span-2">
            <div class="text-2xl mb-1">💾</div><div class="text-sm font-semibold">Sauvegarde</div>
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
  { val: dashboard.value.alertes_urgentes || 0, label: 'Urgentes' },
]);

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/dashboard/responsable');
    dashboard.value = data;
  } catch {}
  finally { chargement.value = false; }
});
</script>
